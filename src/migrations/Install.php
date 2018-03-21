<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/flux/license
 * @link       https://www.flipboxfactory.com/software/flux/
 */

namespace flipbox\flux\migrations;

use craft\db\Migration;
use flipbox\flux\records\Transformer as TransformerRecord;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class Install extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTables();
        $this->createIndexes();
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTableIfExists(TransformerRecord::tableName());
        return true;
    }

    /**
     * Creates the tables.
     *
     * @return void
     */
    protected function createTables()
    {
        $this->createTable(
            TransformerRecord::tableName(),
            [
                'id' => $this->primaryKey(),
                'handle' => $this->string()->notNull(),
                'class' => $this->string()->notNull(),
                'scope' => $this->string()->notNull(),
                'config' => $this->text(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid()
            ]
        );
    }

    /**
     * Creates the indexes.
     *
     * @return void
     */
    protected function createIndexes()
    {
        $this->createIndex(
            $this->db->getIndexName(
                TransformerRecord::tableName(),
                'handle',
                false,
                true
            ),
            TransformerRecord::tableName(),
            'handle',
            false
        );
        $this->createIndex(
            $this->db->getIndexName(
                TransformerRecord::tableName(),
                'handle',
                false
            ),
            TransformerRecord::tableName(),
            'handle',
            false
        );
        $this->createIndex(
            $this->db->getIndexName(
                TransformerRecord::tableName(),
                'handle,scope',
                true
            ),
            TransformerRecord::tableName(),
            'handle,scope',
            true
        );
    }
}
