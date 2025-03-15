<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Webinar;

/**
 * WebinarSearch represents the model behind the search form about `app\models\Webinar`.
 */
class WebinarSearch extends Webinar
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['event_id'], 'integer'], // Event ID should be integer
            [['name', 'description', 'webinar_key'], 'safe'], // Name and description are string fields that can be searched as "safe"
        ];
    }

    /**
     * Creates data provider instance with search query applied.
     *
     * @param array $params The parameters passed to the search.
     * @return ActiveDataProvider The data provider instance
     */
    public function search($params)
    {
        // Create the query with the Webinar model
        $query = Webinar::find();

        // Create a data provider instance for pagination and sorting
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 20,  // Number of items per page
            ],
            'sort' => [
                'defaultOrder' => [
                    'event_id' => SORT_DESC, // Default sorting by event_id in descending order
                ],
            ],
        ]);

        // Load the search parameters and validate the data
        $this->load($params);

        // If the model is not valid, no further filtering should be done
        if (!$this->validate()) {
            return $dataProvider;
        }

        // Add filtering conditions for the search fields
        $query->andFilterWhere([
            'event_id' => $this->event_id, // Filter by event_id if provided
        ]);

        $query->andFilterWhere(['like', 'name', $this->name]) // Filter by name
            ->andFilterWhere(['like', 'description', $this->description]); // Filter by description

        return $dataProvider;
    }
}
