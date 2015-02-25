<?php

namespace exchangecore\filemanager\models;

use Yii;

/**
 * This is the model class for table "Group".
 *
 * @property integer $GroupID
 * @property string $Name
 * @property string $Description
 * @property string $CreatedDateTime
 *
 * @property UserGroup[] $userGroups
 */
class Group extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'Group';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['Description'], 'string'],
            [['CreatedDateTime'], 'safe'],
            [['Name'], 'string', 'max' => 255],
            [['Name'], 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'GroupID' => Yii::t('core', 'Group ID'),
            'Name' => Yii::t('core', 'Name'),
            'Description' => Yii::t('core', 'Description'),
            'CreatedDateTime' => Yii::t('core', 'Created Date Time'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserGroups()
    {
        return $this->hasMany(UserGroup::className(), ['GroupID' => 'GroupID']);
    }
}
