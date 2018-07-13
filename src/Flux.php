<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/flux/license
 * @link       https://www.flipboxfactory.com/software/flux/
 */

namespace flipbox\flux;

use craft\base\Plugin;
use craft\web\twig\variables\CraftVariable;
use flipbox\ember\modules\LoggerTrait;
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
    const GLOBAL_SCOPE = 'global';

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
