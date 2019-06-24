<?php

namespace Workers\Core;

use MongoDB\Client;
use Workers\Abstracts\AbstractSingleton;
use Workers\Extras\Logger;

/**
 * Class Connection
 * @package Workers\Core
 */
class Connection extends AbstractSingleton {
    private static $connection;
    private $dbConnection;

    /**
     * create a new connection if there isn't
     *
     * @param $callback
     * @return mixed
     */
    public static function get($callback) {
        if (static::$connection === NULL) {
            static::$connection = new static;
            static::$connection->dbConnection = $callback();
        }

        return static::$connection;
    }

    public static function connect($connection = 'mongo') {
        return static::get(function() use ($connection) {
            $conn = null;

            switch(app("$connection.driver")) {
                case "mongodb":
                    $conn =  new Client(
                        sprintf(
                            '%1$s://%2$s:%3$s@%4$s:%5$s',
                            app("$connection.driver"),
                            app("$connection.user"),
                            app("$connection.pass"),
                            app("$connection.host"),
                            app("$connection.port")
                        )
                    );
                    break;
                case "pgsql":
                case "mysql":
                $conn = new \PDO(
                    $this->nonMongoConnectInformation(
                        '%1$s:host=%2$s;dbname=%3$s;port=%4$s',
                        $connection
                    ),
                    app("$connection.user"),
                    app("$connection.pass")
                );
                break;
                case "sqlsrv":
                    $conn = new \PDO(
                        $this->nonMongoConnectInformation(
                            '%1$s:Server=%2$s,%4$s;Database=%3$s',
                            $connection
                        ),
                        app("$connection.user"),
                        app("$connection.pass")
                    );
                    break;
                case "odbc":
                    $conn = new \PDO(
                        $this->nonMongoConnectInformation(
                            '%1$s:Driver={SQL Server};Server=%2$s,%4$s;Database=%3$s',
                            $connection
                        ),
                        app("$connection.user"),
                        app("$connection.pass")
                    );
                    break;
                default:
                    Logger::emergency('Specified driver for the connection is not valid.');
                    throw new \InvalidArgumentException('Specified driver for the connection is not valid.');
            }

            return $conn;
        })->dbConnection;
    }

    public function __call($name, $arguments)
    {
        call_user_func_array(static::$connection->$name, $arguments);
    }

    /**
     * @param $connect
     * @param string $connection
     * @return string
     */
    protected function nonMongoConnectInformation($connect, string $connection): string
    {
        return sprintf(
            $connect,
            app("$connection.driver"),
            app("$connection.host"),
            app("$connection.db"),
            app("$connection.port")
        );
    }
}