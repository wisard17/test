<?php
namespace common\models;

use yii\db\ActiveRecord;
use common\components\AuditBehavior;

class TestResultDetail extends ActiveRecord
{
    public static function tableName()
    {
        return 'test_result_detail';
    }

    public function behaviors()
    {
        return [AuditBehavior::class];
    }

    public function rules()
    {
        return [
            [['result_header_id', 'parameter_id', 'result_value', 'unit_id'], 'required'],
            [['result_header_id', 'parameter_id', 'unit_id'], 'integer'],
            [['result_value'], 'number'],
            [['is_out_of_spec'], 'boolean'],
            [['note'], 'string', 'max' => 255],
        ];
    }

    public function beforeValidate()
    {
        if (parent::beforeValidate()) {
            $parameter = $this->parameter;
            if ($parameter && $parameter->limit_min !== null && $this->result_value < $parameter->limit_min) {
                $this->is_out_of_spec = true;
            }
            if ($parameter && $parameter->limit_max !== null && $this->result_value > $parameter->limit_max) {
                $this->is_out_of_spec = true;
            }
            return true;
        }
        return false;
    }

    public function getHeader()
    {
        return $this->hasOne(TestResultHeader::class, ['id' => 'result_header_id']);
    }

    public function getParameter()
    {
        return $this->hasOne(Parameter::class, ['id' => 'parameter_id']);
    }

    public function getUnit()
    {
        return $this->hasOne(Unit::class, ['id' => 'unit_id']);
    }
}
