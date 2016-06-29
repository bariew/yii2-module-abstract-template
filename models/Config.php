<?php
/**
 * Config class file.
 * @copyright (c) 2016, Pavel Bariev
 * @license http://www.opensource.org/licenses/bsd-license.php
 */

namespace bariew\templateModule\models;

use bariew\abstractModule\models\AbstractModel;
use bariew\yii2Tools\helpers\ClassHelper;
use bariew\yii2Tools\behaviors\SerializeBehavior;
use Yii;
use yii\base\Event;
use yii\db\ActiveRecord;

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
                if (!isset($sender->$attribute)) {
                    continue;
                }
                $variables["{{{$attribute}}}"] = $model ? $model->$attribute : $label;
            }
        }

        foreach ($variables as $label => $value) {
            $model ? ($result[$label] = $value) : ($result[] = compact('label', 'value'));
        }
        return $result;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['content', 'subject', 'model_class', 'model_event', 'address', 'type'], 'required'],
            [['model_class', 'model_event', 'address'], 'string', 'max' => 255],
            ['type', 'default', 'value' => static::TYPE_EMAIL],
            [['content', 'subject'], 'safe']
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
     * prepares and sends email content
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

    public function sendSms()
    {
        return false;
    }

    public static function handleEvent(Event $event)
    {
        /** @var \yii\db\ActiveRecord $sender */
        $sender = $event->sender;
        /** @var static $config */
        foreach (static::findForEvent($event)->all() as $config) {
            $variables = $config->variables($sender);
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
                    $config->sendSms();
                    break;
            }
        }

    }
}
