<?php


namespace dnocode\awsddb\ar;

use Aws\ImportExport\Exception\InvalidParameterException;
use dnocode\awsddb\ddb\builders\QueryComparatorBuilder;
use Yii;
use yii\base\Component;
use yii\db\QueryInterface;
use dnocode\awsddb\Transact;


/**
 * Query represents a SELECT SQL statement in a way that is independent of DBMS.
 *
 * Query provides a set of methods to facilitate the specification of different clauses
 * in a SELECT statement. These methods can be chained together.
 *
 * By calling [[createCommand()]], we can get a [[Command]] instance which can be further
 * used to perform/execute the DB query against a database.
 *
 * For example,
 *
 * ```php
 * $query = new Query;
 * // compose the query
 * $query->select('id, name')
 *     ->from('user')
 *     ->limit(10);
 * // build and execute the query
 * $rows = $query->all();
 * // alternatively, you can create DB command and execute it
 * $command = $query->createCommand();
 * // $command->sql returns the actual SQL
 * $rows = $command->queryAll();
 * ```
 *

 */
class Query extends Component implements QueryInterface
{
    /**
     * @var string the name of the ActiveRecord class.
     */
    public $modelClass;
    /**@var $get array  primary keys value to execute a get from dynamodb**/

    public $get;
    /** @var  $where used for execute query */
    public $where;
    /**
     * @var array the table(s) to be selected from. For example, `['user', 'post']`.
     * This is used to construct the FROM clause in a SQL statement.
     * @see from()
     */
    public $select;
    /**
     * @var array the table(s) to be selected from. For example, `['user', 'post']`.
     * This is used to construct the FROM clause in a SQL statement.
     * @see from()
     */
    public $from;
    /**
     * @var array how to group the query results. For example, `['company', 'department']`.
     * This is used to construct the GROUP BY clause in a SQL statement.
     */
    public $asArray;
    public $indexBy;
    /**
     * @var string is the index choice for dynamo db
     */
    public $indexName;

    /**  @var QueryComparatorBuilder $comparator   */
    public $comparator;

    public $params = [];
    /**
     * @var integer maximum number of records to be returned. If not set or less than 0, it means no limit.
     */
    public $limit;
    /**
     * Creates a DB command that can be used to execute this query.
     * @param Connection $db the database connection used to generate the SQL statement.
     * If this parameter is not given, the `db` application component will be used.
     * @return Command the created DB command instance.
     */
    public function createCommand($db = null){   $db->createExecQueryCommand($this,$this->params);  }


    public function select($columns)
    {
        if(!is_array($columns)){throw new InvalidParameterException("columns must be an array!!!");}

        $this->select=$columns;

        return $this;
    }

    /**
     * Sets the FROM part of the query.
     * @param string|array $tables the table(s) to be selected from. This can be either a string (e.g. `'user'`)
     * or an array (e.g. `['user', 'profile']`) specifying one or several table names.
     * Table names can contain schema prefixes (e.g. `'public.user'`) and/or table aliases (e.g. `'user u'`).
     * The method will automatically quote the table names unless it contains some parenthesis
     * (which means the table is given as a sub-query or DB expression).
     *
     * When the tables are specified as an array, you may also use the array keys as the table aliases
     * (if a table does not need alias, do not use a string key).
     *
     * Use a Query object to represent a sub-query. In this case, the corresponding array key will be used
     * as the alias for the sub-query.
     *
     * @return static the query object itself
     */
    public function from($tables)
    {
        if (!is_array($tables)) {
            $tables = preg_split('/\s*,\s*/', trim($tables), -1, PREG_SPLIT_NO_EMPTY);
        }
        $this->from = $tables;
        return $this;
    }

    /**
     * Executes the query and returns all results as an array.
     * @param Connection $db the database connection used to generate the SQL statement.
     * If this parameter is not given, the `db` application component will be used.
     * @return array the query results. If the query results in nothing, an empty array will be returned.
     */
    public function all($db = null)
    {
        /** @var Transact $transaction */
        $transaction = $db->createExecQueryCommand($this);

        return $this->populate($transaction->getResult());
    }


