<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/flux/license
 * @link       https://www.flipboxfactory.com/software/flux/
 */

namespace flipbox\flux\db;

use craft\db\Query;
use craft\helpers\Db;
use flipbox\craft\ember\queries\AuditAttributesTrait;
use flipbox\flux\Flux;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class TransformerQuery extends Query
{
    use AuditAttributesTrait;

    /**
     * @var int|int[]|null
     */
    public $id;

    /**
     * @var string|string[]|null
     */
    public $handle;

    /**
     * @var string|string[]|null
     */
    public $class;

    /**
     * @var string|string[]|null
     */
    public $scope = Flux::GLOBAL_SCOPE;

    /**
     * @inheritdoc
     */
    public function prepare($builder)
    {
        $this->applyAuditAttributeConditions();
        $this->applyConditions();
        return parent::prepare($builder);
    }

    /**
     * Apply attribute conditions
     */
    protected function applyConditions()
    {
        $attributes = ['id', 'handle', 'class', 'scope'];

        foreach ($attributes as $attribute) {
            if (null !== ($value = $this->{$attribute})) {
                $this->andWhere(Db::parseParam($attribute, $value));
            }
        }
    }
}
