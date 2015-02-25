<?php

namespace exchangecore\filemanager\models;

use Yii;

/**
 * This is the model class for table "UserGroup".
 *
 * @property string $UserGroupID
 * @property integer $UserID
 * @property integer $GroupID
 * @property string $AddedDateTime
 *
 * @property User $user
 * @property Group $group
 */
class UserGroup extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'UserGroup';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['UserID', 'GroupID'], 'integer'],
            [['AddedDateTime'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'UserGroupID' => Yii::t('core', 'User Group ID'),
            'UserID' => Yii::t('core', 'User ID'),
            'GroupID' => Yii::t('core', 'Group ID'),
            'AddedDateTime' => Yii::t('core', 'Added Date Time'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['UserID' => 'UserID']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGroup()
    {
        return $this->hasOne(Group::className(), ['GroupID' => 'GroupID']);
    }
}
