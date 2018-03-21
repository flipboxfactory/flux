<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/flux/license
 * @link       https://www.flipboxfactory.com/software/flux/
 */

namespace flipbox\flux\db;

use craft\db\Query;
use craft\db\QueryAbortedException;
use craft\helpers\Db;
use flipbox\ember\db\traits\AuditAttributes;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class TransformerQuery extends Query
{
    use AuditAttributes;

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
    public $scope;

    /**
     * @inheritdoc
     *
     * @throws QueryAbortedException if it can be determined that there won’t be any results
     */
    public function prepare($builder)
    {
        $this->applyAuditAttributeConditions();
        $this->applyAttributeConditions();
        return parent::prepare($builder);
    }

    /**
     *
     */
    protected function applyAttributeConditions()
    {
        if ($this->id !== null) {
            $this->andWhere(Db::parseParam('id', $this->id));
        }

        if ($this->handle !== null) {
            $this->andWhere(Db::parseParam('handle', $this->handle));
        }

        if ($this->class !== null) {
            $this->andWhere(Db::parseParam('class', $this->class));
        }

        if ($this->scope !== null) {
            $this->andWhere(Db::parseParam('scope', $this->scope));
        }
    }
}
