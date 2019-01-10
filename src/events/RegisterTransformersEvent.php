<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/flux/license
 * @link       https://www.flipboxfactory.com/software/flux/
 */

namespace flipbox\flux\events;

use flipbox\flux\Flux;
use yii\base\Event;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class RegisterTransformersEvent extends Event
{
    /**
     * @event Event an event that is triggered when the transformers are registered.
     */
    const REGISTER_TRANSFORMERS = 'registerTransformer';

    /**
     * @param string $scope
     * @return string
     */
    public static function eventName(string $scope = Flux::GLOBAL_SCOPE)
    {
        return self::REGISTER_TRANSFORMERS . ':' . $scope;
    }

    /**
     * The transformer which will be used to alter data.  Keep in mind that multiple listeners may be registered on this
     * event and therefore can alter this value before/after.
     *
     * The returning array should be formatted as ['identifier' => transformer']
     *
     *
     * @var array
     */
    public $transformers = [];
}
