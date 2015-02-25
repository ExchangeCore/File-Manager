<?php

namespace exchangecore\filemanager\models;

use Yii;
use yii\base\NotSupportedException;
use yii\web\IdentityInterface;
use exchangecore\filemanager\models\authentication\UserAuthenticationType;

/**
 * This is the model class for table "User".
 *
 * @property integer $UserID
 * @property string $AuthenticationKey
 * @property string $LastAuthenticatedDateTime
 * @property string $UserName
 * @property string $FirstName
 * @property string $LastName
 * @property string $CreatedDateTime
 *
 * @property UserAuthenticationType[] $userAuthenticationTypes
 * @property UserGroup[] $userGroups
 */
class User extends \yii\db\ActiveRecord implements IdentityInterface
{
    public $currentAuthUser;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'User';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['Username'], 'string', 'max' => 30],
            [['FirstName', 'LastName'], 'string', 'max' => 255],

            [['Username'], 'required', 'message' => Yii::t('core', 'Please choose a username.')],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'UserID' => Yii::t('core', 'User ID'),
            'AuthenticationKey' => Yii::t('core', 'Authentication Key'),
            'LastAuthenticatedDateTime' => Yii::t('core', 'Last Authenticated'),
            'Username' => Yii::t('core', 'Username'),
            'FirstName' => Yii::t('core', 'First Name'),
            'LastName' => Yii::t('core', 'Last Name'),
            'Email' => Yii::t('core', 'Email'),
            'CreatedDateTime' => Yii::t('core', 'Account Created'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserAuthenticationTypes()
    {
        return $this->hasMany(UserAuthenticationType::className(), ['UserID' => 'UserID']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserGroups()
    {
        return $this->hasMany(UserGroup::className(), ['UserID' => 'UserID']);
    }

    /**
     * @param int $id
     * @return self
     */
    public static function findIdentity($id)
    {
        return self::findOne(['UserID' => $id]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not supported');
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->UserID;
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->AuthenticationKey;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() !== null && $this->getAuthKey() === $authKey;
    }

    /**
     * @return null|UserAuthenticationType
     */
    public function getCurrentAuthUser()
    {
        return $this->currentAuthUser;
    }

    /**
     * Sets the current identity's authentication user so we can use it later if needed
     * @param UserAuthenticationType $authType
     */
    public function setCurrentAuthUser(UserAuthenticationType $authType)
    {
        $this->currentAuthUser = $authType;
    }
}
