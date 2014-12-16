<?php
namespace dnocode\awsddb;


use Aws\DynamoDb\Enum\AttributeAction;
use yii\db\BaseActiveRecord;
use yii\helpers\Inflector;
use Aws\DynamoDb\DynamoDbClient;
use yii\helpers\StringHelper;
use Yii;

/**
 * ActiveRecord is the base class for classes representing relational data in terms of objects.
 *
 * This class implements the ActiveRecord pattern for the Dynamodb

 */
class ActiveRecord extends BaseActiveRecord
{


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
    public static function primaryKey()
    {


        throw new NotSupportedException(__METHOD__ . ' you need to override this method');
    }



    public function insertAll($runValidation=true,$attributesrows=array()){ }


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
    public static function updateAll($attributes, $condition = null){}

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
    public static function deleteAll($condition = null){}


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
            $value=current($type_value);
            if (isset($columns[$name])) {
                $record->setAttribute($name, $value);
                $record->setOldAttribute($name,$value);
            } elseif ($record->canSetProperty($name)) {
                $record->$name = $value;
            }
        }


    }



}
