<?php
namespace Potogan\UtilityBundle\Tests\ObjectCache;

use Potogan\UtilityBundle\ObjectCache\Annotations as CAC;

class WrappedObject {
	protected $counter = 0;

	/**
	 * @CAC\Cache
	 */
	public function getCounter()
	{
		return ++$this->counter;
	}

	/**
	 * @CAC\ClearMethodCache("getCounter")
	 */
	public function clearCounterCache()
	{

	}


	/**
	 * @CAC\Cache("testscope")
	 */
	public function testscopeMethod1()
	{
		return rand();
	}

	/**
	 * @CAC\Cache("testscope")
	 */
	public function testscopeMethod2()
	{
		return rand();
	}


	/**
	 * @CAC\ClearScopeCache("testscope")
	 */
	public function clearTestscope()
	{

	}

	/**
	 * @CAC\ClearCache()
	 */
	public function clearAllCaches()
	{

	}


	/**
	 * @CAC\Cache("testscope")
	 */
	public function getParameter($name)
	{
		return rand();
	}

	/**
	 * @CAC\ClearMethodEntryCache("getParameter", parameters = {0})
	 */
	public function clearParameterCache($name, $value)
	{

	}

}

