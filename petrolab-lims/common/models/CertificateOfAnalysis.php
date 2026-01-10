<?php
namespace common\models;

use yii\db\ActiveRecord;
use common\components\AuditBehavior;

class CertificateOfAnalysis extends ActiveRecord
{
    public static function tableName()
    {
        return 'certificate_of_analysis';
    }

    public function behaviors()
    {
        return [AuditBehavior::class];
    }

    public function rules()
    {
        return [
            [['coa_no', 'sample_id', 'issue_date', 'approved_by', 'file_path'], 'required'],
            [['sample_id', 'approved_by'], 'integer'],
            [['issue_date'], 'date', 'format' => 'php:Y-m-d'],
            [['coa_no'], 'string', 'max' => 30],
            [['status'], 'string', 'max' => 20],
            [['file_path'], 'string', 'max' => 255],
            [['coa_no'], 'unique'],
        ];
    }

    public function getSample()
    {
        return $this->hasOne(Sample::class, ['id' => 'sample_id']);
    }
}
