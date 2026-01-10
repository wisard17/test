<?php
use yii\grid\GridView;
use yii\helpers\Html;

$this->title = 'Sample Requests';
?>
<div class="sample-request-index">
    <p><?= Html::a('Create Sample Request', ['create'], ['class' => 'btn btn-success']) ?></p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'request_no',
            [
                'attribute' => 'client_id',
                'value' => 'client.name'
            ],
            'request_date',
            'status',
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]);
    ?>
</div>
