# Petrolab LIMS (Yii2 Advanced Template)

## Ringkasan Arsitektur
- **Template**: Yii2 Advanced (memisahkan frontend, backend, console) sehingga portal client terisolasi dari area operasional dan lebih mudah dideploy berskala enterprise.
- **Bahasa/Platform**: PHP 8+, Yii2, MySQL/MariaDB.
- **Module utama**: Authentication & RBAC, Master Data, Lifecycle Sampel, Input & Manajemen Hasil, Review & Approval, Certificate of Analysis (CoA), Dashboard & Laporan, Audit Trail.
- **Prinsip**: semua skema dibuat via **migration**, akses dikontrol RBAC, audit log otomatis untuk entitas kritikal, business logic dipisah ke **service/component**.

## Skema Database (ringkas)
Tabel inti dan relasi kunci:
- `client(id, name, address, contact_person, email, phone, npwp, status, created_at, updated_at)` → 1..N `sample_request`.
- `sample_type(id, name, status)` → referensi di `sample.sample_type_id`.
- `test_method(id, code, name, standard_ref, description, tat_default, active_flag)`.
- `unit(id, name, symbol)`.
- `parameter(id, name, unit_id→unit, method_id→test_method, limit_min, limit_max, decimal_precision, formula)`.
- `instrument(id, name, code, serial_number, location, status)`.
- `lab_section(id, name, status)`.
- `user(id, username, auth_key, password_hash, email, full_name, role, lab_section_id→lab_section, signature_image, status, created_at, updated_at)`.
- `sample_request(id, request_no, client_id→client, request_date, contact_person, remarks, status, created_by→user, created_at)` → 1..N `sample`.
- `sample(id, sample_no, request_id→sample_request, sample_type_id→sample_type, sample_description, received_date, received_by→user, container_type, quantity, priority, status, created_at, updated_at)` → 1..N `sample_test`.
- `sample_test(id, sample_id→sample, test_method_id→test_method, lab_section_id→lab_section, assigned_to→user, due_date, status, instrument_id→instrument)` → 1..1 `test_result_header`.
- `test_result_header(id, sample_test_id→sample_test, analyst_id→user, analysis_start_date, analysis_end_date, status, remarks)` → 1..N `test_result_detail`.
- `test_result_detail(id, result_header_id→test_result_header, parameter_id→parameter, result_value, unit_id→unit, is_out_of_spec, note)`.
- `certificate_of_analysis(id, coa_no, sample_id→sample, issue_date, approved_by→user, file_path, status)`.
- `audit_log(id, user_id→user, action, model, model_pk, before_data JSON, after_data JSON, created_at)`.
- RBAC tabel bawaan Yii2: `auth_item`, `auth_item_child`, `auth_assignment`, `auth_rule`.

