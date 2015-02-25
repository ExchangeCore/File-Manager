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
 * @property string $Name
 *
 * @property UserAuthenticationType[] $userAuthenticationTypes
 * @property UserGroup[] $userGroups
 */
class User extends \yii\db\ActiveRecord implements IdentityInterface
{
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
            [['Name', 'AuthenticationKey'], 'string', 'max' => 255]
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
            'Name' => Yii::t('core', 'Name'),
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
}
