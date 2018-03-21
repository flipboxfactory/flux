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
use flipbox\flux\Flux;
use flipbox\flux\helpers\TransformerHelper;
use Flipbox\Transform\Transformers\TransformerInterface;
use yii\base\Component;
use yii\db\QueryInterface;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class Transformers extends Component
{
    use Accessor;

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
     * @return callable|TransformerInterface|null
     */
    public function resolve(
        $transformer,
        string $scope = Flux::GLOBAL_SCOPE
    ) {
        if (null !== ($callable = TransformerHelper::resolve($transformer))) {
            return $callable;
        }

        if (is_string($transformer)) {
            return $this->find($transformer, $scope);
        }

        return null;
    }

    /**
     * @param string $identifier
     * @param string $scope
     * @return callable|TransformerInterface
     * @throws ObjectNotFoundException
     */
    public function get(
        string $identifier,
        string $scope = Flux::GLOBAL_SCOPE
    ) {
        if (null === ($transformer = $this->find($identifier, $scope))) {
            $this->notFoundException();
        }

        return $transformer;
    }

    /**
     * @param string $identifier
     * @param string $scope
     * @return callable|TransformerInterface|null
     */
    public function find(
        string $identifier,
        string $scope = Flux::GLOBAL_SCOPE
    ) {
        $identifierKey = is_numeric($identifier) ? 'id' : 'handle';

        return $this->findByCondition([
            $identifierKey => $identifier,
            'scope' => $scope
        ]);
    }
}
