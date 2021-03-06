<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var bariew\templateAbstractModule\models\Config $model
 */

$this->title = Yii::t('modules/template', 'Update Template#{number}', ['number' => $model->id]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('modules/template', 'Templates'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('modules/template', 'view'), 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('modules/template', 'Update');
?>
<div class="config-update">

    <h1><?php echo Html::encode($this->title) ?></h1>

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
