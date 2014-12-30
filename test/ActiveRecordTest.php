<?php
/**
 * Created by PhpStorm.
 * User: dino
 * Date: 12/22/14
 * Time: 10:44 AM
 */

namespace dnocode\awsddb\test;


use dnocode\awsddb\ar\Query;
use dnocode\awsddb\test\model\Element;
use yii\db\ActiveQuery;

class ActiveRecordTest extends \PHPUnit_Framework_TestCase {


    public function testPut(){
        $putsOk=array();
        /**put elements***/
        for($i=1;$i<50;$i++){
            $e=new Element();
            $e->uid="".$i;

            $e->name=$this->generateRandomString(10);

            switch($i){
                case 3:
                           $e->name="fulmicotone"; break;
                case 10:
                             $e->name="crazy8"; break;
                case 11:
                    $e->name="crazy8"; break;
                case 12:
                            $e->name="pig";
                    break;

            }


            $e->surname=$this->generateRandomString(10);
            $e->sex=$this->generateRandomString(10);
            if($e->save()) $putsOk[]="k"  ;
        }
        $this->assertEquals(count($putsOk),49,"puts test OK");
    }


    /**
     * @depends testPut
     */
     public function testScanAll(){

      $elements=Element::find()->all();
      $this->assertEquals(count($elements),49,"scan all test OK");
     }




    /**
     * @depends testScanAll
     */
    public function testGetFindOne(){

        /**where on primary key will be a get operation for dynamo**/
        $element=Element::find()->
            where(["uid"=>"1"])  ->one();

        $this->assertEquals($element->uid,"1","aws get operation OK");
    }

    /**
     * @depends testPut
     */
    public function testUpdate(){

       $element=Element::find()->
            where(["uid"=>"1"])  ->one();
        $element->name="mionome";
        $element->save();
        $element=Element::find()->
            where(["uid"=>"1"])  ->one();
        $this->assertEquals($element->name,"mionome","aws update ok");

    }


    /**
     * @depends testUpdate
     */
    public function testFindByAttribute(){

        /**where attribute**/
        $element=Element::find()->

        where(["name"=>"mionome"])  ->one();

        $this->assertEquals($element->uid,"1","aws scan operation on attribute OK");
    }

    /**
     * @depends testPut
     */
    public function testFindByComparator(){

        $elements=Element::find()->
            andWhere("uid")->eq("1")
            ->all();
        $this->assertEquals(count($elements),1,"aws get by comparator ok");
    }


    /**
     * @depends testPut
     */
    public function testFindByAttributesOr(){

          $elements=Element::find()->
              orWhere("name")->eq("crazy8")
              ->orr("name")->eq("pig")
              ->all();

        $this->assertEquals(count($elements),3,"FindByAttributesOr");
    }


    /**
     * @depends testPut
     */
    public function testFindByAttributesIn(){

        $elements=Element::find()->orWhere("name")->in(["crazy8","pig"])->all();

        $this->assertEquals(count($elements),3,"aFindByAttributesIn ok");
    }


    /**
     * todo  now
     * if we try to retrieve more than one records
     * by pks we receive just the last one
     * throw get operation
     * it will be nice if with this operation the
     * active record execute two get and get the records
     * but im too tired and poor of time help me
     *
     * @depends testPut
     */
    public function testFindByPKOr(){

        $elements=Element::find()->
            orWhere("uid")->eq("1")
            ->orr("uid")->eq("2")
            ->all();

        $this->assertEquals(count($elements),1,"aws get elements");
    }



/* @depends testFindByAttributesIn
*/
    public function testDeleteOnModel(){

        $element=Element::find()->
            andWhere("uid")->eq("3")->one();
        $element->delete();
        $element=Element::find()->
            andWhere("uid")->eq("3")->one();
        $this->assertEquals($element,null,"deleted with success ok");
    }

    /* @depends testDeleteOnModel
     */
    public function testDeleteAll(){

       /*
       i believe it impossible for now delete element by attribute value
       but the primary key is required

       $elements=Element::find()->andWhere("name")->in(["crazy8"])->all();

        Element::deleteAll(["name"=>"crazy8"]);
        **/
        Element::deleteAll(["uid"=>"10"]);
        $elements=Element::find()->andWhere("uid")->in(["10"])->all();

        $this->assertEquals(count($elements),0,"delete all   success ok");
    }


    function generateRandomString($length = 10) {

        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

        $charactersLength = strlen($characters);

        $randomString = '';

        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }



}
 