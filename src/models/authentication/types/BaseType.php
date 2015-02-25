<?php
namespace exchangecore\filemanager\models\authentication\types;

use yii\db\ActiveRecord;
use exchangecore\filemanager\models\User;
use exchangecore\filemanager\models\authentication\UserAuthenticationType;

/**
 * @property User $user
 */
abstract class BaseType extends ActiveRecord
{

    /**
     * @param array $authInfo An associative array of authentication information used for
     * @return
     */
    abstract public function authenticate($authInfo);

    /**
     * @return User
     */
    public function getUser()
    {
        $this->hasOne(User::className(), ['UserID' => 'UserID'])
            ->viaTable(UserAuthenticationType::className(), ['AuthenticationTypeUserID' => 'AuthenticationTypeUserID']);
    }

    public function getUserAuthentication()
    {
        $this->hasOne(UserAuthenticationType::className(), ['AuthenticationTypeUserID' => 'AuthenticationTypeUserID'])
            ->inverseOf('authentication');
    }
} 