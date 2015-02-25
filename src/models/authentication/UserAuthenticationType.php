<?php

namespace exchangecore\filemanager\models\authentication;

use Yii;
use exchangecore\filemanager\models\User;

/**
 * This is the model class for table "UserAuthenticationType".
 *
 * @property string $UserAuthenticationID
 * @property integer $UserID
 * @property integer $AuthenticationTypeID
 * @property integer $AuthenticationTypeUserID
 * @property resource $LastLoginIP
 * @property string $LastLoginDateTime
 *
 * @property AuthenticationType $authenticationType
 * @property User $user
 * @property \exchangecore\filemanager\models\authentication\types\BaseType $authentication
 */
class UserAuthenticationType extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'UserAuthenticationType';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['UserID', 'AuthenticationTypeID', 'AuthenticationTypeUserID'], 'integer'],
            [['LastLoginIP'], 'string'],
            [['LastLoginDateTime'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'UserAuthenticationID' => Yii::t('core', 'User Authentication ID'),
            'UserID' => Yii::t('core', 'User ID'),
            'AuthenticationTypeID' => Yii::t('core', 'Authentication Type ID'),
            'AuthenticationTypeUserID' => Yii::t('core', 'Authentication Type User ID'),
            'LastLoginIP' => Yii::t('core', 'Last Login Ip'),
            'LastLoginDateTime' => Yii::t('core', 'Last Login Date Time'),
        ];
    }

    /**
     * @return AuthenticationType
     */
    public function getAuthenticationType()
    {
        return $this->hasOne(AuthenticationType::className(), ['AuthenticationTypeID' => 'AuthenticationTypeID']);
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['UserID' => 'UserID'])->inverseOf('UserAuthenticationTypes');
    }

    /**
     * This will return the associated authentication type model
     * @return \exchangecore\filemanager\models\authentication\types\BaseType
     */
    public function getAuthentication()
    {
        /** @var \exchangecore\filemanager\models\authentication\types\BaseType $class */
        $class = '\\exchangecore\\filemanager\\models\\authentication\\types\\' . $this->authenticationType->Name;
        return $this->hasOne($class::className(), ['AuthenticationTypeUserID' => 'AuthenticationTypeUserID'])
            ->inverseOf('userAuthentication');
    }
}
