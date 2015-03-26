<?php

namespace kato\modules\media\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * MediaSearch represents the model behind the search form about `kato\modules\media\models\Media`.
 */
class MediaSearch extends Model
{
    public $id;
    public $title;
    public $filename;
    public $source;
    public $source_location;
    public $create_time;
    public $extension;
    public $mimeType;
    public $byteSize;
    public $status;
    public $media_type;

    public function rules()
    {
        return [
            [['id', 'byteSize', 'status'], 'integer'],
            [['title', 'filename', 'source', 'source_location', 'create_time', 'extension', 'mimeType', 'media_type'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'filename' => 'Filename',
            'source' => 'Source',
            'source_location' => 'Source Location',
            'create_time' => 'Create Time',
            'extension' => 'Extension',
            'mimeType' => 'Mime Type',
            'byteSize' => 'Byte Size',
            'status' => 'Status',
            'media_type' => 'Media Type',
        ];
    }

    public function search($params)
    {
        $query = Media::find();
        if (!isset($_GET['sort'])) {
            $query->orderBy('id DESC');
        }
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'create_time' => $this->create_time,
            'byteSize' => $this->byteSize,
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'filename', $this->filename])
            ->andFilterWhere(['like', 'source', $this->source])
            ->andFilterWhere(['like', 'source_location', $this->source_location])
            ->andFilterWhere(['like', 'extension', $this->extension])
            ->andFilterWhere(['like', 'mimeType', $this->mimeType])
            ->andFilterWhere(['like', 'media_type', $this->media_type]);

        return $dataProvider;
    }

    protected function addCondition($query, $attribute, $partialMatch = false)
    {
        if (($pos = strrpos($attribute, '.')) !== false) {
            $modelAttribute = substr($attribute, $pos + 1);
        } else {
            $modelAttribute = $attribute;
        }

        $value = $this->$modelAttribute;
        if (trim($value) === '') {
            return;
        }
        if ($partialMatch) {
            $query->andWhere(['like', $attribute, $value]);
        } else {
            $query->andWhere([$attribute => $value]);
        }
    }
}
