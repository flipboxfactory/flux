<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/flux/license
 * @link       https://www.flipboxfactory.com/software/flux/
 */

namespace flipbox\flux\cp\services;

use craft\helpers\StringHelper;
use flipbox\flux\Flux;
use flipbox\flux\records\Transformer;
use yii\base\Component;
use yii\base\Event;
use yii\db\Query;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class Transformers extends Component
{
    /**
     *
     */
    const EVENT_REGISTER_TRANSFORMERS = \flipbox\flux\services\Transformers::EVENT_REGISTER_TRANSFORMERS;


    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        // Init the transformer to ensure events are triggered
        Flux::getInstance()->getTransformers();
    }

    /**
     * Returns an array of classes that have been registered for a particular scope.
     * @param string $scope
     * @return array
     */
    public function findAllClassesByScope(string $scope): array
    {
        if (!Flux::getInstance()->isValidScope($scope)) {
            return [];
        }

        $classes = (new Query())
            ->select(['class'])
            ->from([Transformer::tableName()])
            ->andWhere([
                'scope' => $scope
            ])
            ->column();

        foreach ($this->eventReflection() as $name => $event) {
            $events = $this->scopeEvents($name, $event);

            // Are there events AND is it the scope we're looking for
            if (empty($events) || !array_key_exists($scope, $events)) {
                continue;
            }

            $classes = array_merge(
                $classes,
                array_keys($events[$scope])
            );
        }

        return $classes;
    }

    /**
     * Returns an array of classes that have been registered for a particular scope.
     * @param string $scope
     * @return array
     */
    public function findAllHandlesByScopeAndClass(string $scope, string $class): array
    {
        if (!Flux::getInstance()->isValidScope($scope)) {
            return [];
        }

        $transformers = Flux::getInstance()->getTransformers()->resolveAllByScopeAndClass(
            $scope,
            $class
        );

        return array_keys($transformers);
    }


    /**
     * Get all registered events
     *
     * @return array
     */
    protected function eventReflection(): array
    {
        try {
            $reflection = new \ReflectionClass(Event::class);

            $eventsProperty = $reflection->getProperty('_events');
            $eventsProperty->setAccessible(true);

            return $eventsProperty->getValue();

        } catch (\ReflectionException $exception) {
            Flux::warning(
                "Unabel to resolve events.",
                __METHOD__
            );
        }

        return [];
    }

    /**
     * @param $name
     * @param $classEvents
     * @return array
     */
    protected function scopeEvents($name, $classEvents): array
    {
        if (!StringHelper::matchWildcard(self::EVENT_REGISTER_TRANSFORMERS . ':*', $name)) {
            return [];
        }

        $scope = StringHelper::removeLeft($name, self::EVENT_REGISTER_TRANSFORMERS . ':');

        // The scope must be valid
        if (!Flux::getInstance()->isValidScope($scope)) {
            return [];
        }

        return [$scope => $classEvents];
    }
}
