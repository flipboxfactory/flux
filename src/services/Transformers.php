<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/flux/license
 * @link       https://www.flipboxfactory.com/software/flux/
 */

namespace flipbox\flux\services;

use craft\helpers\ArrayHelper;
use craft\helpers\Json;
use flipbox\ember\exceptions\ObjectNotFoundException;
use flipbox\ember\helpers\QueryHelper;
use flipbox\ember\services\traits\objects\Accessor;
use flipbox\flux\db\TransformerQuery;
use flipbox\flux\events\RegisterTransformerEvent;
use flipbox\flux\Flux;
use flipbox\flux\helpers\TransformerHelper;
use Flipbox\Transform\Transformers\TransformerInterface;
use yii\base\Component;
use yii\base\Event;
use yii\db\QueryInterface;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class Transformers extends Component
{
    use Accessor;

    /**
     * @event Event an event that is triggered when the query is initialized via [[init()]].
     */
    const EVENT_INIT = 'init';

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
     * @inheritdoc
     */
    public static function objectClass()
    {
        return null;
    }

    /**
     * @inheritdoc
     */
    public function getQuery($config = []): QueryInterface
    {
        $query = new TransformerQuery();

        if (!empty($config)) {
            QueryHelper::configure(
                $query,
                $config
            );
        }

        return $query;
    }

    /**
     * @param array $config
     * @return array
     */
    protected function prepareConfig(array $config = []): array
    {
        if (null !== $settings = ArrayHelper::remove($config, 'config')) {
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

    /**
     * @param $transformer
     * @param string $scope
     * @param string|null $class
     * @return callable|TransformerInterface|null
     */
    public function resolve(
        $transformer,
        string $scope = Flux::GLOBAL_SCOPE,
        string $class = null
    ) {
        if (null !== ($callable = TransformerHelper::resolve($transformer))) {
            return $callable;
        }

        if (is_string($transformer)) {
            return $this->find($transformer, $scope, $class);
        }

        return null;
    }

    /**
     * @param string $identifier
     * @param string $scope
     * @param string|null $class
     * @return callable|TransformerInterface
     * @throws ObjectNotFoundException
     */
    public function get(
        string $identifier,
        string $scope = Flux::GLOBAL_SCOPE,
        string $class = null
    ) {
        if (null === ($transformer = $this->find($identifier, $scope, $class))) {
            $this->notFoundException();
        }

        return $transformer;
    }

    /**
     * @param string $identifier
     * @param string $scope
     * @param string|null $class
     * @return callable|TransformerInterface|null
     */
    public function find(
        string $identifier,
        string $scope = Flux::GLOBAL_SCOPE,
        string $class = null
    ) {
        $identifierKey = is_numeric($identifier) ? 'id' : 'handle';

        $condition = [
            $identifierKey => $identifier,
            'scope' => $scope
        ];

        if($class !== null) {
            $condition['class'] = $class;
        }

        return $this->triggerEvent(
            $identifier,
            $scope,
            $class,
            $this->findByCondition($condition)
        );
    }

    /**
     * @param string $identifier
     * @param string $scope
     * @param string|null $class
     * @param callable|TransformerInterface|null $transformer
     * @return callable|TransformerInterface|null
     */
    private function triggerEvent(string $identifier, string $scope, string $class = null, $transformer = null)
    {
        if($class === null) {
            $class = get_class($this);
        }

        $event = new RegisterTransformerEvent([
            'scope' => $scope,
            'transformer' => $transformer
        ]);

        Event::trigger(
            $class,
            $identifier,
            $event
        );

        return $event->transformer;
    }
}
