<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/flux/license
 * @link       https://www.flipboxfactory.com/software/flux/
 */

namespace flipbox\flux;

use Craft;
use craft\base\Plugin;
use craft\helpers\Json;
use craft\web\twig\variables\CraftVariable;
use Flipbox\Transform\Factory;
use yii\base\Event;
use yii\log\Logger;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class Flux extends Plugin
{
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

    /*******************************************
     * SERVICES
     *******************************************/

    /**
     * @return services\Transformers
     */
    public function getTransformers()
    {
        return $this->get('transformers');
    }

    /*******************************************
     * TRANSFORM
     *******************************************/

    /**
     * @param $data
     * @param string $transformer
     * @param string $scope
     * @param mixed $default
     * @param array $config
     * @return mixed
     */
    public function item(
        $transformer,
        $data,
        string $scope = 'global',
        array $config = [],
        $default = null
    ) {
        $transformer = $this->getTransformers()->resolve($transformer, $scope);
        if ($transformer === null) {
            static::warning(sprintf(
                "Unable to transform item because the transformer '%s' could not be resolved.",
                (string)Json::encode($transformer)
            ));
            return $default;
        }

        return Factory::item(
            $transformer,
            $data,
            $config
        );
    }

    /**
     * @param $data
     * @param string $transformer
     * @param string $scope
     * @param mixed $default
     * @param array $config
     * @return mixed
     */
    public function collection(
        $transformer,
        $data,
        string $scope = 'global',
        array $config = [],
        $default = []
    ) {
        $transformer = $this->getTransformers()->resolve($transformer, $scope);
        if ($transformer === null) {
            static::warning(sprintf(
                "Unable to transform collection because the transformer '%s' could not be resolved.",
                (string)Json::encode($transformer)
            ));
            return $default;
        }

        return Factory::collection(
            $transformer,
            $data,
            $config
        );
    }

    /*******************************************
     * LOGGING
     *******************************************/

    /**
     * Logs a trace message.
     * Trace messages are logged mainly for development purpose to see
     * the execution work flow of some code.
     * @param string $message the message to be logged.
     * @param string $category the category of the message.
     */
    public static function trace($message, string $category = null)
    {
        Craft::getLogger()->log($message, Logger::LEVEL_TRACE, self::normalizeCategory($category));
    }

    /**
     * Logs an error message.
     * An error message is typically logged when an unrecoverable error occurs
     * during the execution of an application.
     * @param string $message the message to be logged.
     * @param string $category the category of the message.
     */
    public static function error($message, string $category = null)
    {
        Craft::getLogger()->log($message, Logger::LEVEL_ERROR, self::normalizeCategory($category));
    }

    /**
     * Logs a warning message.
     * A warning message is typically logged when an error occurs while the execution
     * can still continue.
     * @param string $message the message to be logged.
     * @param string $category the category of the message.
     */
    public static function warning($message, string $category = null)
    {
        Craft::getLogger()->log($message, Logger::LEVEL_WARNING, self::normalizeCategory($category));
    }

    /**
     * Logs an informative message.
     * An informative message is typically logged by an application to keep record of
     * something important (e.g. an administrator logs in).
     * @param string $message the message to be logged.
     * @param string $category the category of the message.
     */
    public static function info($message, string $category = null)
    {
        Craft::getLogger()->log($message, Logger::LEVEL_INFO, self::normalizeCategory($category));
    }

    /**
     * @param string|null $category
     * @return string
     */
    private static function normalizeCategory(string $category = null)
    {
        $normalizedCategory = 'Flux';

        if ($category === null) {
            return $normalizedCategory;
        }

        return $normalizedCategory . ': ' . $category;
    }
}
