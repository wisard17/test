<?php
use yii\db\Migration;

class m240101_000001_create_petrolab_lims extends Migration
{
    public function safeUp()
    {
        // master tables
        $this->createTable('client', [
            'id' => $this->primaryKey(),
            'name' => $this->string(150)->notNull(),
            'address' => $this->text(),
            'contact_person' => $this->string(100),
            'email' => $this->string(120),
            'phone' => $this->string(50),
            'npwp' => $this->string(50),
            'status' => $this->string(20)->notNull()->defaultValue('active'),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);

        $this->createTable('sample_type', [
            'id' => $this->primaryKey(),
            'name' => $this->string(100)->notNull(),
            'status' => $this->string(20)->defaultValue('active'),
        ]);

        $this->createTable('test_method', [
            'id' => $this->primaryKey(),
            'code' => $this->string(30)->notNull()->unique(),
            'name' => $this->string(150)->notNull(),
            'standard_ref' => $this->string(80),
            'description' => $this->text(),
            'tat_default' => $this->integer()->defaultValue(3),
            'active_flag' => $this->boolean()->defaultValue(true),
        ]);

        $this->createTable('unit', [
            'id' => $this->primaryKey(),
            'name' => $this->string(50)->notNull(),
            'symbol' => $this->string(20)->notNull(),
        ]);

        $this->createTable('parameter', [
            'id' => $this->primaryKey(),
            'name' => $this->string(100)->notNull(),
            'unit_id' => $this->integer()->notNull(),
            'method_id' => $this->integer()->notNull(),
            'limit_min' => $this->decimal(12,4),
            'limit_max' => $this->decimal(12,4),
            'decimal_precision' => $this->integer()->defaultValue(2),
            'formula' => $this->string(255),
        ]);

        $this->createTable('instrument', [
            'id' => $this->primaryKey(),
            'name' => $this->string(120)->notNull(),
            'code' => $this->string(50)->notNull()->unique(),
            'serial_number' => $this->string(80),
            'location' => $this->string(120),
            'status' => $this->string(20)->defaultValue('active'),
        ]);

        $this->createTable('lab_section', [
            'id' => $this->primaryKey(),
            'name' => $this->string(100)->notNull(),
            'status' => $this->string(20)->defaultValue('active'),
        ]);

        $this->createTable('user', [
            'id' => $this->primaryKey(),
            'username' => $this->string(60)->notNull()->unique(),
            'auth_key' => $this->string(32)->notNull(),
            'password_hash' => $this->string()->notNull(),
            'password_reset_token' => $this->string()->unique(),
            'email' => $this->string(120)->notNull()->unique(),
            'full_name' => $this->string(120)->notNull(),
            'role' => $this->string(30)->notNull(),
            'lab_section_id' => $this->integer(),
            'signature_image' => $this->string(255),
            'status' => $this->smallInteger()->defaultValue(10),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);

        // lifecycle
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

        $this->createTable('test_result_header', [
            'id' => $this->primaryKey(),
            'sample_test_id' => $this->integer()->notNull(),
            'analyst_id' => $this->integer()->notNull(),
            'analysis_start_date' => $this->date(),
            'analysis_end_date' => $this->date(),
            'status' => $this->string(20)->defaultValue('Draft'),
            'remarks' => $this->text(),
        ]);

        $this->createTable('test_result_detail', [
            'id' => $this->primaryKey(),
            'result_header_id' => $this->integer()->notNull(),
            'parameter_id' => $this->integer()->notNull(),
            'result_value' => $this->decimal(14,4)->notNull(),
            'unit_id' => $this->integer()->notNull(),
            'is_out_of_spec' => $this->boolean()->notNull()->defaultValue(false),
            'note' => $this->string(255),
        ]);

        $this->createTable('certificate_of_analysis', [
            'id' => $this->primaryKey(),
            'coa_no' => $this->string(30)->notNull()->unique(),
            'sample_id' => $this->integer()->notNull(),
            'issue_date' => $this->date()->notNull(),
            'approved_by' => $this->integer()->notNull(),
            'file_path' => $this->string(255)->notNull(),
            'status' => $this->string(20)->defaultValue('Draft'),
        ]);

        $this->createTable('audit_log', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer(),
            'action' => $this->string(50)->notNull(),
            'model' => $this->string(120)->notNull(),
            'model_pk' => $this->string(64)->notNull(),
            'before_data' => $this->json(),
            'after_data' => $this->json(),
            'created_at' => $this->integer()->notNull(),
        ]);

        // fks
        $this->addForeignKey('fk_param_unit', 'parameter', 'unit_id', 'unit', 'id');
        $this->addForeignKey('fk_param_method', 'parameter', 'method_id', 'test_method', 'id');
        $this->addForeignKey('fk_user_lab_section', 'user', 'lab_section_id', 'lab_section', 'id');
        $this->addForeignKey('fk_sr_client', 'sample_request', 'client_id', 'client', 'id');
        $this->addForeignKey('fk_sr_user', 'sample_request', 'created_by', 'user', 'id');
        $this->addForeignKey('fk_sample_request', 'sample', 'request_id', 'sample_request', 'id');
        $this->addForeignKey('fk_sample_type', 'sample', 'sample_type_id', 'sample_type', 'id');
        $this->addForeignKey('fk_sample_received_by', 'sample', 'received_by', 'user', 'id');
        $this->addForeignKey('fk_sample_test_sample', 'sample_test', 'sample_id', 'sample', 'id');
        $this->addForeignKey('fk_sample_test_method', 'sample_test', 'test_method_id', 'test_method', 'id');
        $this->addForeignKey('fk_sample_test_section', 'sample_test', 'lab_section_id', 'lab_section', 'id');
        $this->addForeignKey('fk_sample_test_assign', 'sample_test', 'assigned_to', 'user', 'id');
        $this->addForeignKey('fk_sample_test_instrument', 'sample_test', 'instrument_id', 'instrument', 'id');
        $this->addForeignKey('fk_trh_sample_test', 'test_result_header', 'sample_test_id', 'sample_test', 'id');
        $this->addForeignKey('fk_trh_analyst', 'test_result_header', 'analyst_id', 'user', 'id');
        $this->addForeignKey('fk_trd_header', 'test_result_detail', 'result_header_id', 'test_result_header', 'id');
        $this->addForeignKey('fk_trd_param', 'test_result_detail', 'parameter_id', 'parameter', 'id');
        $this->addForeignKey('fk_trd_unit', 'test_result_detail', 'unit_id', 'unit', 'id');
        $this->addForeignKey('fk_coa_sample', 'certificate_of_analysis', 'sample_id', 'sample', 'id');
        $this->addForeignKey('fk_coa_approver', 'certificate_of_analysis', 'approved_by', 'user', 'id');
        $this->addForeignKey('fk_audit_user', 'audit_log', 'user_id', 'user', 'id');
    }

    public function safeDown()
    {
        $tables = [
            'audit_log','certificate_of_analysis','test_result_detail','test_result_header','sample_test','sample','sample_request','user','lab_section','instrument','parameter','unit','test_method','sample_type','client'
        ];
        foreach ($tables as $table) {
            $this->dropTable($table);
        }
    }
}
