<?php
namespace common\models;

use yii\db\ActiveRecord;

class Parameter extends ActiveRecord
{
    public static function tableName()
    {
        return 'parameter';
    }

    public function rules()
    {
        return [
            [['name', 'unit_id', 'method_id'], 'required'],
            [['unit_id', 'method_id', 'decimal_precision'], 'integer'],
            [['limit_min', 'limit_max'], 'number'],
            [['name'], 'string', 'max' => 100],
            [['formula'], 'string', 'max' => 255],
        ];
    }

    public function getUnit()
    {
        return $this->hasOne(Unit::class, ['id' => 'unit_id']);
    }

    public function getMethod()
    {
        return $this->hasOne(TestMethod::class, ['id' => 'method_id']);
    }
}
