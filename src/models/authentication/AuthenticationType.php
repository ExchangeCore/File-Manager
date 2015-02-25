<?php

namespace exchangecore\filemanager\models\authentication;

use Yii;

/**
 * This is the model class for table "AuthenticationType".
 *
 * @property integer $AuthenticationTypeID
 * @property string $Name
 * @property string $Handle
 * @property string $Description
 * @property integer $IsEnabled
 * @property integer $Order
 *
 * @property UserAuthenticationType[] $userAuthenticationTypes
 */
class AuthenticationType extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'AuthenticationType';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['Description'], 'string'],
            [['IsEnabled', 'Order'], 'integer'],
            [['Name'], 'string', 'max' => 255],

            [['Name','Handle','IsEnabled'], 'required'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'AuthenticationTypeID' => Yii::t('core', 'Authentication Type ID'),
            'Name' => Yii::t('core', 'Name'),
            'Description' => Yii::t('core', 'Description'),
            'IsEnabled' => Yii::t('core', 'Is Enabled'),
            'Order' => Yii::t('core', 'Order'),
        ];
    }

    /**
     * @return self[]
     */
    public static function findEnabledAuthenticationTypes()
    {
        return self::find()
            ->where('IsEnabled = 1')
            ->orderBy('Order')
            ->all();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserAuthenticationTypes()
    {
        return $this->hasMany(UserAuthenticationType::className(), ['AuthenticationTypeID' => 'AuthenticationTypeID']);
    }
}
