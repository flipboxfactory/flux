<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/flux/license
 * @link       https://www.flipboxfactory.com/software/flux/
 */

namespace flipbox\flux\events;

use Flipbox\Transform\Transformers\TransformerInterface;
use yii\base\Event;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class RegisterTransformerEvent extends Event
{
    /**
     * The transform scope; allowing specific targeting for distinct usages.  A plugin may have it's own scope
     * which allows one to register a transformer against it specifically.  For example, to transform a `User` element
     * one may register transformers for: data output, CRM API usage, Email Marketing API usage, etc.  Each of these
     * may provide their own scope, so a transformer can be distinctly registered against it.
     *
     * @var string
     */
    public $scope;

    /**
     * The transformer which will be used to alter data.  Keep in mind that multiple listeners may be registered on this
     * event and therefore can alter this value before/after.
     * @var callable|TransformerInterface|null
     */
    public $transformer;
}
