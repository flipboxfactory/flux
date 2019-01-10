<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/flux/license
 * @link       https://www.flipboxfactory.com/software/flux/
 */

namespace flipbox\flux\services;

use craft\helpers\ArrayHelper;
use craft\helpers\Json;
use flipbox\flux\events\RegisterTransformersEvent;
use flipbox\flux\exceptions\TransformerNotFoundException;
use flipbox\flux\Flux;
use flipbox\flux\helpers\TransformerHelper;
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
     * @event Event an event that is triggered when the class is initialized via [[init()]].
     */
    const EVENT_INIT = 'init';

    /**
     * Transformers that have previously been loaded
     *
     * @var array
     */
    private $transformers;

    /**
     * The transformers that Event triggers have been executed
     *
     * @var array
     */
    private $processed = [];

    /**
     * Initializes the object.
     * This method is called at the end of the constructor. The default implementation will trigger
     * an [[EVENT_INIT]] event. If you override this method, make sure you call the parent implementation at the end
     * to ensure triggering of the event.
     */
    public function init()
    {
        parent::init();
        $this->trigger(self::EVENT_INIT);
    }

    /**
     * @param $transformer
     * @param string $scope
     * @param string|null $class
     * @param callable|null $default
     * @return callable|null
     */
    public function resolve(
        $transformer,
        string $scope = Flux::GLOBAL_SCOPE,
        string $class = null,
        $default = null
    ) {
        if (null !== ($callable = TransformerHelper::resolve($transformer))) {
            return $callable;
        }

        if (is_string($transformer)) {
            return $this->find($transformer, $scope, $class, $default);
        }

        return null;
    }

    /**
     * @param string $identifier
     * @param string $scope
     * @param string|null $class
     * @param null $default
     * @return callable
     * @throws TransformerNotFoundException
     */
    public function get(
        string $identifier,
        string $scope = Flux::GLOBAL_SCOPE,
        string $class = null,
        $default = null
    ): callable {
        if (null === ($transformer = $this->find($identifier, $scope, $class, $default))) {
            throw new TransformerNotFoundException(sprintf(
                "Unable to find transformer with the following criteria: %s",
                Json::encode([
                    'identifier' => $identifier,
                    'scope' => $scope,
                    'class' => $class
                ])
            ));
        }

        return $transformer;
    }

    /**
     * @param string $identifier
     * @param string $scope
     * @param string|null $class
     * @param callable|null $default
     * @return callable|null
     */
    public function find(
        string $identifier,
        string $scope = Flux::GLOBAL_SCOPE,
        string $class = null,
        $default = null
    ) {
        if (!Flux::getInstance()->isValidScope($scope)) {
            return null;
        }

        $transformersByScopeAndClass = $this->resolveAllByScopeAndClass($scope, $class);

        if (null === ($transformer = $transformersByScopeAndClass[$identifier] ?? $default)) {
            return null;
        }

        return TransformerHelper::resolve($transformer);
    }


    /**
     * @param string $scope
     * @param string|null $class
     * @return array
     */
    public function resolveAllByScopeAndClass(
        string $scope = Flux::GLOBAL_SCOPE,
        string $class = null
    ): array {

        // Default class
        $class = $class ?: Flux::class;

        if (($this->processed[$scope][$class] ?? false) !== true) {
            $this->processed[$scope][$class] = true;

            $this->ensureTransformerConfigs();

            $event = new RegisterTransformersEvent([
                'transformers' => $this->transformers[$scope][$class] ?? []
            ]);

            Event::trigger(
                $class,
                $event::eventName($scope),
                $event
            );

            $this->transformers[$scope][$class] = $event->transformers;
        }

        return $this->transformers[$scope][$class];
    }

    /**
     * Ensure the db transformers are loaded
     */
    protected function ensureTransformerConfigs()
    {
        if ($this->transformers === null) {
            $this->transformers = $this->dbTransformers();
        }
    }

    /**
     * @return array
     */
    protected function dbTransformers(): array
    {
        $query = (new Query())
            ->select(['handle', 'scope', 'class', 'config'])
            ->from([Transformer::tableName()]);

        $configs = [];

        foreach ($query->all() as $result) {
            $configs[$result['scope']][$result['class']][$result['handle']] = $this->prepareConfig([$result['config']]);
        }

        return $configs;
    }

    /**
     * @param array $config
     * @return array
     */
    protected function prepareConfig(array $config = []): array
    {
        if (null !== ($settings = ArrayHelper::remove($config, 'config'))) {
            if (is_string($settings)) {
                $settings = Json::decodeIfJson($settings);
            }

            $config = array_merge(
                $config,
                $settings
            );
        }

        return $config;
    }
}
