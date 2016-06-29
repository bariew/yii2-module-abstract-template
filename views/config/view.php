<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/**
 * @var yii\web\View $this
 * @var bariew\templateAbstractAbstractModule\models\Config $model
 */

$this->title = Yii::t('modules/template', 'Notification Config#{number}', ['number' => $model->id]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('modules/template', 'Notification Configs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="config-view">

    <h1><?php echo Html::encode($this->title) ?></h1>

    <p>
        <?php echo Html::a(Yii::t('modules/template', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?php echo Html::a(Yii::t('modules/template', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('modules/template', 'Are you sure you want to delete this log?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?php echo DetailView::widget([
        'model' => $model,
        'attributes' => [
            \bariew\yii2Tools\helpers\GridHelper::listFormat($model, 'type'),
            'address',
            \bariew\yii2Tools\helpers\GridHelper::arrayFormat($model, 'subject'),
            \bariew\yii2Tools\helpers\GridHelper::arrayFormat($model, 'content'),
            \bariew\yii2Tools\helpers\GridHelper::listFormat($model, 'model_class'),
            \bariew\yii2Tools\helpers\GridHelper::listFormat($model, 'model_event'),
        ],
    ]) ?>

</div>
