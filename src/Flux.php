<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/flux/license
 * @link       https://www.flipboxfactory.com/software/flux/
 */

namespace flipbox\flux;

use craft\base\Plugin;
use craft\web\twig\variables\CraftVariable;
use flipbox\flux\events\RegisterScopesEvent;
use flipbox\craft\ember\modules\LoggerTrait;
use yii\base\Event;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 *
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
