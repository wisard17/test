<?php
namespace common\models;

use yii\db\ActiveRecord;
use common\components\AuditBehavior;

class SampleTest extends ActiveRecord
{
    public static function tableName()
    {
        return 'sample_test';
    }

    public function behaviors()
    {
        return [AuditBehavior::class];
    }

    public function rules()
    {
        return [
            [['sample_id', 'test_method_id', 'lab_section_id'], 'required'],
            [['sample_id', 'test_method_id', 'lab_section_id', 'assigned_to', 'instrument_id'], 'integer'],
            [['due_date'], 'date', 'format' => 'php:Y-m-d'],
            [['status'], 'string', 'max' => 20],
        ];
    }

    public function getSample()
    {
        return $this->hasOne(Sample::class, ['id' => 'sample_id']);
    }

    public function getTestMethod()
    {
        return $this->hasOne(TestMethod::class, ['id' => 'test_method_id']);
    }

    public function getResultHeader()
    {
        return $this->hasOne(TestResultHeader::class, ['sample_test_id' => 'id']);
    }
}
