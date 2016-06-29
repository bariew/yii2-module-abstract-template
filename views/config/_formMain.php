<?php
use yii\helpers\Url;
/**
 * @var yii\web\View $this
 * @var bariew\templateModule\models\Config $model
 * @var yii\widgets\ActiveForm $form
 */
?>

<?= $form->field($model, 'type')->dropDownList($model->typeList()) ?>

<?= $form->field($model, 'address')->textInput(['maxlength' => 255]) ?>

<?= $form->field($model, 'model_class')->widget(\kartik\select2\Select2::classname(), [
    'data' => $model->modelClassList(),
    'options' => [
        'prompt' => '',
        'class' => 'form-control',
        'onchange'  => '
            $.post(
                "'.Url::toRoute(["model-variables"]).'",
                $(this).parents("form").serialize(),
                function(data) {  $(".configVariables").html(data); }
            )'
    ]
]);?>
<?= $form->field($model, 'model_event')->widget(\kartik\depdrop\DepDrop::classname(), [
        'data'=> $model->modelEventList(),
        'options' => [ 'class' => 'form-control'],
        'type' => \kartik\depdrop\DepDrop::TYPE_DEFAULT,
        'pluginOptions'=>[
            'depends' => ['config-model_class'],
            'url' => Url::toRoute(['events']),
            'loadingText' => '',
            'initialize' => true
        ],
    ]);
?>
