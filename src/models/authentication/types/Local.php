<?php

namespace exchangecore\filemanager\models\authentication\types;

use Yii;

/**
 * This is the model class for table "AuthenticationLocal".
 *
 * @property integer $UsersLocalID
 * @property string $Username
 * @property string $Password
 * @property string $LastPasswordChangeDateTime
 */
class Local extends BaseType
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'AuthenticationLocal';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['LastPasswordChangeDateTime'], 'safe'],
            [['Username', 'Password'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'AuthenticationTypeUserID' => Yii::t('core', 'Local User ID'),
            'Username' => Yii::t('core', 'Username'),
            'Password' => Yii::t('core', 'Password'),
            'LastPasswordChangeDateTime' => Yii::t('core', 'Last Password Change Date Time'),
        ];
    }
}
