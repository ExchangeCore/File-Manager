<?php

namespace exchangecore\filemanager\models\install;

use yii\base\Model;
use Yii;
use yii\db\Connection;
use yii\db\Exception;

class DbConfig extends Model
{
    public $types = [
        'mssql' => 'Microsoft SQL',
        'odbc-mssql' => 'Microsoft SQL (ODBC)',
        'mysql' => 'MySQL',
        'pgsql' => 'Postgres',
        'sqlite' => 'SQLite',
    ];

    public $type = 'mysql';
    public $host;
    public $database;
    public $username;
    public $password;

    public function rules()
    {
        return [
            [['type', 'host', 'database', 'username', 'password'], 'required'],
            ['type', 'in', 'range' => array_keys($this->types)],
            ['host', 'validateCanConnect']
        ];
    }

    public function attributeLabels()
    {
        return [
            'type' => Yii::t('core', 'Connection Type'),
            'host' => Yii::t('core', 'Host'),
            'database' => Yii::t('core', 'Database Name'),
            'username' => Yii::t('core', 'Username'),
            'password' => Yii::t('core', 'Password'),
        ];
    }

    public function validateCanConnect()
    {
        if (!$this->hasErrors()) {
            $db = new Connection($this->buildConnectionParams());
            try {
                $db->open();
                $db->close();
            } catch (Exception $e) {
                $this->addError('host',
                    Yii::t('app', 'The database connection is not valid: {error}', ['error' => $e->getMessage()]));
            }
        }
    }
    
    protected function buildConnectionParams()
    {
        $connectionParams = [];
        if($pos = strpos($this->type, 'odbc') === 0) {
            $connectionParams['dsn'] = 'odbc:' . $this->host;
            $connectionParams['driverName'] = substr($this->type, 5);
        } else {
            $connectionParams['dsn'] = $this->type . ':host=' . $this->host . ';dbname=' . $this->database;
        }
        $connectionParams['username'] = $this->username;
        $connectionParams['password'] = $this->password;
        $connectionParams['charset'] = 'utf8';

        return $connectionParams;
    }
}