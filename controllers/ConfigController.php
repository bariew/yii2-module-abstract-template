<?php
/**
 * ConfigController class file.
 */

namespace bariew\templateAbstractModule\controllers;

use bariew\abstractModule\controllers\AbstractModelController;
use Yii;
use bariew\templateAbstractModule\models\Config;
use yii\web\Response;
use yii\widgets\DetailView;

/**
 * ConfigController implements the CRUD actions for Config model.
 */
class ConfigController extends AbstractModelController
{
    /**
     * Returns json event list for form DepDrop widget.
     */
    public function actionEvents()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        /** @var Config $model */
        $model = $this->findModel();
        $model->model_class = Yii::$app->request->post('depdrop_parents')[0];
        $result = $model->modelEventList();
        $output = [];
        foreach ($result as $id => $name) {
            $output[] = compact('id', 'name');
        }
        return ['output' => $output, 'selected' => ''];
    }

    /**
     *
     */
    public function actionModelVariables()
    {
        /** @var Config $model */
        $model = $this->findModel();
        $model->load(Yii::$app->request->post());
        echo DetailView::widget([
            'model'         => false,
            'attributes'    => $model->variables(),
        ]);
    }
}
