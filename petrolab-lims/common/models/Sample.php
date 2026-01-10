<?php
namespace common\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use common\components\AuditBehavior;

class Sample extends ActiveRecord
{
    public static function tableName()
    {
        return 'sample';
    }

    public function behaviors()
    {
        return [TimestampBehavior::class, AuditBehavior::class];
    }

    public function rules()
    {
        return [
            [['sample_no', 'request_id', 'sample_type_id', 'received_date'], 'required'],
            [['request_id', 'sample_type_id', 'received_by', 'created_at', 'updated_at'], 'integer'],
            [['sample_description'], 'string'],
            [['received_date'], 'date', 'format' => 'php:Y-m-d'],
            [['quantity'], 'number'],
            [['sample_no'], 'string', 'max' => 30],
            [['container_type'], 'string', 'max' => 50],
            [['priority'], 'in', 'range' => ['Normal', 'Urgent']],
            [['status'], 'string', 'max' => 20],
            [['sample_no'], 'unique'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'sample_no' => 'Sample No',
            'sample_type_id' => 'Sample Type',
            'sample_description' => 'Description',
            'received_date' => 'Received Date',
            'priority' => 'Priority',
            'status' => 'Status',
        ];
    }

    public function getRequest()
    {
        return $this->hasOne(SampleRequest::class, ['id' => 'request_id']);
    }

    public function getSampleType()
    {
        return $this->hasOne(SampleType::class, ['id' => 'sample_type_id']);
    }

    public function getTests()
    {
        return $this->hasMany(SampleTest::class, ['sample_id' => 'id']);
    }
}
