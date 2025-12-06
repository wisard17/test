<?php
namespace common\models;

use yii\db\ActiveRecord;

class Unit extends ActiveRecord
{
    public static function tableName()
    {
        return 'unit';
    }

    public function rules()
    {
        return [
            [['name', 'symbol'], 'required'],
            [['name'], 'string', 'max' => 50],
            [['symbol'], 'string', 'max' => 20],
        ];
    }
}
