<?php
use bariew\templateAbstractModule\models\Config;

class m140515_073531_template_config extends \yii\db\Migration
{
    public function up()
    {
        $this->createTable(Config::tableName(), array(
            'id'            => $this->primaryKey(),
            'type'          => $this->smallInteger(),
            'address'       => $this->string(),
            'subject'       => $this->text(),
            'content'       => $this->text(),
            'model_class'   => $this->string(),
            'model_event'   => $this->string(),
            'owner_id'      => $this->integer(),
        ));
    }

    public function down()
    {
        $this->dropTable(Config::tableName());
    }
}