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
            $e->uid=$this->generateRandomString(5);
            $e->name=$this->generateRandomString(10);
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





    /*public function testFindSimple(){


        //create schema with connection
        //put some object
        //then query

        $this->assertEquals("a","a","bella vita");

    }*/


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
 