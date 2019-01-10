<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/flux/license
 * @link       https://www.flipboxfactory.com/software/flux/
 */

namespace flipbox\flux\exceptions;

use yii\base\Exception;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 2.0.0
 */
class TransformerNotFoundException extends Exception
{
    /**
     * @return string
     */
    public function getName()
    {
        return 'Transformer Not Found';
    }
}
