<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var bariew\templateAbstractModule\models\Config $model
 */

$this->title = Yii::t('modules/template', 'Create Notification Config');
$this->params['breadcrumbs'][] = ['label' => Yii::t('modules/template', 'Notification Configs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="config-create">

    <h1><?php echo Html::encode($this->title) ?></h1>

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
