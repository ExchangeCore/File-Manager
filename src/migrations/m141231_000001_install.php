<?php
use yii\db\Schema;

class m141231_000001_install extends \yii\db\Migration
{

    public function safeUp()
    {
        $this->createTable('users', [
            'uuid' => Schema::TYPE_STRING,
            'username' => SCHEMA::TYPE_STRING,
            'password' => SCHEMA::TYPE_STRING,
        ]);
    }

    public function safeDown()
    {
        $this->dropTable('users');
    }

} 