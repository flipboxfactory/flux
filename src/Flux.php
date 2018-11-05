<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/flux/license
 * @link       https://www.flipboxfactory.com/software/flux/
 */

namespace flipbox\flux;

use craft\base\Plugin;
use craft\events\RegisterUrlRulesEvent;
use craft\web\twig\variables\CraftVariable;
use craft\web\UrlManager;
use flipbox\ember\modules\LoggerTrait;
use flipbox\flux\events\RegisterScopesEvent;
use yii\base\Event;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 *
 * @property cp\Cp $cp
 * @property services\Transformers $transformers
 */
class Flux extends Plugin
{
    use LoggerTrait;

    /**
     * The global scope
     */
    const EVENT_REGISTER_SCOPES = 'registerScopes';

    /**
     * The global scope
     */
    const GLOBAL_SCOPE = 'global';

    /**
     * @var array
     */
    private $scopes;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        // Modules
        $this->setModules([
            'cp' => cp\Cp::class,
        ]);

        // Components
        $this->setComponents([
            'transformers' => services\Transformers::class,
        ]);

        // Twig variables
        Event::on(
            CraftVariable::class,
            CraftVariable::EVENT_INIT,
            function (Event $event) {
                /** @var CraftVariable $variable */
                $variable = $event->sender;
                $variable->set('flux', static::getInstance());
            }
        );

        // CP routes
        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_CP_URL_RULES,
            [self::class, 'onRegisterCpUrlRules']
        );
    }

    /**
     * @inheritdoc
     */
    protected static function getLogFileName(): string
    {
        return 'flux';
    }

    /**
     * Register scopes
     * @return array
     */
    public function getScopes(): array
    {
        if ($this->scopes === null) {
            $event = new RegisterScopesEvent([
                'scopes' => [
                    self::GLOBAL_SCOPE
                ]
            ]);

            $this->trigger(
                self::EVENT_REGISTER_SCOPES,
                $event
            );

            $this->scopes = $event->scopes;
        }

        return $this->scopes;
    }

    /**
     * @param string $scope
     * @return bool
     */
    public function isValidScope(string $scope): bool
    {
        return in_array($scope, $this->getScopes(), true);
    }

    /*******************************************
     * EVENTS
     *******************************************/

    /**
     * @param RegisterUrlRulesEvent $event
     */
    public static function onRegisterCpUrlRules(RegisterUrlRulesEvent $event)
    {
        $event->rules = array_merge(
            $event->rules,
            [
                // FLUX
                'flux' => 'flux/cp/view/flux/index',
            ]
        );
    }

    /*******************************************
     * SERVICES
     *******************************************/

    /**
     * @noinspection PhpDocMissingThrowsInspection
     * @return services\Transformers
     */
    public function getTransformers(): services\Transformers
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->get('transformers');
    }
}
