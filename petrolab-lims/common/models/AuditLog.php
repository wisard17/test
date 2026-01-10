<?php
namespace common\models;

use yii\db\ActiveRecord;

class AuditLog extends ActiveRecord
{
    public static function tableName()
    {
        return 'audit_log';
    }

    public function rules()
    {
        return [
            [['action', 'model', 'model_pk', 'created_at'], 'required'],
            [['user_id', 'created_at'], 'integer'],
            [['before_data', 'after_data'], 'safe'],
            [['action'], 'string', 'max' => 50],
            [['model'], 'string', 'max' => 120],
            [['model_pk'], 'string', 'max' => 64],
        ];
    }
}
