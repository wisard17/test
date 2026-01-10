<?php
namespace common\models;

use yii\db\ActiveRecord;

class TestMethod extends ActiveRecord
{
    public static function tableName()
    {
        return 'test_method';
    }

    public function rules()
    {
        return [
            [['code', 'name'], 'required'],
            [['description'], 'string'],
            [['tat_default'], 'integer'],
            [['active_flag'], 'boolean'],
            [['code'], 'string', 'max' => 30],
            [['name', 'standard_ref'], 'string', 'max' => 150],
            [['code'], 'unique'],
        ];
    }
}