## Contoh Migration Kunci
Gunakan console command `yii migrate`.
```php
<?php
use yii\db\Migration;

class m240101_000001_create_sample_tables extends Migration
{
    public function safeUp()
    {
        $this->createTable('sample_request', [
            'id' => $this->primaryKey(),
            'request_no' => $this->string(30)->notNull()->unique(),
            'client_id' => $this->integer()->notNull(),
            'request_date' => $this->date()->notNull(),
            'contact_person' => $this->string(100),
            'remarks' => $this->text(),
            'status' => $this->string(20)->notNull()->defaultValue('Draft'),
            'created_by' => $this->integer()->notNull(),
            'created_at' => $this->integer()->notNull(),
        ]);
        $this->addForeignKey('fk_sr_client','sample_request','client_id','client','id');
        $this->addForeignKey('fk_sr_user','sample_request','created_by','user','id');

        $this->createTable('sample', [
            'id' => $this->primaryKey(),
            'sample_no' => $this->string(30)->notNull()->unique(),
            'request_id' => $this->integer()->notNull(),
            'sample_type_id' => $this->integer()->notNull(),
            'sample_description' => $this->text(),
            'received_date' => $this->date()->notNull(),
            'received_by' => $this->integer(),
            'container_type' => $this->string(50),
            'quantity' => $this->decimal(10,2),
            'priority' => $this->string(10)->defaultValue('Normal'),
            'status' => $this->string(20)->defaultValue('Registered'),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);
        $this->addForeignKey('fk_s_request','sample','request_id','sample_request','id');
        $this->addForeignKey('fk_s_type','sample','sample_type_id','sample_type','id');
        $this->addForeignKey('fk_s_received_by','sample','received_by','user','id');

        $this->createTable('sample_test', [
            'id' => $this->primaryKey(),
            'sample_id' => $this->integer()->notNull(),
            'test_method_id' => $this->integer()->notNull(),
            'lab_section_id' => $this->integer()->notNull(),
            'assigned_to' => $this->integer(),
            'due_date' => $this->date(),
            'status' => $this->string(20)->defaultValue('Pending'),
            'instrument_id' => $this->integer(),
        ]);
        $this->addForeignKey('fk_st_sample','sample_test','sample_id','sample','id');
        $this->addForeignKey('fk_st_method','sample_test','test_method_id','test_method','id');
        $this->addForeignKey('fk_st_section','sample_test','lab_section_id','lab_section','id');
        $this->addForeignKey('fk_st_assigned','sample_test','assigned_to','user','id');
        $this->addForeignKey('fk_st_instrument','sample_test','instrument_id','instrument','id');

        $this->createTable('test_result_header', [
            'id' => $this->primaryKey(),
            'sample_test_id' => $this->integer()->notNull()->unique(),
            'analyst_id' => $this->integer()->notNull(),
            'analysis_start_date' => $this->date(),
            'analysis_end_date' => $this->date(),
            'status' => $this->string(20)->defaultValue('Draft'),
            'remarks' => $this->text(),
        ]);
        $this->addForeignKey('fk_trh_sample_test','test_result_header','sample_test_id','sample_test','id');
        $this->addForeignKey('fk_trh_analyst','test_result_header','analyst_id','user','id');

        $this->createTable('test_result_detail', [
            'id' => $this->primaryKey(),
            'result_header_id' => $this->integer()->notNull(),
            'parameter_id' => $this->integer()->notNull(),
            'result_value' => $this->decimal(12,4),
            'unit_id' => $this->integer()->notNull(),
            'is_out_of_spec' => $this->boolean()->defaultValue(false),
            'note' => $this->string(255),
        ]);
        $this->addForeignKey('fk_trd_header','test_result_detail','result_header_id','test_result_header','id');
        $this->addForeignKey('fk_trd_parameter','test_result_detail','parameter_id','parameter','id');
        $this->addForeignKey('fk_trd_unit','test_result_detail','unit_id','unit','id');

        $this->createTable('certificate_of_analysis', [
            'id' => $this->primaryKey(),
            'coa_no' => $this->string(30)->notNull()->unique(),
            'sample_id' => $this->integer()->notNull(),
            'issue_date' => $this->date()->notNull(),
            'approved_by' => $this->integer()->notNull(),
            'file_path' => $this->string(255),
            'status' => $this->string(20)->defaultValue('Draft'),
        ]);
        $this->addForeignKey('fk_coa_sample','certificate_of_analysis','sample_id','sample','id');
        $this->addForeignKey('fk_coa_user','certificate_of_analysis','approved_by','user','id');

        $this->createTable('audit_log', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer(),
            'action' => $this->string(50),
            'model' => $this->string(100),
            'model_pk' => $this->string(50),
            'before_data' => $this->json(),
            'after_data' => $this->json(),
            'created_at' => $this->integer()->notNull(),
        ]);
        $this->addForeignKey('fk_audit_user','audit_log','user_id','user','id');
    }

    public function safeDown()
    {
        $this->dropTable('audit_log');
        $this->dropTable('certificate_of_analysis');
        $this->dropTable('test_result_detail');
        $this->dropTable('test_result_header');
        $this->dropTable('sample_test');
        $this->dropTable('sample');
        $this->dropTable('sample_request');
    }
}
```

