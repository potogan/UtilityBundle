<?php
namespace Potogan\UtilityBundle\ObjectCache;

interface WrappedAwareObjectInterface
{
	public function setWrapper(ObjectWrapper $wrapper);
	public function getWrapper();
}