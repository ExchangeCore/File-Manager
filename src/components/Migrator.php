<?php

namespace exchangecore\filemanager\components;

use Yii;
use yii\base\Exception;
use yii\db\Connection;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;
use yii\log\Logger;

class Migrator extends \yii\base\component
{

    /**
     * The name of the dummy migration that marks the beginning of the whole migration history.
     */
    const BASE_MIGRATION = 'm000000_000000_base';

    /**
     * An array of output messages
     * @var array
     */
    protected $messages = [];

    /**
     * An array of error messages
     * @var array
     */
    protected $errors = [];

    /**
     * @var string the directory storing the migration classes. This can be either
     * a path alias or a directory.
     */
    public $migrationPath = '@app/migrations';

    /**
     * @var string the name of the table for keeping applied migration information.
     */
    public $migrationTable = '{{%migration}}';

    /**
     * @var Connection|string the DB connection object or the application
     * component ID of the DB connection.
     */
    public $db = 'db';

    /**
     * @return bool Returns true if there are errors present
     */
    public function hasErrors()
    {
        return !empty($this->errors);
    }

    /**
     * Returns an array of errors, false if no errors exist
     * @return array|bool
     */
    public function getErrors()
    {
        if($this->hasErrors()) {
            return $this->errors;
        }
        return false;
    }

    /**
     * Adds an error to the internal errors array
     * @param string $message
     */
    protected function addError($message)
    {
        $this->errors[] = $message;
    }
    /**
     * @return bool Returns true if there are errors present
     */
    public function hasMessages()
    {
        return !empty($this->messages);
    }

    /**
     * Returns an array of messages, false if no messages exist
     * @return array|bool
     */
    public function getMessages()
    {
        if($this->hasMessages()) {
            return $this->messages;
        }
        return false;
    }

    /**
     * Adds an message to the internal messages array
     * @param string $message
     */
    protected function addMessage($message)
    {
        $this->messages[] = $message;
    }

    /**
     * Function to run prior to the migration. Returns true if the migration can continue to run,
     * false if there was an problem which should prevent the migration from continuing
     */
    public function beforeMigration()
    {
        $path = Yii::getAlias($this->migrationPath);
        if (!is_dir($path)) {
            if(!FileHelper::createDirectory($path)){
                Yii::error('Could not create the directory ' . $path);
                return false;
            };
        }
        $this->migrationPath = $path;
        if (is_string($this->db)) {
            $this->db = Yii::$app->get($this->db);
        }
        if (!$this->db instanceof Connection) {
            throw new Exception("The 'db' option must refer to the application component ID of a DB connection.");
        }
        return true;
    }

    /**
     * Upgrades the application by applying new migrations.
     *
     * @param integer $limit the number of new migrations to be applied. If 0, it means
     * applying all available new migrations.
     *
     * @return bool Returns true if the upgrade was run successfully, false if there was an error
     */
    public function upgrade($limit = 0)
    {
        $migrations = $this->getNewMigrations();
        if (empty($migrations)) {
            $this->addMessage('No new migration found. Your system is up-to-date.');
            return true;
        }

        $total = count($migrations);
        $limit = (int) $limit;
        if ($limit > 0) {
            $migrations = array_slice($migrations, 0, $limit);
        }

        $n = count($migrations);
        if ($n === $total) {
            $this->addMessage("Total $n new " . ($n === 1 ? 'migration' : 'migrations') . " to be applied");
        } else {
            $this->addMessage("Total $n out of $total new " . ($total === 1 ? 'migration' : 'migrations') . " to be applied");
        }

        foreach ($migrations as $migration) {
            if (!$this->migrateUp($migration)) {
                $this->addError("Migration failed. The rest of the migrations are canceled.");

                return false;
            }
        }
        $this->addMessage("Migrated up successfully.");
        return true;
    }

