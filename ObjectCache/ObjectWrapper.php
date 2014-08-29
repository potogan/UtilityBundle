<?php

namespace Potogan\UtilityBundle\ObjectCache;

use Doctrine\Common\Annotations\Reader;
use ReflectionMethod;
use Potogan\UtilityBundle\ObjectCache\Annotations\Cache;
use Potogan\UtilityBundle\ObjectCache\Annotations\ClearCache;
use Potogan\UtilityBundle\ObjectCache\Annotations\ClearScopeCache;
use Potogan\UtilityBundle\ObjectCache\Annotations\ClearMethodCache;
use Potogan\UtilityBundle\ObjectCache\Annotations\ClearMethodEntryCache;

class ObjectWrapper
{
    protected $_original;
    protected $_reader;
    protected $_scopes = array();
    protected $_meta = array();
    protected $_cache = array();

    public function __construct($original, Reader $reader)
    {
        $this->_original = $original;
        $this->_reader  = $reader;

        if ($original instanceof WrappedAwareObjectInterface) {
            if ($wrapper = $original->getWrapper($this)) {
                $this->_meta = &$wrapper->_meta;
                $this->_cache = &$wrapper->_cache;

                return;
            }
        }

        $methods = get_class_methods(get_class($original));

        foreach ($methods as $method) {
            $r = new ReflectionMethod($original, $method);

            $meta = (object) array(
                'reflection' => $r,
                'annotations' => $this->_reader->getMethodAnnotations($r),
                'cache' => false,
                'clear_cache' => array(),
            );

            foreach ($meta->annotations as $annotation) {
                if ($annotation instanceof Cache) {
                    $meta->cache = true;
                    $this->_cache[$method] = array();

                    if ($annotation->value) {
                        if (!isset($this->_scopes[$annotation->value])) {
                            $this->_scopes[$annotation->value] = array();
                        }

                        $this->_scopes[$annotation->value][] = $method;
                    }
                } elseif ($annotation instanceof ClearCache) {
                    $meta->clear_cache[] = $annotation;
                }
            }

            $this->_meta[$method] = $meta;
        }

        if ($original instanceof WrappedAwareObjectInterface) {
            $original->setWrapper($this);
        }
    }

    public function __call($method, $arguments)
    {
        $res = null;
        $key = null;
        $meta = null;

        if (isset($this->_meta[$method])) {
            $meta = $this->_meta[$method];

            if ($meta->cache) {
                $key = json_encode($arguments);
            }
        }

        if ($meta && $meta->cache && array_key_exists($key, $this->_cache[$method])) {
            $res = $this->_cache[$method][$key];
        } else {
            $res = call_user_func_array(array($this->_original, $method), $arguments);

            if ($meta && $meta->cache) {
                $this->_cache[$method][$key] = $res;
            }
        }

        if ($meta && count($meta->clear_cache)) {
            foreach ($meta->clear_cache as $cc) {
                if ($cc instanceof ClearScopeCache) {
                    if (isset($this->_scopes[$cc->value])) {
                        foreach ($this->_scopes[$cc->value] as $m) {
                            $this->_cache[$m] = array();
                        }
                    }
                } elseif ($cc instanceof ClearMethodCache) {
                    if (isset($this->_cache[$cc->value])) {
                        $this->_cache[$cc->value] = array();
                    }
                } elseif ($cc instanceof ClearMethodEntryCache) {
                    if (isset($this->_cache[$cc->value])) {
                        $key = array();
                        foreach ($cc->parameters as $index) {
                            $key[] = $arguments[$index];
                        }

                        $key = json_encode($key);
                        unset($this->_cache[$cc->value][$key]);
                    }
                } elseif ($cc instanceof ClearCache) {
                    foreach (array_keys($this->_cache) as $m) {
                        $this->_cache[$m] = array();
                    }
                }
            }
        }

        return $res;
    }
}
