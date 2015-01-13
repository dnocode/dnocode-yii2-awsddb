<?php
namespace dnocode\awsddb\ar;


use Aws\DynamoDb\Enum\AttributeAction;

use dnocode\awsddb\ddb\inputs\Attribute;
use yii\base\InvalidCallException;
use yii\helpers\Inflector;

use yii\helpers\StringHelper;
use Yii;

/**
 * ActiveRecord is the base class for classes representing relational data in terms of objects.
 *
 * This class implements the ActiveRecord pattern for the Dynamodb

 */
class ActiveRecord extends BaseActiveRecord
{

    public function init(){

        $reader = function & ($object, $property) {
            $value = & \Closure::bind(function & () use ($property) {
                return $this->$property;
            }, $object, $object)->__invoke();
            return $value;
        };

        /**this allows to put property in
         * private property attributes inside active record by ref**/

       $reflect=new \ReflectionClass(get_called_class());

        $props=$reflect->
            getProperties(\ReflectionProperty::IS_PUBLIC |
            \ReflectionProperty::IS_PROTECTED |
            \ReflectionProperty::IS_PRIVATE);

        foreach ($props as $prop) {
            $pref=&$reader($this, $prop->getName());
            $this->setAttributeByRef($prop->getName(),$pref);
        }

        $this->on( ActiveRecord::EVENT_BEFORE_INSERT,[$this,'beforeInsertIntegrityRefresh']);
        $this->on( ActiveRecord::EVENT_AFTER_INSERT,[$this,'afterInsertIntegritySave']);
    }



    public static function tableName()
    {
        return Inflector::camel2id(StringHelper::basename(get_called_class()), '_');
    }
    /**
     * Returns the database connection used by this AR class.
     * By default, the "ddb" application component is used as the database connection.
     * You may override this method if you want to use a different database connection.
     * @return Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
         return Yii::$app->get("ddb");
    }

    /**
     * @inheritdoc
     * @return ActiveQuery the newly created [[ActiveQuery]] instance.
     */
    public static function find()
    {
        return Yii::createObject(ActiveQuery::className(), [get_called_class(),static::getDb()]);
    }



    /**
     * Returns the schema information of the DB table associated with this AR class.
     * @return TableSchema the schema information of the DB table associated with this AR class.
     * @throws InvalidConfigException if the table for the AR class does not exist.
     */
    public static function getTableSchema()
    {
        throw new NotSupportedException(__METHOD__ . ' is not supported.');
    }

    /**
     * you must override this method for indicates
     * the primary key of dynamo
     * the first one its a hash key
     * the second its the range (optional)
     * @return \string[]|void
     * @throws NotSupportedException
     */
    public static function primaryKey(){throw new NotSupportedException(__METHOD__ . ' you need to override this method');}



    /**
     * @inheritdoc
     */
    public function insert($runValidation = true, $attributes = null)
    {
        if ($runValidation && !$this->validate($attributes)) {
            Yii::info('Model not inserted due to validation error.', __METHOD__);
            return false;
        }

        if (!$this->beforeSave(true)) {
            return false;
        }
        //An attribute is considered dirty if its value was modified after the model was loaded
        $values = $this->getDirtyAttributes($attributes);

        if (empty($values)) {

            foreach ($this->getPrimaryKey(true) as $key => $value) {

                $values[$key] = $value;
            }
        }

        $db = static::getDb();

        $db->createExecCommand($this->tableName(),AttributeAction::PUT,$values);

        $changedAttributes = array_fill_keys(array_keys($values), null);

        $this->setOldAttributes($values);

        $this->afterSave(true, $changedAttributes);

        return true;


    }





    public  function delete(){


        if ($this->beforeDelete()) {
            // we do not check the return value of deleteAll() because it's possible
            // the record is already deleted in the database and thus the method will return 0
            $condition = $this->getOldPrimaryKey(true);
            $result = $this->deleteAll($condition);
            $this->setOldAttributes(null);
            $this->afterDelete();
        }

        return $result;

    }



    /**
     * Updates the whole table using the provided attribute values and conditions.
     * For example, to change the status to be 1 for all customers whose status is 2:
     *
     * ~~~
     * Customer::updateAll(['status' => 1], ['id' => 2]);
     * ~~~
     *
     * @param array $attributes attribute values (name-value pairs) to be saved into the table
     * @param array $condition the conditions that will be put in the WHERE part of the UPDATE SQL.
     * Please refer to [[ActiveQuery::where()]] on how to specify this parameter.
     * @return integer the number of rows updated
     */
    public static function updateAll($condition, $attributes = null){

        $db = static::getDb();

        $db->createExecCommand(static::tableName(),AttributeAction::ADD,$attributes,$condition);

    }

