<?php
namespace common\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use common\components\AuditBehavior;

class SampleRequest extends ActiveRecord
{
    public static function tableName()
    {
        return 'sample_request';
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => false,
            ],
            AuditBehavior::class,
        ];
    }

    public function rules()
    {
        return [
            [['request_no', 'client_id', 'request_date', 'status', 'created_by'], 'required'],
            [['client_id', 'created_by', 'created_at'], 'integer'],
            [['request_date'], 'date', 'format' => 'php:Y-m-d'],
            [['remarks'], 'string'],
            [['request_no'], 'string', 'max' => 30],
            [['contact_person'], 'string', 'max' => 100],
            [['status'], 'in', 'range' => ['Draft', 'Submitted', 'Approved', 'Rejected']],
            [['request_no'], 'unique'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'request_no' => 'Request No',
            'client_id' => 'Client',
            'request_date' => 'Request Date',
            'contact_person' => 'Contact Person',
            'remarks' => 'Remarks',
            'status' => 'Status',
        ];
    }

    public function getClient()
    {
        return $this->hasOne(Client::class, ['id' => 'client_id']);
    }

    public function getSamples()
    {
        return $this->hasMany(Sample::class, ['request_id' => 'id']);
    }
}
