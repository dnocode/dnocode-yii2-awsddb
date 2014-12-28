<?php
defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'dev');
define('YII_ENABLE_ERROR_HANDLER',false);
require dirname(__DIR__) . '/vendor/autoload.php';
require  dirname(__DIR__) .'/vendor/yiisoft/yii2/Yii.php';
require_once 'PHPUnit/TextUI/TestRunner.php';
use Aws\CloudFront\Exception\Exception;
use dnocode\awsddb\ar\Connection;
use yii\web\Application;

$tableExist = true;
/**remember to start dynamo db in local ***/
$config = require(dirname(__DIR__) . '/test/config/web.php');

(new Application($config));

/** @var  Connection $connection */
$connection = \Yii::$app->get("ddb");
/**creating table for test**/
$tableElement = array('TableName' => "element", 'AttributeDefinitions' => array(array('AttributeName' => 'uid', 'AttributeType' => 'S')), 'KeySchema' => array(array('AttributeName' => 'uid', 'KeyType' => 'HASH')),
    'ProvisionedThroughput' => array('ReadCapacityUnits' => 1, 'WriteCapacityUnits' => 1)
);

try {
    $response = $connection->aws()->describeTable(array("TableName" => "element"));

} catch (Exception $e) {

    $tableExist = false;
}


if ($tableExist) {

    $connection->aws()->deleteTable(array('TableName' => 'element'));

    $connection->aws()->waitUntil('TableNotExists', array('TableName' => 'element'));
}
$connection->aws()->createTable($tableElement);
$connection->aws()->waitUntil('TableExists', array('TableName' => 'element'));
