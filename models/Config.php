<?php
/**
 * Config class file.
 * @copyright (c) 2016, Pavel Bariev
 * @license http://www.opensource.org/licenses/bsd-license.php
 */

namespace bariew\templateAbstractModule\models;

use bariew\abstractModule\models\AbstractModel;
use bariew\yii2Tools\helpers\ClassHelper;
use bariew\yii2Tools\behaviors\SerializeBehavior;
use kartik\mpdf\Pdf;
use Yii;
use yii\base\Event;
use yii\db\ActiveRecord;
use yii\helpers\Inflector;

/**
 * Description.
 *
 * Usage:
 * @property integer $id
 * @property integer $type
 * @property integer $owner_id
 * @property string $address
 * @property array $subject
 * @property array $content
 * @property string $model_class
 * @property string $model_event
 *
 * @author Pavel Bariev <bariew@yandex.ru>
 *
 */
class Config extends AbstractModel
{
    const TYPE_EMAIL = 1;
    const TYPE_SMS = 2;
    const TYPE_PDF = 3;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['content', 'subject', 'model_class', 'model_event', 'type'], 'required'],
            [['model_class', 'model_event', 'address'], 'string', 'max' => 255],
            ['type', 'default', 'value' => static::TYPE_EMAIL],
            [['content', 'subject'], 'safe'],
            ['address', 'required', 'when' => function($data){return in_array($data->type, [
                static::TYPE_EMAIL, static::TYPE_SMS
            ]);}],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'          => Yii::t('modules/template', 'ID'),
            'type'        => Yii::t('modules/template', 'Type'),
            'address'     => Yii::t('modules/template', 'Address'),
            'subject'     => Yii::t('modules/template', 'Subject'),
            'content'     => Yii::t('modules/template', 'Content'),
            'model_class' => Yii::t('modules/template', 'Model Name'),
            'model_event' => Yii::t('modules/template', 'Model Event'),
            'owner_id'    => Yii::t('modules/template', 'Owner'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => SerializeBehavior::className(),
                'attributes' => ['subject', 'content']
            ]
        ];
    }

    /**
     * @return array
     */
    public function modelEventList()
    {
        return ($this->model_class)
            ? array_flip(ClassHelper::getEventNames($this->model_class))
            : [];
    }

    /**
     * @return array
     */
    public function modelClassList()
    {
        $classes = ClassHelper::getAllClasses();

        return array_combine($classes, $classes);
    }

    /**
     * @return array
     */
    public function typeList()
    {
        return [
            static::TYPE_EMAIL  => Yii::t('modules/template', 'Email'),
            static::TYPE_SMS    => Yii::t('modules/template', 'Sms'),
            static::TYPE_PDF    => Yii::t('modules/template', 'Pdf'),
        ];
    }

    public function languageList()
    {
        return [Yii::$app->language => Yii::$app->language];
    }

    /**
     * @param Event $event
     * @return \yii\db\ActiveQuery
     */
    public static function findForEvent(Event $event)
    {
        /** @var \yii\db\ActiveRecord $sender */
        $sender = $event->sender;
        /** @var static $config */
        return static::find()->andWhere([
            'model_event' => $event->name,
            'model_class' => get_class($sender),
            'owner_id' => $sender->getAttribute('owner_id'),
        ]);
    }

    /**
     * @param ActiveRecord|bool $model
     * @return array
     */
    public function variables($model = false)
    {
        $result = [];
        $variables = [
            '{{site_url}}'  => Yii::$app->request->hostInfo,
            '{{site_name}}' => Yii::$app->name,
            '{{admin_email}}' => @Yii::$app->params['adminEmail'],
        ];
        if ($model || $this->model_class) {
            /** @var ActiveRecord $sender */
            $sender = $model ? : new $this->model_class();
            foreach ($sender->attributeLabels() as $attribute => $label) {
                $variables["{{{$attribute}}}"] = $model
                    ? @$model->$attribute
                    : $label;
            }
        }

        foreach ($variables as $label => $value) {
            $model ? ($result[$label] = $value) : ($result[] = compact('label', 'value'));
        }
        return $result;
    }

    /**
     * sends email content
     * @return bool whether email is sent
     */
    public function email()
    {
        return \Yii::$app->mailer->compose()
            ->setFrom(\Yii::$app->params['adminEmail'])
            ->setTo($this->address)
            ->setSubject($this->subject)
            ->setHtmlBody($this->content)
            ->send();
    }

    /**
     * sends sms
     * @return bool
     */
    public function sms()
    {
        return false;
    }

    /**
     * sends odf file
     * @param array $options
     * @return Pdf
     */
    public function pdf($options = [])
    {
        return (new Pdf(array_merge_recursive([
            // set to use core fonts only
            'mode' => Pdf::MODE_UTF8,
            // A4 paper format
            'format' => Pdf::FORMAT_A4,
            // portrait orientation
            'orientation' => Pdf::ORIENT_PORTRAIT,
            // stream to browser inline
            'destination' => Pdf::DEST_DOWNLOAD,
            // your html content input
            'content' => $this->content,
            // set mPDF properties on the fly
            'options' => ['title' => $this->subject],
            'filename'=>  Inflector::slug($this->subject) . '.pdf',

            'marginLeft' => 0,
            'marginRight' => 0,
            'marginTop' => 0,
            'marginBottom' => 0,
            'marginHeader' => 0,
            'marginFooter' => 0,
        ], $options)))->render();
    }

    public static function handleEvent(Event $event)
    {
        /** @var \yii\db\ActiveRecord $sender */
        $sender = $event->sender;
        /** @var static $config */
        foreach (static::findForEvent($event)->all() as $config) {
            $variables = array_filter($config->variables($sender), function($v){
                return !is_array($v) && !is_object($v);
            });
            foreach (['address', 'subject', 'content'] as $attribute) {
                $value = (is_array($config->$attribute)
                    ? (@$config->{$attribute}[Yii::$app->language] ?: reset($config->$attribute))
                    : $config->$attribute);
                $config->$attribute = str_replace(array_keys($variables), array_values($variables), $value);
            }
            switch ($config->type) {
                case static::TYPE_EMAIL:
                    $config->email();
                    break;
                case static::TYPE_SMS:
                    $config->sms();
                    break;
                case static::TYPE_PDF;
                    $config->pdf(); Yii::$app->end();
                    break;
            }
        }

    }
}
