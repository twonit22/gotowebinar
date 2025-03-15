<?php
namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class Webinar extends ActiveRecord
{
    public static function tableName()
    {
        return 'webinar';  // Ensure this matches your table name
    }

    // Override primaryKey() to specify event_id as the primary key
    public static function primaryKey()
    {
        return ['event_id'];  // Set event_id as the primary key
    }

    public function rules()
    {
        return [
            [['name', 'description'], 'required'],  // name and description are required
            [['description'], 'string'],  // description field validation
            [['name'], 'string', 'max' => 255],  // name field validation
            [['webinar_key'], 'string', 'max' => 50],
            // event_id is auto-incremented by the database, no need to validate it
        ];
    }

    // Optional: Override beforeSave() method if you want custom logic for event_id
    public function beforeSave($insert)
    {
        // event_id will be auto-incremented by the database, so we don't set it here
        if ($insert) {
            // We can add additional logic here if needed, but MySQL will auto-increment event_id
        }

        return parent::beforeSave($insert);
    }
}
