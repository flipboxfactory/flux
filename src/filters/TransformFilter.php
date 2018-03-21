<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/flux/license
 * @link       https://www.flipboxfactory.com/software/flux/
 */

namespace flipbox\flux\filters;

use Craft;
use flipbox\flux\Flux;
use Flipbox\Transform\Transformers\TransformerInterface;
use yii\base\ActionFilter;
use yii\data\DataProviderInterface;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class TransformFilter extends ActionFilter
{
    /**
     * The default data transformer.  If a transformer cannot be resolved via an action mapping,
     * this transformer will be used.
     *
     * @var string|callable|TransformerInterface
     */
    public $transformer;

    /**
     * @var array this property defines the transformers for each action.
     * Each action that should only support one transformer.
     *
     * You can use `'*'` to stand for all actions. When an action is explicitly
     * specified, it takes precedence over the specification given by `'*'`.
     *
     * For example,
     *
     * ```php
     * [
     *   'create' => SomeClass::class,
     *   'update' => 'transformerHandle',
     *   'delete' => function() { return ['foo' => 'bar'] },
     *   '*' => SomeOtherClass::class,
     * ]
     * ```
     */
    public $actions = [];

    /**
     * @var string
     */
    public $fieldsParam = 'fields';

    /**
     * @var string
     */
    public $includesParam = 'includes';

    /**
     * @var string
     */
    public $excludesParam = 'excludes';

    /**
     * @var string the name of the envelope (e.g. `items`) for returning the resource objects in a collection.
     * This is used when serving a resource collection. When this is set and pagination is enabled, the serializer
     * will return a collection in the following format:
     *
     * ```php
     * [
     *     'data' => [...],  // assuming collectionEnvelope is "data"
     * ]
     * ```
     *
     * If this property is not set, the resource arrays will be directly returned without using envelope.
     * The pagination information as shown in `_links` and `_meta` can be accessed from the response HTTP headers.
     */
    public $collectionEnvelope = 'data';

    /**
     * The scope used to resolve a transformer
     *
     * @var string
     */
    public $scope = Flux::GLOBAL_SCOPE;

    /**
     * @param \yii\base\Action $action
     * @param mixed $result
     * @return array|mixed|null|DataProviderInterface
     */
    public function afterAction($action, $result)
    {
        return $this->transform($result);
    }

    /**
     * @param $data
     * @return array|null|DataProviderInterface
     */
    protected function transform($data)
    {
        if ($data instanceof DataProviderInterface) {
            return $this->transformDataProvider($data);
        }

        return $this->transformData($data);
    }

    /**
     * @param $data
     * @return array|null
     */
    protected function transformData($data)
    {
        if (Craft::$app->getRequest()->getIsHead()) {
            return null;
        } else {
            return Flux::getInstance()->item(
                $this->transformer(),
                $data,
                $this->scope,
                $this->getTransformConfig(),
                $data
            );
        }
    }

    /**
     * Serializes a data provider.
     *
     * @param DataProviderInterface $dataProvider
     * @return array|DataProviderInterface
     */
    protected function transformDataProvider(DataProviderInterface $dataProvider)
    {
        if (Craft::$app->getRequest()->getIsHead()) {
            return null;
        } else {
            $data = Flux::getInstance()->collection(
                $this->transformer(),
                $dataProvider->getModels(),
                $this->scope,
                $this->getTransformConfig(),
                $dataProvider
            );

            if ($this->collectionEnvelope === null) {
                return $data;
            } else {
                return [
                    $this->collectionEnvelope => $data,
                ];
            }
        }
    }

    /**
     * @return callable|TransformerInterface|null
     */
    protected function transformer()
    {
        // The requested action
        $action = Craft::$app->requestedAction->id;

        // Default transformer
        $transformer = $this->transformer;

        // Look for definitions
        if (isset($this->actions[$action])) {
            $transformer = $this->actions[$action];
        } elseif (isset($this->actions['*'])) {
            $transformer = $this->actions['*'];
        }

        if (null === $transformer) {
            return null;
        }

        return $transformer;
    }

    /**
     * @return array
     */
    protected function getTransformConfig(): array
    {
        return [
            'includes' => $this->getRequestedIncludes(),
            'excludes' => $this->getRequestedExcludes(),
            'fields' => $this->getRequestedFields()
        ];
    }

    /**
     * @return array
     */
    protected function getRequestedFields(): array
    {
        return $this->normalizeRequest(
            Craft::$app->getRequest()->get($this->fieldsParam)
        );
    }

    /**
     * @return array
     */
    protected function getRequestedIncludes(): array
    {
        return $this->normalizeRequest(
            Craft::$app->getRequest()->get($this->includesParam)
        );
    }

    /**
     * @return array
     */
    protected function getRequestedExcludes(): array
    {
        return $this->normalizeRequest(
            Craft::$app->getRequest()->get($this->excludesParam)
        );
    }

    /**
     * @param $value
     * @return array
     */
    private function normalizeRequest($value): array
    {
        return is_string($value) ? preg_split('/\s*,\s*/', $value, -1, PREG_SPLIT_NO_EMPTY) : [];
    }
}