## Model Utama (contoh ringkas)
```php
<?php
namespace backend\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

class Sample extends ActiveRecord
{
    public static function tableName() { return 'sample'; }

    public function behaviors()
    {
        return [TimestampBehavior::class];
    }

    public function rules()
    {
        return [
            [['sample_no','request_id','sample_type_id','received_date'], 'required'],
            [['request_id','sample_type_id','received_by'], 'integer'],
            [['received_date'], 'date', 'format' => 'php:Y-m-d'],
            [['sample_description'], 'string'],
            [['quantity'], 'number'],
            [['sample_no'], 'string', 'max' => 30],
            [['status','priority'], 'string', 'max' => 20],
            [['sample_no'], 'unique'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'sample_no' => 'Sample No',
            'sample_type_id' => 'Sample Type',
            'received_date' => 'Received Date',
        ];
    }

    public function getRequest() { return $this->hasOne(SampleRequest::class, ['id'=>'request_id']); }
    public function getSampleType() { return $this->hasOne(SampleType::class, ['id'=>'sample_type_id']); }
    public function getTests() { return $this->hasMany(SampleTest::class, ['sample_id'=>'id']); }
}
```

```php
<?php
namespace backend\models;

use yii\db\ActiveRecord;

class TestResultHeader extends ActiveRecord
{
    public static function tableName() { return 'test_result_header'; }

    public function rules()
    {
        return [
            [['sample_test_id','analyst_id'], 'required'],
            [['analysis_start_date','analysis_end_date'], 'date', 'format' => 'php:Y-m-d'],
            [['status'], 'in', 'range' => ['Draft','Submitted','Reviewed','Approved']],
        ];
    }

    public function getDetails() { return $this->hasMany(TestResultDetail::class, ['result_header_id'=>'id']); }
    public function getSampleTest() { return $this->hasOne(SampleTest::class, ['id'=>'sample_test_id']); }
}
```

## Controller & View (contoh)
### SampleRequestController (backend/controllers/SampleRequestController.php)
```php
public function behaviors()
{
    return [
        'access' => [
            'class' => \yii\filters\AccessControl::class,
            'rules' => [
                ['allow' => true, 'roles' => ['ADMIN','CUSTOMER_SERVICE','LAB_MANAGER']],
            ],
        ],
    ];
}

public function actionCreate()
{
    $model = new SampleRequest();
    if ($model->load(Yii::$app->request->post()) && $model->save()) {
        return $this->redirect(['view','id'=>$model->id]);
    }
    return $this->render('create',['model'=>$model]);
}
```

### View contoh (backend/views/sample/_form.php)
```php
<?php $form = ActiveForm::begin(); ?>
<?= $form->field($model, 'sample_no')->textInput(['readonly'=>true, 'value'=>$model->isNewRecord ? $model->generateNo() : $model->sample_no]) ?>
<?= $form->field($model, 'sample_type_id')->dropDownList($sampleTypes) ?>
<?= $form->field($model, 'received_date')->input('date') ?>
<?= $form->field($model, 'priority')->dropDownList(['Normal'=>'Normal','Urgent'=>'Urgent']) ?>
<div class="form-group">
    <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
</div>
<?php ActiveForm::end(); ?>
```

