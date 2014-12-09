<?php
namespace dnocode\awsddb;

use yii\base\Component;
use yii\db\Exception;
use yii\helpers\Inflector;

/**
 * The ddb connection class is used to establish a connection to a DynamoDbServer
 *
 * By default it assumes there is a dynamo db  server running on localhost at port 8000 and uses the database number 0.
 *
 *
 * @property string $driverName Name of the DB driver. This property is read-only.
 * @property boolean $isActive Whether the DB connection is established. This property is read-only.
 * @property LuaScriptBuilder $luaScriptBuilder This property is read-only.
 *
 * @author Carsten Brandt <mail@cebe.cc>
 * @since 2.0
 */
class Connection extends Component
{
    /**
     * @event Event an event that is triggered after a DB connection is established
     */
    const EVENT_AFTER_OPEN = 'afterOpen';

    /**
     * @var string the hostname or ip address to use for connecting to the ddb server. Defaults to 'localhost'.
     *
     */
    public $hostname = 'localhost';
    /**
     * @var integer the port to use for connecting to the dynamodb server. Default port is 8000.
     */
    public $port = 8000;

    /**
     * @var float timeout to use for connection to dynamodb. If not set the timeout set in php.ini will be used: ini_get("default_socket_timeout")
     */
    public $connectionTimeout = null;
  /**
     * Closes the connection when this component is being serialized.
     * @return array
     */
    public function __sleep()
    {
        $this->close();

        return array_keys(get_object_vars($this));
    }


    /**
     * Establishes a DB connection.
     * It does nothing if a DB connection has already been established.
     * @throws Exception if connection fails
     */
    public function open()
    {

    }

    /**
     * Closes the currently active DB connection.
     * It does nothing if the connection is already closed.
     */
    public function close()
    {

    }

    /**
     * Initializes the DB connection.
     * This method is invoked right after the DB connection is established.
     * The default implementation triggers an [[EVENT_AFTER_OPEN]] event.
     */
    protected function initConnection()
    {
        $this->trigger(self::EVENT_AFTER_OPEN);
    }

    /**
     * Returns the name of the DB driver for the current [[dsn]].
     * @return string name of the DB driver
     */
    public function getDriverName()
    {
        return 'ddb';
    }





}
