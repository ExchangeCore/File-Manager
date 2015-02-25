<?php

namespace exchangecore\filemanager\models\authentication\types;

use Yii;
use yii\base\Model;

class LocalLoginForm extends Model
{

    public $username;
    public $password;
    public $rememberMe = true;

    protected $user;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username', 'password'], 'required'],
            ['password', 'validatePassword'],
            ['rememberMe', 'boolean'],
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getAuthenticationAccount();
            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, 'Incorrect username or password.');
            }
        }
    }

    /**
     * Logs in a user using the provided username and password.
     * @return boolean whether the user is logged in successfully
     */
    public function login()
    {
        if ($this->validate()) {
            $this->getAuthenticationAccount()->getUser()->currentAuthUser = $this->getAuthenticationAccount();
            return Yii::$app->user->login($this->getAuthenticationAccount()->getUser(), $this->rememberMe ? 3600*24*30 : 0);
        } else {
            return false;
        }
    }

    /**
     * Finds local user by [[username]]
     *
     * @return Local|null
     */
    public function getAuthenticationAccount()
    {
        if ($this->user === false) {
            $this->user = Local::findByUsername($this->username);
        }
        return $this->user;
    }
}
