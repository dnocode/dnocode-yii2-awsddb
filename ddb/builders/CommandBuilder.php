<?php

namespace dnocode\awsddb\ddb\builders;

use Aws\DynamoDb\Enum\ComparisonOperator;
use dnocode\awsddb\ar\ActiveRecord;
use dnocode\awsddb\ddb\inputs\AWSInput;
use dnocode\awsddb\ddb\inputs\GetInput;
use dnocode\awsddb\ddb\inputs\QueryInput;
use dnocode\awsddb\ddb\inputs\ScanInput;
use yii\base\InvalidParamException;
use yii\base\NotSupportedException;
use Aws\DynamoDb\Model\Item;
use dnocode\awsddb\ddb\enums\Search;
use yii\helpers\ArrayHelper;
use dnocode\awsddb\ar\Query;

/**
 * CommandBuilder creates command for dynamo db
 * from ActiveQuery
 *
 *    DATA TYPES FOR AMZ OBJECT
 *    S => (string)
 *    A String data type.
 *
 *    N => (string)
 *    A Number data type.
 *
 *    B => (string)
 *    The supplied string value will be automatically base64 encoded by the SDK. Base64 encoding this value before passing it into an operation will double-encode the data.
 *    A Binary data type.
 *
 *   SS => (array<string>)
 *   A String Set data type.
 *
 *    NS => (array<string>)
 *    A Number Set data type.
 *
 *    BS => (array<string>)
 *    A Binary Set data type.
 *
 *    M => (associative-array<associative-array>)
 *    Associative array of <AttributeName> keys mapping to (associative-array) values. Each array key should be changed to an appropriate <AttributeName>.
 *    A Map of attribute values.
 *
 *   <AttributeName> => (associative-array)
 *   Associative array of custom key value pairs
 *
 *   L => (array<associative-array>)
 *   A List of attribute values.
 *
 *   (associative-array)
 *   Associative array of custom key value pairs
 *
 *   NULL => (bool)
 *   A Null data type.
 *
 *   BOOL => (bool)
 *   A Boolean data type.
 *
 * @author Dino <ricceri.dino@gmail.com>
 * @since 2.0
 */
class CommandBuilder extends \yii\base\Object
{
    /**
     * @var Connection the database connection.
     */
    public $db;

    /**
     * Constructor.
     * @param Connection $connection the database connection.
     * @param array $config name-value pairs that will be used to initialize the object properties
     */
    public function __construct($connection, $config = [])
    {
        $this->db = $connection;
        parent::__construct($config);
    }

    /**
     *
     * @param $config
     * @param Query $query
     * @return Command
     */
    public function build($query,&$config)
    {
        $config ['db']=$this->db;

        /** @var AWSInput $amz_input */
        $amz_input=$query!=null? $this->buildAWSGetInput($query,$config):$this->buildPutAWSInput($config);

        unset($config['attributes']);

        unset($config['table']);

        $config['amz_input']=$amz_input;

        /* @var Command $cmd*/
        $cmd=\Yii::createObject($config);

        $cmd->validate();

        return $cmd;

    }

    /**
     * creates a InputCommand Object From Query
     * @param Query $qry
     * @param $config
     * @return AWSInput
     */
    private function buildAWSGetInput($qry,&$config){

        /** todo  transform aws input in object that create array after */
        $inputObject=new ScanInput();


        /**there is a comparator
         * means that was used andWhere or or Orwhere
         *  where**/
        if($qry->comparator!=null){

            $qry->where=$qry->comparator->columns();


        }

        if(!empty($qry->where)){

            /** @var ActiveRecord $modelClass */
            $modelClass=$qry->modelClass;
            $config["type"]=Search::SCAN;

            /**the where contain just primary keys it a get**/
            if($isGet=$modelClass::isPrimaryKey(array_keys($qry->where))){

                $config["type"]=Search::GET;
                $inputObject=new GetInput();
            }

            /**check if  the conditions columns are primary key too**/
            if( $isGet===false&&$this->targetContainsAtLeastOneKey($modelClass::primaryKey(),$qry->where)){
                $config["type"]=Search::QUERY;
                $inputObject=new QueryInput();
            }

            /**common query configuration**/
            $qry->select=="count(*)"?$inputObject->count():$inputObject->select($qry->select);

            $inputObject->tableName($qry->from)

            ->indexName($qry->indexName)

            ->limit($qry->limit);

            $isQuery= $inputObject instanceof QueryInput;

            foreach($qry->where as $name=>$value){

                $isPk=false;
                /**is a dynamo query AND  non e` una primary usa il query filter**/
                $filter=((!$isQuery)||$isQuery&&($isPk=$modelClass::isPrimaryKey([$name])))?

                $inputObject->filter():$inputObject->queryFilter();

                if($value===null&&$qry->comparator!=null){

                    $filter ->injectAttribute($qry->comparator->getAttribute($name),$isPk);
                    $filter->setConditionalOperator($qry->comparator->cond_choosen);
                    continue;
                }

                $filter ->attr($name)->eq($value);
            }
        }

        return $inputObject;
        }

    /**
     * /**
     * creates a InputCommand Object
     */
    private function buildPutAWSInput(&$config){
        /** @var Item $item */

        $item=Item::fromArray($config['attributes'],$config['table']);

        return array("TableName"=>$item->getTableName(),"Item"=>$item->toArray());

    }


    private function targetContainsAtLeastOneKey($keysArray,$targetArray){

            $contain=false;

            foreach($keysArray as $key){
                $contain=array_key_exists($key,$targetArray);
                if ($contain==true) { break; }
            }

        return $contain;

}


}
