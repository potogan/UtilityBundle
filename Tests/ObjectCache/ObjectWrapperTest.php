<?php

namespace Potogan\UtilityBundle\Tests\ObjectCache;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Potogan\UtilityBundle\ObjectCache\ObjectWrapper;

class ObjectWrapperTest extends WebTestCase
{

    public function testInstanciation()
    {
        $instance = new ObjectWrapper(
            new WrappedObject(),
            $this->getContainer()->get('annotation_reader')
        );

        $this->assertInstanceOf('Potogan\\UtilityBundle\\ObjectCache\\ObjectWrapper', $instance);

        return $instance;
    }

    /**
     * @depends testInstanciation
     */
    public function testSimpleCache($instance)
    {
        $value = $instance->getCounter();

        $this->assertEquals($value, $instance->getCounter());
    }

    /**
     * @depends testInstanciation
     */
    public function testMethodClearCache($instance)
    {
        $value = $instance->getCounter();

        $instance->clearCounterCache();

        $this->assertNotEquals($value, $instance->getCounter());
    }

    /**
     * @depends testInstanciation
     */
    public function testScopeClearCache($instance)
    {
        $value1 = $instance->testscopeMethod1();
        $value2 = $instance->testscopeMethod2();

        $instance->clearTestscope();

        $this->assertNotEquals($value1, $instance->testscopeMethod1());
        $this->assertNotEquals($value2, $instance->testscopeMethod2());
    }

    /**
     * @depends testInstanciation
     */
    public function testClearAllCache($instance)
    {
        $value1 = $instance->getCounter();
        $value2 = $instance->testscopeMethod2();

        $instance->clearAllCaches();

        $this->assertNotEquals($value1, $instance->getCounter());
        $this->assertNotEquals($value2, $instance->testscopeMethod2());
    }

    /**
     * @depends testInstanciation
     */
    public function testClearMethodEntryCache($instance)
    {
        $value1 = $instance->getParameter('param1');
        $value2 = $instance->getParameter('param2');

        $instance->clearParameterCache('param1', 'blahblahblah');

        $this->assertNotEquals($value1, $instance->getParameter('param1'));
        $this->assertEquals($value2, $instance->getParameter('param2'));
    }

    /**
     * @depends testInstanciation
     * @expectedException PHPUnit_Framework_Error_Warning
     */
    public function testCallUnexistingMethod($instance)
    {
        $instance->iLoledHard();
    }

    protected function getContainer()
    {
        return static::createClient()->getContainer();
    }
}
