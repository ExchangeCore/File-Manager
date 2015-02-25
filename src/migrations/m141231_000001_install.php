<?php
use yii\db\Schema;

class m141231_000001_install extends \yii\db\Migration
{

    public function safeUp()
    {
        try {

            $this->createTable(
                'User',
                [
                    'UserID' => Schema::TYPE_PK,
                    'AuthenticationKey' => Schema::TYPE_STRING,
                    'LastAuthenticatedDateTime' => Schema::TYPE_DATETIME,
                    'Username' => Schema::TYPE_STRING,
                    'FirstName' => Schema::TYPE_STRING,
                    'LastName' => Schema::TYPE_STRING,
                    'PrimaryEmail' => Schema::TYPE_STRING,
                    'CreatedDateTime' => Schema::TYPE_DATETIME,
                ]
            );

            $this->createIndex('Username', 'User', 'Username', true);

            $this->createTable(
                'AuthenticationType',
                [
                    'AuthenticationTypeID' => SCHEMA::TYPE_PK,
                    'Handle' => Schema::TYPE_STRING,
                    'Name' => Schema::TYPE_STRING,
                    'Description' => Schema::TYPE_TEXT,
                    'IsEnabled' => SCHEMA::TYPE_BOOLEAN,
                    'Order' => SCHEMA::TYPE_INTEGER
                ]
            );

            $this->insert(
                'AuthenticationType',
                [
                    'Name' => 'Local',
                    'Handle' => 'Local',
                    'Description' => 'The local database password authentication method',
                    'IsEnabled' => 1,
                    'Order' => 0,
                ]
            );

            $this->createIndex('Handle', 'AuthenticationType', 'Handle', true);
            $this->createIndex('Order', 'AuthenticationType', ['IsEnabled', 'Order'], false);


            $this->createTable(
                'UserAuthenticationType',
                [
                    'UserAuthenticationID' => Schema::TYPE_BIGPK,
                    'UserID' => SCHEMA::TYPE_INTEGER,
                    'AuthenticationTypeID' => SCHEMA::TYPE_INTEGER,
                    'AuthenticationTypeUserID' => SCHEMA::TYPE_INTEGER,
                    'LastLoginIP' => SCHEMA::TYPE_BINARY,
                    'LastLoginDateTime' => SCHEMA::TYPE_DATETIME,
                ]
            );

            $this->addForeignKey(
                'Fk_UserAuthenticationType_User',
                'UserAuthenticationType',
                'UserID',
                'User',
                'UserID',
                'RESTRICT',
                'CASCADE'
            );
            $this->addForeignKey(
                'Fk_UserAuthenticationType_Type',
                'UserAuthenticationType',
                'AuthenticationTypeID',
                'AuthenticationType',
                'AuthenticationTypeID',
                'RESTRICT',
                'CASCADE'
            );

            $this->createIndex(
                'AuthenticationTypeUserID',
                'UserAuthenticationType',
                'AuthenticationTypeUserID',
                false
            );

            $this->createTable(
                'AuthenticationLocal',
                [
                    'UsersLocalID' => Schema::TYPE_PK,
                    'Username' => Schema::TYPE_STRING,
                    'Password' => Schema::TYPE_STRING,
                    'AuthenticationKey' => Schema::TYPE_STRING,
                    'LastPasswordChangeDateTime' => Schema::TYPE_DATETIME,
                ]
            );

            $this->createTable(
                'Group',
                [
                    'GroupID' => Schema::TYPE_PK,
                    'Name' => Schema::TYPE_STRING,
                    'Description' => Schema::TYPE_TEXT,
                    'CreatedDateTime' => Schema::TYPE_DATETIME,
                ]
            );

            $this->createIndex('Name', 'Group', 'Name', true);

            $this->insert(
                'Group',
                [
                    'Name' => 'Administrators',
                    'Description' => 'The group who manages the system',
                    'CreatedDateTime' => (new DateTime())->format('Y-m-d H:i:s')
                ]
            );

            $this->createTable(
                'UserGroup',
                [
                    'UserGroupID' => Schema::TYPE_BIGPK,
                    'UserID' => Schema::TYPE_INTEGER,
                    'GroupID' => Schema::TYPE_INTEGER,
                    'AddedDateTime' => Schema::TYPE_DATETIME,
                ]
            );

            $this->addForeignKey(
                'Fk_UserGroup_Group',
                'UserGroup',
                'GroupID',
                'Group',
                'GroupID',
                'RESTRICT',
                'CASCADE'
            );

            $this->addForeignKey(
                'Fk_UserGroup_User',
                'UserGroup',
                'UserID',
                'User',
                'UserID',
                'RESTRICT',
                'CASCADE'
            );
        } catch(\Exception $e) {
            Yii::getLogger()->log($e, \yii\log\Logger::LEVEL_ERROR);
            return false;
        }
        return true;
    }

    public function safeDown()
    {
        $this->dropTable('User');
        $this->dropTable('AuthenticationType');
        $this->dropTable('UserAuthenticationType');
        $this->dropTable('AuthenticationLocal');
        $this->dropTable('UserGroup');
        $this->dropTable('Group');
    }

} 