<?php
namespace gafhyb\iTop\API;
/**
 * User: virgile
 * Date: 27/10/2016
 * Time: 09:35
 */
class Config
{
    /**
     * @var Config
     */
    private static $config;

    /**
     * @var string
     */
    private $user;
    /**
     * @var string
     */
    private $password;
    /**
     * @var string
     */
    private $serverUrl;

    /**
     * @var string
     */
    private static $configPath;

    /**
     * Config constructor.
     * @param string $user
     * @param string $password
     * @param string $serverUrl
     */
    public function __construct($user, $password, $serverUrl)
    {
        $this->user = $user;
        $this->password = $password;
        $this->serverUrl = $serverUrl;
    }

    /**
     * @return Config
     */
    public static function getConfig()
    {
        if (!isset(self::$config)) {
            $config = json_decode(file_get_contents(self::getConfigPath()), true);

            $password = $config["password"];

            if (strpos($password, "`") === 0) {
                $command = substr($password, 1, -1);
                exec($command, $op);
                $password=$op[0];
            }

            self::$config = new Config($config["user"], $password, $config["serverUrl"]);
        }
        return self::$config;
    }

    /**
     * @return string
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @return string
     */
    public function getServerUrl()
    {
        return $this->serverUrl;
    }

    /**
     * @return mixed
     */
    public static function getConfigPath()
    {
        if(isset(self::$configPath))
        return self::$configPath;
        return __DIR__ . "/config.json";
    }

    /**
     * @param mixed $configPath
     */
    public static function setConfigPath($configPath)
    {
        self::$configPath = $configPath;
    }
}