### Input Hasil Uji (backend/controllers/TestResultController.php)
```php
public function actionUpdate($id)
{
    $header = TestResultHeader::findOne($id);
    $details = $header->details;
    if (Yii::$app->request->isPost) {
        $header->load(Yii::$app->request->post());
        $valid = $header->validate();
        foreach ($details as $detail) {
            $detail->load(Yii::$app->request->post());
            $detail->is_out_of_spec = $detail->result_value < $detail->parameter->limit_min || $detail->result_value > $detail->parameter->limit_max;
            $valid = $detail->validate() && $valid;
        }
        if ($valid) {
            $header->save(false);
            foreach ($details as $detail) { $detail->save(false); }
            Yii::$app->session->setFlash('success','Result saved');
            return $this->redirect(['view','id'=>$header->id]);
        }
    }
    return $this->render('update',[ 'header'=>$header, 'details'=>$details ]);
}
```

## RBAC
### Seed Role & Permission (console/migrations/m240101_000010_rbac.php)
```php
$auth = Yii::$app->authManager;
$roles = ['ADMIN','LAB_MANAGER','ANALYST','CUSTOMER_SERVICE','CLIENT'];
foreach ($roles as $roleName) {
    if (!$auth->getRole($roleName)) {
        $auth->add($auth->createRole($roleName));
    }
}
// contoh permission
$manageSample = $auth->createPermission('manageSample');
$auth->add($manageSample);
$auth->addChild($auth->getRole('LAB_MANAGER'), $manageSample);
$auth->addChild($auth->getRole('ADMIN'), $manageSample);
```

### AccessControl di controller
```php
'components' => [
    'authManager' => [
        'class' => 'yii\rbac\DbManager',
        'defaultRoles' => ['CLIENT'],
    ],
],
```
Gunakan `roles` pada AccessControl seperti contoh di SampleRequestController.

## Audit Trail
### Behavior (common/components/AuditBehavior.php)
```php
class AuditBehavior extends Behavior
{
    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_INSERT => 'logInsert',
            ActiveRecord::EVENT_AFTER_UPDATE => 'logUpdate',
        ];
    }

    protected function log($action, $before, $after)
    {
        $log = new AuditLog([
            'user_id' => Yii::$app->user->id ?? null,
            'action' => $action,
            'model' => get_class($this->owner),
            'model_pk' => json_encode($this->owner->getPrimaryKey(true)),
            'before_data' => $before,
            'after_data' => $after,
            'created_at' => time(),
        ]);
        $log->save(false);
    }

    public function logInsert($event)
    {
        $this->log('insert', null, $this->owner->attributes);
    }

    public function logUpdate($event)
    {
        $this->log('update', $event->changedAttributes, $this->owner->attributes);
    }
}
```
Tambahkan behavior ke model kritikal: `public function behaviors(){ return [AuditBehavior::class]; }`.

## Generate PDF CoA
- Gunakan ekstensi `yii2-mpdf/yii2-mpdf`.

### Controller Action (backend/controllers/CoaController.php)
```php
public function actionGenerate($sampleId)
{
    $sample = Sample::findOne($sampleId);
    $tests = $sample->tests;
    $html = $this->renderPartial('_coa_pdf',['sample'=>$sample,'tests'=>$tests]);

    $pdf = new \Mpdf\Mpdf(['tempDir' => '@runtime/mpdf']);
    $pdf->WriteHTML($html);
    $filename = "uploads/coa/" . $sample->sample_no . "-" . date('Ymd') . ".pdf";
    $pdf->Output(Yii::getAlias('@backend/web/') . $filename, \Mpdf\Output\Destination::FILE);

    $coa = new CertificateOfAnalysis([
        'coa_no' => CoaService::generateNo(),
        'sample_id' => $sample->id,
        'issue_date' => date('Y-m-d'),
        'approved_by' => Yii::$app->user->id,
        'file_path' => $filename,
        'status' => 'Issued',
    ]);
    $coa->save(false);

    return $this->redirect(['view','id'=>$coa->id]);
}
```

