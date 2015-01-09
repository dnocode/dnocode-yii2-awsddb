<?php
/**
 * Created by PhpStorm.
 * User: dino
 * Date: 1/7/15
 * Time: 3:47 PM
 */

namespace dnocode\awsddb\ddb\inputs;


class Item  extends \Aws\DynamoDb\Model\Item{

    /**
     * Create an item from a simplified array
     *
     * @param array  $attributes Array of attributes
     * @param string $tableName  Name of the table associated with the item
     *
     * @return self
     */
    public static function fromArray(array $attributes, $tableName = null)
    {
        foreach ($attributes as &$value) {
            $value = Attribute::factory($value);
        }

        return new self($attributes, $tableName);
    }



    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        $result = $this->data;

        foreach ($result as &$attr) {

            if ($attr instanceof Attribute) {

                if(is_array($attr->getValue())){

                    foreach($attr->getValue() as &$subattr){
                        if ($subattr instanceof Attribute) {
                            $subattr=$subattr->toArray();
                        }
                    }
                }

                $attr = $attr->toArray();
            }
        }

        return $result;
    }

} 