    /**
     * Downgrades the application by reverting old migrations.
     *
     * @param integer|string $limit the number of migrations to be reverted. Defaults to 1,
     * meaning the last applied migration will be reverted. 'all' can be used to downgrade all steps
     * @throws Exception if the number of the steps specified is less than 1.
     *
     * @return bool Returns true if the upgrade was run successfully, false if there was an error
     */
    public function downgrade($limit = 1)
    {
        if ($limit === 'all') {
            $limit = null;
        } else {
            $limit = (int) $limit;
            if ($limit < 1) {
                throw new Exception("The step argument must be greater than 0.");
            }
        }

        $migrations = $this->getMigrationHistory($limit);

        if (empty($migrations)) {
            $this->addMessage("No migration to downgrade.");
            return true;
        }

        $migrations = array_keys($migrations);
        $n = count($migrations);
        $this->addMessage("Total $n " . ($n === 1 ? 'migration' : 'migrations') . " to be reverted");
        foreach ($migrations as $migration) {
            if (!$this->migrateDown($migration)) {
                $this->addError("Migration failed. The rest of the migrations are canceled.");
                return false;
            }
        }

        $this->addMessage("Migrated down successfully.");
        return true;
    }

    /**
     * Redoes the last few migrations.
     *
     * This command will first revert the specified migrations, and then apply them again.
     *
     * @param integer|string $limit the number of migrations to be redone. Defaults to 1,
     * meaning the last applied migration will be redone. 'all' can be used to redo everything
     * @throws Exception if the number of the steps specified is less than 1.
     *
     * @return bool Returns true if the upgrade was run successfully, false if there was an error
     */
    public function Redo($limit = 1)
    {
        if ($limit === 'all') {
            $limit = null;
        } else {
            $limit = (int) $limit;
            if ($limit < 1) {
                throw new Exception("The step argument must be greater than 0.");
            }
        }

        $migrations = $this->getMigrationHistory($limit);
        if (empty($migrations)) {
            $this->addMessage("No migration has been done before, nothing to redo.");
            return true;
        }

        $migrations = array_keys($migrations);
        $n = count($migrations);
        $this->addMessage("Total $n " . ($n === 1 ? 'migration' : 'migrations') . " to be redone");
        foreach ($migrations as $migration) {
            if (!$this->migrateDown($migration)) {
                $this->addError("Migration failed. The rest of the migrations are canceled.");
                return false;
            }
        }
        foreach (array_reverse($migrations) as $migration) {
            if (!$this->migrateUp($migration)) {
                $this->addError("Migration failed. The rest of the migrations migrations are canceled.");
                return false;
            }
        }
        $this->addMessage("Migration redone successfully.");
        return true;
    }

