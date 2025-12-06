<?php
namespace common\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use common\components\AuditBehavior;

class Client extends ActiveRecord
{
    public static function tableName()
    {
        return 'client';
    }

    public function behaviors()
    {
        return [TimestampBehavior::class, AuditBehavior::class];
    }

    public function rules()
    {
        return [
            [['name'], 'required'],
            [['address'], 'string'],
            [['created_at', 'updated_at'], 'integer'],
            [['name', 'contact_person'], 'string', 'max' => 150],
            [['email'], 'email'],
            [['phone', 'npwp'], 'string', 'max' => 50],
            [['status'], 'in', 'range' => ['active', 'inactive']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'name' => 'Name',
            'address' => 'Address',
            'contact_person' => 'Contact Person',
            'email' => 'Email',
            'phone' => 'Phone',
            'npwp' => 'NPWP',
            'status' => 'Status',
        ];
    }

    public function getSampleRequests()
    {
        return $this->hasMany(SampleRequest::class, ['client_id' => 'id']);
    }
}
