<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/flux/license
 * @link       https://www.flipboxfactory.com/software/flux/
 */

namespace flipbox\flux\helpers;

use Craft;
use craft\helpers\ArrayHelper;
use craft\helpers\StringHelper;
use flipbox\flux\Flux;
use Flipbox\Transform\Helpers\TransformerHelper as BaseTransformerHelper;
use Flipbox\Transform\Transformers\TransformerInterface;
use yii\base\InvalidConfigException;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class TransformerHelper extends BaseTransformerHelper
{
    /**
     * @param $name
     * @param bool $lowercase
     * @return string
     */
    public static function eventName($name, $lowercase = true)
    {
        if (!is_array($name)) {
            $name = ArrayHelper::toArray($name);
        }

        // Clean
        $name = array_filter($name);

        // Lowercase
        if ($lowercase === true) {
            $name = array_map('strtolower', $name);
        }

        return StringHelper::toString($name, ':');
    }

    /**
     * @param $transformer
     * @return bool
     */
    public static function isTransformerConfig($transformer)
    {
        if (!is_array($transformer)) {
            return false;
        }

        if ($class = ArrayHelper::getValue($transformer, 'class')) {
            return false;
        }

        return static::isTransformerClass($class);
    }

    /**
     * @param $transformer
     * @return null|callable|TransformerInterface
     */
    public static function resolve($transformer)
    {
        if (null !== ($callable = parent::resolve($transformer))) {
            return $callable;
        }

        try {
            if (static::isTransformerConfig($transformer)) {
                return static::resolve(
                    Craft::createObject($transformer)
                );
            }
        } catch (InvalidConfigException $e) {
            Flux::warning("Invalid transformer configuration.");
        }

        return null;
    }
}