    /**
     * receive in inputs primarykeys array for retrieve item by get
     * @param The `$conditions` should be an array of primarykeys   [prkey1=>value]
     * @return $this
     * @throws \Aws\ImportExport\Exception\InvalidParameterException
     */
    public function get($conditions)
    {
       // check if conditions as primary keys//if( whe($condition)==false){throw new InvalidParameterException;}

        //$this->where = $condition;

        return $this;
    }

    /**
     * Sets the WHERE part of the query.
     * The method requires a `$condition`
     * The `$condition` should be an array.
     * it will used query dynamo db
     * @param array $condition the conditions that should be put in the WHERE part.
     */
    public function where($condition=array())
    {
         /** is eq conditions
         * done as array (a=>1,b=>2)
         * that mean where a=1 and b =2
         */
        if(!empty($condition))
        {
            $this->where = $condition;

            return $this;
        }


    }

    /**
     * @param array|string $attributeName
     * @return
     */
    public function andWhere($attributeName)
    {
        $this->comparator=$this->comparator==null? new QueryComparatorBuilder($this):$this->comparator;

        return $this->comparator->andd($attributeName);
    }

    public function orWhere($attributeName)
    {
        $this->comparator=$this->comparator==null? new QueryComparatorBuilder($this):$this->comparator;
        return $this->comparator->orr($attributeName);
    }




    /**
     * Executes the query and returns a single row of result.
     * @param Connection $db the database connection used to generate the SQL statement.
     * If this parameter is not given, the `db` application component will be used.
     * @return array|boolean the first row (in terms of an array) of the query result. False is returned if the query
     * results in nothing.
     */
    public function one($db = null)
    {
        /** @var Transact $transaction */
        $transaction = $db->createExecQueryCommand($this);

        return $transaction->getResult();
    }

    /**
     * Returns a value indicating whether the query result contains any row of data.
     * @param Connection $db the database connection used to generate the SQL statement.
     * If this parameter is not given, the `db` application component will be used.
     * @return boolean whether the query result contains any row of data.
     */
    public function exists($db = null)
    {
       //todo
    }

    /**
     * Converts the raw query results into the format as specified by this query.
     * This method is internally used to convert the data fetched from database
     * into the format as required by this query.
     * @param array $rows the raw query result from database
     * @return array the converted query result
     */
    public function populate($rows)
    {
        $models = [];
        if ($this->asArray) {

            foreach ($rows as $row) {
                if (is_string($this->indexBy)) {
                    $key = $row[$this->indexBy];
                } else {
                    $key = call_user_func($this->indexBy, $row);
                }
                $models[$key] = $row;
            }
        } else {
            /* @var $class ActiveRecord */
            $class = $this->modelClass;
            if ($this->indexBy === null) {
                foreach ($rows as $row) {
                    $model = $class::instantiate($row);
                    $class::populateRecord($model, $row);
                    $models[] = $model;
                }
            } else {
                foreach ($rows as $row) {
                    $model = $class::instantiate($row);
                    $class::populateRecord($model, $row);
                    if (is_string($this->indexBy)) {
                        $key = $model->{$this->indexBy};
                    } else {
                        $key = call_user_func($this->indexBy, $model);
                    }
                    $models[$key] = $model;
                }
            }
        }

        return $models;
    }


    public function count($q = '*', $db = null)
    {
        $this->select="count($q)";
    }


    public function indexBy($column)
    {
          throw new NotSupportedException(__METHOD__ . ' is not supported.');
    }


    public function filterWhere(array $condition)
    {
        throw new NotSupportedException(__METHOD__ . ' is not supported.');
    }

    public function andFilterWhere(array $condition)
    {
        throw new NotSupportedException(__METHOD__ . ' is not supported.');

        }


    public function orFilterWhere(array $condition)
    {
        throw new NotSupportedException(__METHOD__ . ' is not supported.');
    }


    public function orderBy($columns)
    {
        throw new NotSupportedException(__METHOD__ . ' is not supported.');
    }


    public function addOrderBy($columns)
    {
        throw new NotSupportedException(__METHOD__ . ' is not supported.');
    }


    public function limit($limit)
    {
        throw new NotSupportedException(__METHOD__ . ' is not supported.');
    }


    public function offset($offset)
    {
        throw new NotSupportedException(__METHOD__ . ' is not supported.');
    }

    public function tableName(){

        if($this->modelClass==null){return false;}
        /**
         * @var ActiveRecord $mc
         */
        $mc=$this->modelClass;

        return $mc::tableName();

    }

}
