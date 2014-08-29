<?php

namespace Potogan\UtilityBundle\ObjectCache\Annotations;

/**
 * @Annotation
 * @Target({"METHOD"})
 */
class ClearMethodEntryCache extends ClearCache
{
    /**
	 * @var array<int>
	 */
    public $parameters = array();
}
