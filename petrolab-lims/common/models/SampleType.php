<?php
namespace common\models;

use yii\db\ActiveRecord;

class SampleType extends ActiveRecord
{
    public static function tableName()
    {
        return 'sample_type';
    }

    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 100],
            [['status'], 'string', 'max' => 20],
        ];
    }
}
