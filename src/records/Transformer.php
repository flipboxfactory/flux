<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/flux/license
 * @link       https://www.flipboxfactory.com/software/flux/
 */

namespace flipbox\flux\records;

use flipbox\craft\ember\records\ActiveRecordWithId;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 *
 * @property string $class
 * @property string $scope
 * @property string $config
 */
class Transformer extends ActiveRecordWithId
{
    /**
     * The table name
     */
    const TABLE_ALIAS = 'transformers';

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(
            parent::rules(),
            [
                [
                    [
                        'scope'
                    ],
                    'required'
                ]
            ]
        );
    }
}
