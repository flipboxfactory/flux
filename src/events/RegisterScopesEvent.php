<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/flux/license
 * @link       https://www.flipboxfactory.com/software/flux/
 */

namespace flipbox\flux\events;

use yii\base\Event;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class RegisterScopesEvent extends Event
{
    /**
     * The transformer which will be used to alter data.  Keep in mind that multiple listeners may be registered on this
     * event and therefore can alter this value before/after.
     * @var array
     */
    public $scopes = [];
}
