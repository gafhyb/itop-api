# Basic PHP library to interact with an *iTop* instance

## Configuration
It uses *config.json* as configuration file.

You can either you plain text password :

    {
      "user" : "toto",
      "password" : "#G1bm2p!",
      "serverUrl" : "http://host/path"
    }

Or a system command to retrieve your (cryptic) password :

    {
      "user" : "toto",
      "password" : "`security find-generic-password -a thisisamacexample -w`",
      "serverUrl" : "http://host/path"
    }

You can define file's path with

    \gafhyb\iTop\API\Config::setConfigPath(__DIR__ . "/config-test.json");

## Composer
