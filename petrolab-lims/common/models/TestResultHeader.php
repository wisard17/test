<?php
namespace common\models;

use yii\db\ActiveRecord;
use common\components\AuditBehavior;

class TestResultHeader extends ActiveRecord
{
    public static function tableName()
    {
        return 'test_result_header';
    }

    public function behaviors()
    {
        return [AuditBehavior::class];
    }

    public function rules()
    {
        return [
            [['sample_test_id', 'analyst_id'], 'required'],
            [['sample_test_id', 'analyst_id'], 'integer'],
            [['analysis_start_date', 'analysis_end_date'], 'date', 'format' => 'php:Y-m-d'],
            [['remarks'], 'string'],
            [['status'], 'string', 'max' => 20],
        ];
    }

    public function getSampleTest()
    {
        return $this->hasOne(SampleTest::class, ['id' => 'sample_test_id']);
    }

    public function getDetails()
    {
        return $this->hasMany(TestResultDetail::class, ['result_header_id' => 'id']);
    }
}