    /**
     * Deletes rows in the table using the provided conditions.
     * WARNING: If you do not specify any condition, this method will delete ALL rows in the table.
     *
     * For example, to delete all customers whose status is 3:
     *
     * ~~~
     * Customer::deleteAll(['status' => 3]);
     * ~~~
     *
     * @param array $condition the conditions that will be put in the WHERE part of the DELETE SQL.
     * Please refer to [[ActiveQuery::where()]] on how to specify this parameter.
     * @return integer the number of rows deleted
     */
    public static function deleteAll($condition = null){

        $db = static::getDb();

        $db->createExecCommand(static::tableName(),AttributeAction::DELETE,$condition);

        return true;

    }

    /**
     * Populates an active record object using a row of data from the database/storage.
     *
     * This is an internal method meant to be called to create active record objects after
     * fetching data from the database. It is mainly used by [[ActiveQuery]] to populate
     * the query results into active records.
     *
     * When calling this method manually you should call [[afterFind()]] on the created
     * record to trigger the [[EVENT_AFTER_FIND|afterFind Event]].
     *
     * @param BaseActiveRecord $record the record to be populated. In most cases this will be an instance
     * created by [[instantiate()]] beforehand.
     * @param array $row attribute values (name => value)
     */
    public static function populateRecord($record, $row)
    {
        $columns = array_flip($record->attributes());

        foreach ($row as $name =>  $type_value) {

            $value=Attribute::resolve($type_value);
            if (isset($columns[$name])) {

                $record->setAttribute($name, $value);
                $record->setOldAttribute($name,$value);
            }
            elseif ($record->canSetProperty($name)) {
                $record->$name = $value;
            }
        }


    }

    /**
     * @param ModelEvent $evt
     */
    public function beforeInsertIntegrityRefresh($evt){

        /** @var $attributes */
        $attributes=$this->getAttributes();

        $this::lookingForRelationProperty($attributes,$this);
    }
    /**
     * @param ModelEvent $evt
     */
    public function afterInsertIntegritySave($evt){

        /** @var ActiveRecord $activeRecord */
        $relatedOutdatedObjects=$this->getOutdated();
        /** @var ActiveRecord $ar */
        foreach($relatedOutdatedObjects as $ar){$ar->save();}
    }




    /**
     * this method maintein referential
     * integrity beeween parent saving and
     * its property active record
     * example
     *
     * $client=new Client();
     * $client->save();
     *
     * $parent=new Parent();
     * $parent->clients[]=$client;
     * $parent->save();
     *
     *
     * if client has property parent this will be updated
     * we can update this name and beetween $source property understund what kind of object wants to be update
     *
     * this method save parent and
     * then save
     * @param $source_property
     * @param ActiveRecord $parentObject
     */
    public function addRelationProperty($parentSourceProperty,$parentObject)
    {
        $relatedObject=$this;

        if($relatedObject->getIsNewRecord()){ throw new InvalidCallException("related Object is detached from context please save it before. ".$parentObject::className());}

        $relationsMap=$this->relationsMap();

        if(empty($relationsMap)|| array_key_exists($parentObject->className(),$relationsMap))
        {
            throw new InvalidCallException("need to override this method for custom relation adder or indicate a relations map by overriding of relationsMap");
        }

        //find property name in $relations map by name
        $relationProperty=$relationsMap[$parentObject->tableName()][$parentSourceProperty];

        if(array_key_exists("target",$relationProperty)==false){ throw new InvalidCallException("invalid relation map on sourceProperty:".$parentSourceProperty);}

        $propertyTarget=$relationProperty["target"];
        //is the scenario as property is implemented will be used
        $parentObject->setScenario(array_key_exists("scenario",$relationProperty)?$relationProperty["scenario"]:self::SCENARIO_DEFAULT);

        $pattributes=$parentObject->getAttributes($parentObject->safeAttributes());

        if(is_array($relatedObject->$propertyTarget)){   array_push($relatedObject->$propertyTarget,$pattributes);}

        else{ $relatedObject->$propertyTarget=$pattributes;}

        //relation object that need to be updated
        $parentObject->populateOutDated($relatedObject);
    }

    /**
     * @param $attributes
     * @param ActiveRecord $activeRecordParent
     * @param null $propertyName
     */
    private function lookingForRelationProperty($attributes,$activeRecordParent,$propertyName=null){

        $attributes=array_filter($attributes);

        foreach( $attributes as $name=>$value){

            //ricorsione
            if(is_array($value)){

                $this->lookingForRelationProperty($value,$activeRecordParent,$name);

                continue;
            }


            if($value instanceof ActiveRecord){

                $relatedObject=$value;

                /**convert active record to array**/
                /**@var Model $attribute**/

                $attribute=&$this->getAttribute($propertyName==null?$name:$propertyName);

                if(is_array($attribute)){
                    /**
                     * @var ActiveRecord $child
                     */
                    $child=&$attribute[$name];

                    $attribute[$name]=$child->getAttributes($child->activeAttributes());

                }else{

                    $attribute=$attribute->getAttributes();
                }

                $relatedObject->addRelationProperty($propertyName==null?$name:$propertyName,$activeRecordParent);
            }
        }
    }



    public function relationsMap()  {

        return [];
    }
}