    /**
     * Upgrades or downgrades till the specified version.
     *
     * Can also downgrade versions to the certain apply time in the past by providing
     * a UNIX timestamp or a string parseable by the strtotime() function. This means
     * that all the versions applied after the specified certain time would be reverted.
     *
     * This command will first revert the specified migrations, and then apply
     * them again.
     *
     * @param string $version either the version name or the certain time value in the past
     * that the application should be migrated to. This can be either the timestamp,
     * the full name of the migration, the UNIX timestamp, or the parseable datetime
     * string.
     * @return int
     * @throws Exception if the version argument is invalid.
     */
    public function migrateTo($version)
    {
        if (preg_match('/^m?(\d{6}_\d{6})(_.*?)?$/', $version, $matches)) {
            return $this->migrateToVersion('m' . $matches[1]);
        } elseif ((string) (int) $version == $version) {
            return $this->migrateToTime($version);
        } elseif (($time = strtotime($version)) !== false) {
            return $this->migrateToTime($time);
        } else {
            throw new Exception("The version argument must be either a timestamp (e.g. 101129_185401),
             the full name of a migration (e.g. m101129_185401_create_user_table),
             a UNIX timestamp (e.g. 1392853000), or a datetime string parseable
             by the strtotime() function (e.g. 2014-02-15 13:00:50).");
        }
    }

    /**
     * Modifies the migration history to the specified version.
     *
     * No actual migration will be performed.
     *
     * @param string $version the version at which the migration history should be marked.
     * This can be either the timestamp or the full name of the migration.
     * @return boolean Returns true if the marking of the migrations was successful, false otherwise
     * @throws Exception if the version argument is invalid or the version cannot be found.
     */
    public function markMigration($version)
    {
        $originalVersion = $version;
        if (preg_match('/^m?(\d{6}_\d{6})(_.*?)?$/', $version, $matches)) {
            $version = 'm' . $matches[1];
        } else {
            throw new Exception("The version argument must be either a timestamp (e.g. 101129_185401)
            or the full name of a migration (e.g. m101129_185401_create_user_table).");
        }

        // try mark up
        $migrations = $this->getNewMigrations();
        foreach ($migrations as $i => $migration) {
            if (strpos($migration, $version . '_') === 0) {
                for ($j = 0; $j <= $i; ++$j) {
                    $this->addMigrationHistory($migrations[$j]);
                }
                $this->addMessage("The migration history is set at $originalVersion.No actual migration was performed.");
                return true;
            }
        }

        // try mark down
        $migrations = array_keys($this->getMigrationHistory(null));
        foreach ($migrations as $i => $migration) {
            if (strpos($migration, $version . '_') === 0) {
                if ($i === 0) {
                    $this->addMessage("Already at '$originalVersion'. Nothing needs to be done.");
                } else {
                    for ($j = 0; $j < $i; ++$j) {
                        $this->removeMigrationHistory($migrations[$j]);
                    }
                    $this->addMessage("The migration history is set at $originalVersion. No actual migration was performed.");
                }

                return true;
            }
        }

        throw new Exception("Unable to find the version '$originalVersion'.");
    }

    /**
     * Upgrades with the specified migration class.
     * @param string $class the migration class name
     * @return boolean whether the migration is successful
     */
    protected function migrateUp($class)
    {
        if(!$this->beforeMigration()) {
            return false;
        }
        if ($class === self::BASE_MIGRATION) {
            return true;
        }
        Yii::getLogger()->log("Applying $class\n", Logger::LEVEL_TRACE);
        $start = microtime(true);
        $migration = $this->createMigration($class);
        if ($migration->up() !== false) {
            $this->addMigrationHistory($class);
            $time = microtime(true) - $start;
            Yii::getLogger()->log("Applied $class (time: " . sprintf("%.3f", $time) . "s)\n\n", Logger::LEVEL_TRACE);
            return true;
        } else {
            $time = microtime(true) - $start;
            Yii::getLogger()->log("Failed to apply $class (time: " . sprintf("%.3f", $time) . "s)\n\n", Logger::LEVEL_TRACE);

            return false;
        }
    }

    /**
     * Downgrades with the specified migration class.
     * @param string $class the migration class name
     * @return boolean whether the migration is successful
     */
    protected function migrateDown($class)
    {
        if(!$this->beforeMigration()) {
            return false;
        }
        if ($class === self::BASE_MIGRATION) {
            return true;
        }

        Yii::getLogger()->log("Reverting $class\n", Logger::LEVEL_TRACE);
        $start = microtime(true);
        $migration = $this->createMigration($class);
        if ($migration->down() !== false) {
            $this->removeMigrationHistory($class);
            $time = microtime(true) - $start;
            Yii::getLogger()->log("Reverted $class (time: " . sprintf("%.3f", $time) . "s)\n\n", Logger::LEVEL_TRACE);

            return true;
        } else {
            $time = microtime(true) - $start;
            Yii::getLogger()->log("Failed to revert $class (time: " . sprintf("%.3f", $time) . "s)\n\n", Logger::LEVEL_TRACE);

            return false;
        }
    }

    /**
     * Migrates to the specified apply time in the past.
     * @param integer $time UNIX timestamp value.
     * @return bool Returns whether or not the migration was successful
     */
    protected function migrateToTime($time)
    {
        $count = 0;
        $migrations = array_values($this->getMigrationHistory(null));
        while ($count < count($migrations) && $migrations[$count] > $time) {
            ++$count;
        }
        if ($count === 0) {
            $this->addMessage("Nothing needs to be done.");
            return true;
        } else {
            return $this->migrateDown($count);
        }
    }

    /**
     * Migrates to the certain version.
     * @param string $version name in the full format.
     * @return bool Returns whether or not the migration to the specified version was successful
     * @throws Exception if the provided version cannot be found.
     */
    protected function migrateToVersion($version)
    {
        $originalVersion = $version;

        // try migrate up
        $migrations = $this->getNewMigrations();
        foreach ($migrations as $i => $migration) {
            if (strpos($migration, $version . '_') === 0) {
                $this->upgrade($i + 1);
                return true;
            }
        }

        // try migrate down
        $migrations = array_keys($this->getMigrationHistory(null));
        foreach ($migrations as $i => $migration) {
            if (strpos($migration, $version . '_') === 0) {
                if ($i === 0) {
                    $this->addMessage("Already at '$originalVersion'. Nothing needs to be done.");
                } else {
                    return $this->downgrade($i);
                }

                return true;
            }
        }

        throw new Exception("Unable to find the version '$originalVersion'.");
    }

    /**
     * Returns the migrations that are not applied.
     * @return array list of new migrations
     */
    public function getNewMigrations()
    {
        $applied = [];
        foreach ($this->getMigrationHistory(null) as $version => $time) {
            $applied[substr($version, 1, 13)] = true;
        }

        $migrations = [];
        $handle = opendir($this->migrationPath);
        while (($file = readdir($handle)) !== false) {
            if ($file === '.' || $file === '..') {
                continue;
            }
            $path = $this->migrationPath . DIRECTORY_SEPARATOR . $file;
            if (preg_match('/^(m(\d{6}_\d{6})_.*?)\.php$/', $file, $matches) && is_file($path) && !isset($applied[$matches[2]])) {
                $migrations[] = $matches[1];
            }
        }
        closedir($handle);
        sort($migrations);

        return $migrations;
    }

    /**
     * Creates a new migration instance.
     * @param string $class the migration class name
     * @return \yii\db\Migration the migration instance
     */
    protected function createMigration($class)
    {
        $file = $this->migrationPath . DIRECTORY_SEPARATOR . $class . '.php';
        require_once($file);

        return new $class(['db' => $this->db]);
    }

    /**
     * @inheritdoc
     */
    public function getMigrationHistory($limit)
    {
        if(!$this->beforeMigration()) {
            return false;
        }
        if ($this->db->schema->getTableSchema($this->migrationTable, true) === null) {
            $this->createMigrationHistoryTable();
        }
        $query = new Query;
        $rows = $query->select(['version', 'apply_time'])
            ->from($this->migrationTable)
            ->orderBy('version DESC')
            ->limit($limit)
            ->createCommand($this->db)
            ->queryAll();
        $history = ArrayHelper::map($rows, 'version', 'apply_time');
        unset($history[self::BASE_MIGRATION]);

        return $history;
    }

    /**
     * Creates the migration history table.
     */
    protected function createMigrationHistoryTable()
    {
        $tableName = $this->db->schema->getRawTableName($this->migrationTable);
        Yii::getLogger()->log("Creating migration history table \"$tableName\"...", Logger::LEVEL_TRACE);
        $this->db->createCommand()->createTable($this->migrationTable, [
                'version' => 'varchar(180) NOT NULL PRIMARY KEY',
                'apply_time' => 'integer',
            ])->execute();
        $this->db->createCommand()->insert($this->migrationTable, [
                'version' => self::BASE_MIGRATION,
                'apply_time' => time(),
            ])->execute();
        Yii::getLogger()->log("Migration history table created", Logger::LEVEL_TRACE);
    }

    /**
     * Adds new migration entry to the history.
     * @param string $version migration version name.
     */
    protected function addMigrationHistory($version)
    {
        $command = $this->db->createCommand();
        $command->insert($this->migrationTable, [
                'version' => $version,
                'apply_time' => time(),
            ])->execute();
    }

    /**
     * Removes existing migration from the history.
     * @param string $version migration version name.
     */
    protected function removeMigrationHistory($version)
    {
        $command = $this->db->createCommand();
        $command->delete($this->migrationTable, [
                'version' => $version,
            ])->execute();
    }

} 