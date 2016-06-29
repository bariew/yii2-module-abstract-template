<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\DetailView;
/**
 * @var yii\web\View $this
 * @var bariew\templateModule\models\Config $model
 * @var yii\widgets\ActiveForm $form
 */
?>

<div class="config-form">

    <?php $form = ActiveForm::begin(); ?>

        <?php $tabs = [[
            'label' => Yii::t('app', 'Main'),
            'content' => $this->render('_formMain', compact('form', 'model'))
        ]];
        foreach ($model->languageList() as $language) {
            $tabs[] = [
                'label' => $language,
                'active' => empty($active) && ($active = $model->hasErrors('content')
                        || $active = $model->hasErrors('subject')),
                'content' =>
                    $form->field($model, "subject[{$language}]", ['options' => ['class'=> 'subjectInput']])
                        ->textInput()
                    . $form->field($model, "content[{$language}]", ['options' => ['class'=> 'messageInput']])
                        ->widget(\vova07\imperavi\Widget::className(), [
                            'options' => ['style' => ['min-height' => '200px']],
                            'settings'   => [
                                'deniedTags' => [],
                                'convertDivs' => false,
                                'paragraphy' => false,
                                'formattingTags' => [],
                                'convertLinks' => false,
                                'cleanup' => false,
                                'removeEmptyTags' => false,
                                'cleanSpaces' => false,
                                'cleanFontTag' => false,
                                'tidyHtml' => false,
                                'paragraphize'             => false,
                                'replaceDivs'              => false,
                                'replaceTags'              => false,
                                'replaceStyles'            => false,
                                'removeEmpty'              => false,
                                'minHeight'                => 200,
                            ]
                        ])
            ];
        } ?>
        <?= \yii\bootstrap\Tabs::widget(['items' => $tabs]) ?>

        <label><?= Yii::t('modules/template', 'Variables') ?></label>
        <div class="configVariables">
            <?php echo DetailView::widget([
                'model'         => false,
                'attributes'    => $model->variables(),
            ]);?>
        </div>

        <div class="form-group text-right">
            <?php echo Html::submitButton($model->isNewRecord ? Yii::t('modules/template', 'Create') : Yii::t('modules/template', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>

    <?php ActiveForm::end(); ?>

</div>
