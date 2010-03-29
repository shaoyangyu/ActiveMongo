<?php

class HookTest extends PHPUnit_Framework_TestCase
{

    /**
     *  Hooking test, update model2 when model1 changes
     */
    function model1_after_update($document, $object)
    {
        $m2 = new model2;
        $m2->update_refs($object);
    }

    /**
     *  Testing hook, modifing on update other documents
     */
    function testChanges()
    {
        Model1::addEvent('after_update', array($this, 'model1_after_update'));
        $m1 = new Model1;
        $m1->a = 1;
        $m1->b = 2;
        $m1->save();

        for ($i=0; $i < 1000; $i++) {
            $m2 = new Model2;
            $m2->M1 = $m1->getID();
            $m2->a  = $m1->a;
            $m2->save();
        }

        $m1->a = 50;
        $m1->save();

        $m2->reset();
        foreach ($m2->where("M1", $m1->getID()) as $item) {
            $this->assertEquals($m1->a, $item->a);
        }

    }
}