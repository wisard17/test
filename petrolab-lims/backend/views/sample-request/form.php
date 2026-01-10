<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\Client;

$clients = Client::find()->select(['name','id'])->indexBy('id')->column();
?>
<div class="sample-request-form">
    <?php $form = ActiveForm::begin(); ?>
        <?= $form->field($model, 'request_no')->textInput(['readonly' => true]) ?>
        <?= $form->field($model, 'client_id')->dropDownList($clients, ['prompt' => 'Select client']) ?>
        <?= $form->field($model, 'request_date')->input('date') ?>
        <?= $form->field($model, 'contact_person')->textInput() ?>
        <?= $form->field($model, 'remarks')->textarea() ?>
        <?= $form->field($model, 'status')->dropDownList(['Draft' => 'Draft','Submitted' => 'Submitted','Approved' => 'Approved','Rejected' => 'Rejected']) ?>
        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-primary']) ?>
        </div>
    <?php ActiveForm::end(); ?>
</div>
