<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/flux/license
 * @link       https://www.flipboxfactory.com/software/flux/
 */

namespace flipbox\flux\migrations;

use craft\db\Migration;
use flipbox\flux\records\Transformer;

class m180408_121732_class_not_null extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->alterColumn(
            Transformer::tableName(),
            'class',
            $this->string()
        );
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m180408_121732_class_not_null cannot be reverted.\n";
        return false;
    }
}
