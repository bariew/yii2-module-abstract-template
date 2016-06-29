<?php
/**
 * ConfigSearch class file.
 * @copyright (c) 2016, Pavel Bariev
 * @license http://www.opensource.org/licenses/bsd-license.php
 */

namespace bariew\templateModule\models;

use bariew\abstractModule\models\AbstractModelExtender;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * Description.
 *
 * Usage:
 *
 * @mixin Config
 * @author Pavel Bariev <bariew@yandex.ru>
 *
 */
class ConfigSearch extends AbstractModelExtender
{

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'type'], 'integer'],
            [['address', 'subject', 'content', 'model_class', 'model_event'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * @inheritdoc
     */
    public function search($params = [])
    {
        $query = parent::search();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'type' => $this->type,
        ]);

        $query->andFilterWhere(['like', 'address', $this->address])
            ->andFilterWhere(['like', 'subject', $this->subject])
            ->andFilterWhere(['like', 'content', $this->content])
            ->andFilterWhere(['like', 'model_class', $this->model_class])
            ->andFilterWhere(['like', 'model_event', $this->model_event]);

        return $dataProvider;
    }
}
