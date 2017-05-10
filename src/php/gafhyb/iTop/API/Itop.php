<?php
namespace gafhyb\iTop\API;
/**
 * User: virgile
 * Date: 27/10/2016
 * Time: 09:47
 */

class Itop
{
    /**
     * @var string
     */
    private static $urlSuffix = "/webservices/rest.php?version=1.0";

    /**
     * Gets objects
     * @param $className string class of objects to find
     * @param $key string query
     * @param $outputFields string wanted fields
     * @return string iTop response
     */
    public static function get($className, $key, $outputFields = "*")
    {
        return self::exec("core/get", $className, $key, null, $outputFields, null);
    }

    /**
     * @param string $className class of new instance
     * @param string $key query
     * @param array $values values for fields
     * @return boolean|stdClass
     */
    public static function findOrCreate($className, $key, $values)
    {
        $result = Itop::get($className, $key);
        $objects = json_decode($result)->{"objects"};

        $nbObjects = sizeof(array_keys((array)$objects));

        if ($nbObjects > 1) {
            echo "Too much $className for query : $key";
            return false;
        } else {
            if ($nbObjects < 1) {
                $result = Itop::create($className, $values, "*");
                $jsonCreate = json_decode($result);
                $returnCode = $jsonCreate->{'code'};
                if ($returnCode != 0) {
                    echo "\n" . $jsonCreate->{'message'};
                } else {
                    echo " - Created\n";
                }

                if ($returnCode == 0) {
                    $result = Itop::get($className, $key);
                    $objects = json_decode($result)->{"objects"};
                    $object = reset($objects);
                }
            } else {
                echo "Found $className\n";
                $object = reset($objects);
            }
        }
        return $object;
    }

    /**
     * Creates object
     * @param string $className class of new instance
     * @param array $fields values for fields
     * @param string $outputFields
     * @param string $comment
     * @return string iTop response
     */
    public
    static function create($className, $fields, $outputFields = "*", $comment = "")
    {
        echo "Will try to create $className";

        return self::exec("core/create", $className, null, $fields, $outputFields, $comment);
    }

    /**
     * Updates object
     * @param string $className class of the instance tu update
     * @param string $key key of instance
     * @param array $fields values for fields
     * @param string $outputFields
     * @param string $comment
     * @return string iTop response
     */
    public
    static function update($className, $key, $fields, $outputFields = "*", $comment = "")
    {
        return self::exec("core/update", $className, $key, $fields, $outputFields, $comment);
    }

    /**
     * Deletes objects
     * @param $className string class of objects to find
     * @param $key string query
     * @return string iTop response
     */
    public static function delete($className, $key)
    {
        return self::exec("core/delete", $className, $key, null, null, null);
    }

    /**
     * Executes a query
     * @param string $operation name of operation
     * @param string $className name of class to query
     * @param string $key
     * @param string $fields
     * @param string $outputFields
     * @param $comment
     * @return string iTop response
     */
    private
    static function exec($operation, $className, $key, $fields = "*", $outputFields = "*", $comment)
    {
        $url = Config::getConfig()->getServerUrl() . self::$urlSuffix;

        $vars = array();
        $vars["operation"] = $operation;
        $vars["class"] = $className;
        if (isset($key)) $vars["key"] = $key;
        $vars["output_fields"] = $outputFields;
        if (isset($fields)) $vars["fields"] = $fields;
        $c = Config::getConfig()->getUser() . " (API)";
        if (isset($comment) && trim($comment) != "") {
            $c = $comment . " - " . $c;
        }
        $vars["comment"] = $c;

        $jsonData = json_encode($vars);

        //var_dump($jsonData);

        $data = array('auth_user' => Config::getConfig()->getUser(), 'auth_pwd' => Config::getConfig()->getPassword(), 'json_data' => $jsonData);

        // use key 'http' even if you send the request to https://...
        $options = array(
            'http' => array(
                'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                'method' => 'POST',
                'content' => http_build_query($data)
            )
        );
        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        if ($result === FALSE) { /* Handle error */
        }

        return $result;
    }
}