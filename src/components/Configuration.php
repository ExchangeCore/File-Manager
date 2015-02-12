<?php
namespace exchangecore\filemanager\components;

use Yii;

class Configuration extends \yii\base\Component
{
    protected $configuration = [];
    protected $path;

    /**
     * @param string $path The path to the configuration file to be loaded
     * @return bool
     */
    public function loadConfiguration($path = '@app/config/main.php')
    {
        $path = Yii::getAlias($path);
        $this->path = $path;
        if(file_exists($path)) {
            $this->configuration = require($this->path);
            return true;
        } else {
            return false;
        }
    }

    /**
     * Creates a configuration file at the specified path with the provided configuration value.
     *
     * @param string $path
     * @param array $config
     * @return bool Returns true if the configuration file was created successfully, false if not
     */
    public function createConfiguration($path =  '@app/config/main.php', $config = [])
    {
        $path = Yii::getAlias($path);
        $this->path = $path;
        $this->configuration = $config;
        return $this->writeConfiguration();
    }

    protected function writeConfiguration()
    {
        return file_put_contents($this->path, "<?php\nreturn " . var_export($this->configuration, true) . ';') !== false;
    }
}