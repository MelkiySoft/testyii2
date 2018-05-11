<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "twitt_user".
 *
 * @property int $id
 * @property string $name
 * @property string $date_add
 * @property string $date_view
 */
class TwittUser extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'twitt_user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['date_add', 'date_view'], 'safe'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'date_add' => 'Date Add',
            'date_view' => 'Date View',
        ];
    }
}
