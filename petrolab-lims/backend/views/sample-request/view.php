<?php
use yii\widgets\DetailView;
use yii\helpers\Html;

$this->title = $model->request_no;
?>
<div class="sample-request-view">
    <p><?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?></p>
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'request_no',
            'request_date',
            'contact_person',
            'status',
            [
                'label' => 'Client',
                'value' => $model->client ? $model->client->name : null,
            ],
            'remarks:ntext',
        ],
    ]);
    ?>
</div>
