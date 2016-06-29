<?php
/**
 * TemplateBootstrap class file.
 * @copyright (c) 2016, Pavel Bariev
 * @license http://www.opensource.org/licenses/bsd-license.php
 */

namespace bariew\templateAbstractModule;

use bariew\templateAbstractModule\models\Config;
use yii\base\BootstrapInterface;
use yii\base\Event;

/**
 * Description.
 *
 * Usage:
 * @author Pavel Bariev <bariew@yandex.ru>
 *
 */
class TemplateBootstrap implements BootstrapInterface
{
    /**
     * @inheritdoc
     */
    public function bootstrap($app)
    {
        /** @var Config $configClass */
        $configClass = TemplateModule::getNamespace() . '\\models\\Config';
        if (!$app->db->schema->getTableSchema($configClass::tableName())) {
            return;
        }
        $configs = $configClass::find()
            ->select(['model_class', 'model_event'])
            ->asArray()
            ->all();
        $handler = [$configClass, 'handleEvent'];
        foreach ($configs as $data) {
            list($className, $eventName) = array_values($data);
            Event::on($className, $eventName, $handler);
        }
    }
}