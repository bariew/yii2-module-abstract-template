<?php

use yii\helpers\Html;
use yii\grid\GridView;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var bariew\templateAbstractModule\models\ConfigSearch $searchModel
 */

$this->title = Yii::t('modules/template', 'Templates');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="config-index">

    <h1>
        <?php echo Html::encode($this->title) ?>
        <?php echo Html::a(Yii::t('modules/template', 'Create Template'), ['create'], ['class' => 'btn btn-success pull-right']) ?>
    </h1>

    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            \bariew\yii2Tools\helpers\GridHelper::listFormat($searchModel, 'type'),
            'address',
            \bariew\yii2Tools\helpers\GridHelper::arrayFormat($searchModel, 'subject'),
            \bariew\yii2Tools\helpers\GridHelper::arrayFormat($searchModel, 'content'),
            \bariew\yii2Tools\helpers\GridHelper::listFormat($searchModel, 'model_class'),
            \bariew\yii2Tools\helpers\GridHelper::listFormat($searchModel, 'model_event'),
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
