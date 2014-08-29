<?php

namespace Potogan\UtilityBundle\ObjectCache\Annotations;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 * @Target({"METHOD"})
 */
class ClearCache extends Annotation
{
}