### Template PDF (backend/views/coa/_coa_pdf.php)
```php
<h3>Certificate of Analysis</h3>
<p>Client: <?= Html::encode($sample->request->client->name) ?></p>
<p>Sample No: <?= Html::encode($sample->sample_no) ?></p>
<table class="table table-bordered" width="100%">
    <thead><tr><th>Method</th><th>Parameter</th><th>Result</th><th>Unit</th><th>Limits</th><th>OOS</th></tr></thead>
    <tbody>
    <?php foreach($tests as $test): foreach($test->resultHeader->details as $detail): ?>
        <tr>
            <td><?= Html::encode($test->testMethod->name) ?></td>
            <td><?= Html::encode($detail->parameter->name) ?></td>
            <td><?= $detail->result_value ?></td>
            <td><?= Html::encode($detail->unit->symbol) ?></td>
            <td><?= $detail->parameter->limit_min ?> - <?= $detail->parameter->limit_max ?></td>
            <td><?= $detail->is_out_of_spec ? 'YES' : 'NO' ?></td>
        </tr>
    <?php endforeach; endforeach; ?>
    </tbody>
</table>
<p>Approved by: <img src="<?= Yii::getAlias('@web/' . $sample->request->approvedBy->signature_image) ?>" height="60" /></p>
```

## Konfigurasi Penting (config/main.php)
```php
'components' => [
    'request' => [
        'cookieValidationKey' => getenv('APP_KEY'),
        'enableCsrfValidation' => true,
    ],
    'user' => [
        'identityClass' => 'common\models\User',
        'enableAutoLogin' => true,
        'authTimeout' => 3600,
    ],
    'db' => [
        'class' => 'yii\db\Connection',
        'dsn' => 'mysql:host=localhost;dbname=petrolab_lims',
        'username' => getenv('DB_USER'),
        'password' => getenv('DB_PASS'),
        'charset' => 'utf8mb4',
    ],
    'authManager' => [
        'class' => 'yii\rbac\DbManager',
    ],
    'formatter' => [
        'class' => 'yii\i18n\Formatter',
        'nullDisplay' => '-',
    ],
];
```

## Modul & Struktur Folder (Yii2 Advanced)
- `frontend/` : Portal CLIENT untuk tracking status & download CoA.
- `backend/` : Operasional lab (CS, Analyst, Lab Manager, Admin).
- `common/` : Model shared (User, Client, Parameter, dll), komponen audit.
- `console/` : Migration, seeding RBAC, cron (mis. notifikasi overdue).
- `modules/lab/` : Modul lifecycle sampel, hasil uji, CoA.
- `modules/report/` : Dashboard & laporan (GridView + export).

## Dashboard & Laporan
- Gunakan `kartik/grid` untuk filter + export Excel/CSV.
- Query contoh TAT report: hitung `DATEDIFF(sample_test.completed_at, sample.received_date)` per metode/per client.
- Filter RBAC: CLIENT hanya `where client_id = currentUser->client_id`; ANALYST hanya `assigned_to = user_id` kecuali punya role lebih tinggi.

## Keamanan & Validasi
- CSRF aktif, input difilter via rules() dan `Html::encode` di view.
- Validasi numeric sesuai `decimal_precision` pada parameter (`number` + custom validator).
- Upload `signature_image` dibatasi tipe & ukuran.
- Semua endpoint REST (jika ditambah) gunakan `HttpBearerAuth` + scope RBAC.

## Barcode/QR
- Gunakan ekstensi `yiisoft/yii2-qr` atau library `endroid/qr-code`. Sample label dapat di-render di view detail sampel dan dicetak via mPDF.

## Alur Review & Approval
1. ANALYST isi hasil → status header `Submitted`.
2. LAB_MANAGER review: set `Reviewed` atau `Approved`; jika perlu revisi set `Draft/Rework`.
3. Setelah semua `sample_test` Approved → action generate CoA.
4. CLIENT dapat unduh CoA dari frontend.

Dokumen ini dapat menjadi acuan implementasi awal Petrolab LIMS dengan Yii2.
