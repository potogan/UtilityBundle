<?php

namespace Potogan\UtilityBundle;

/***************************************************************
*  Copyright notice
*
*  (c) 2011 Popy (popy.dev@gmail.com)
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

/**
 * @package TYPO3
 * @subpackage pp_lib
 * @author Popy <popy.dev@gmail.com>
 */
class Paginator
{
    /**
     * Current page pointer
     * the value will not be checked against page number, so it has to be set BEFORE ->calc
     * @access protected
     * @var int
     */
    protected $pointer = 0;

    /**
     * Maximum results displayed on a page
     * @access protected
     * @var int
     */
    protected $resPerPage = 5;

    /**
     * Result pages count
     * @access protected
     * @var int
     */
    protected $pages = 1;

    /**
     * Maximum displayed page pointers
     * @access protected
     * @var int
     */
    protected $maxPages = 0;

    /**
     * Result count
     * @access protected
     * @var int
     */
    protected $count = 0;

    /**
     * SQL Limit
     * @access protected
     * @var string
     */
    protected $limit = null;
    protected $limit_start;
    protected $limit_count;

    /**
     * Displayabled result list : The paginator object can be used as a result container
     * @access protected
     * @var array
     */
    protected $results = array();

    /**
     * Callback used to generate page links
     * @access protected
     * @var array
     */
    protected $buildLinkCallback = array();

    /**
     * Callback used to generate alternate page links
     * @access protected
     * @var array
     */
    protected $buildAlternateLinkCallback = array();

    public function __construct($opts = array())
    {
        foreach ($opts as $key => $value) {
            $this->__set($key, $value);
        }
    }

    /**
     * Read-only property accessor
     *
     * @param string $name = property name
     *
     * @access public
     * @return mixed
     */
    public function __get($name)
    {
        if (isset($this->$name)) {
            return $this->$name;
        } else {
            return null;
        }
    }

    /**
     * Write property accessor
     *
     * @param string $name = property name
     *
     * @access public
     * @return mixed
     */
    public function __set($name, $value)
    {
        $intval = array('resPerPage', 'maxPages', 'pointer');
        $accessible = array('results', 'buildLinkCallback', 'buildAlternateLinkCallback');

        if (method_exists($this, $m = 'set' . $name)) {
            $this->$m($value);
        } elseif (in_array($name, $intval)) {
            $this->$name = intval($value);
        } elseif (in_array($name, $accessible)) {
            $this->$name = $value;
        }
    }

    /**
     * Calculate pagination infos
     *
     * @param int $count = total result count
     *
     * @access public
     * @return void
     */
    public function calc($count)
    {
        if ($count) {
            $this->count = $count;

            $this->pages = ceil($this->count / $this->resPerPage);

            $this->pointer = min($this->pointer, $this->pages - 1);

            $this->limit_start = ($this->pointer * $this->resPerPage);
            $this->limit_count  = min($this->resPerPage, $count - $this->limit_start);
            $this->limit = $this->limit_start . ', ' . $this->limit_count;
        }
    }

    /**
     * Pagination builder
     *
     * @access public
     * @return object
     */
    public function build()
    {
        $res = (object) array(
            'first' => false,
            'previous' => false,
            'pages' => array(),
            'next' => false,
            'last' => false,
            'previousRange' => false,
            'nextRange' => false,
            'count' => $this->pages,
        );
        $start = 0;
        $stop = $this->pages;

        if ($this->maxPages && $this->maxPages < $this->pages) {
            $half = intval(($this->maxPages - 1) / 2);
            $start = max(
                0,
                min($this->pointer - $half, $this->pages - $this->maxPages)
            );

            $stop = $start + $this->maxPages;
        }

        for ($i=$start; $i < $stop; $i++) {
            $res->pages[$i] = $this->buildPageObject($i);
        }

        if ($this->pointer) {
            $res->first = $this->buildPageObject(0);
            $res->previous = $this->buildPageObject($this->pointer - 1);
        }

        if ($this->pointer + 1 < $this->pages) {
            $res->last = $this->buildPageObject($this->pages - 1);
            $res->next = $this->buildPageObject($this->pointer + 1);
        }

        if ($this->maxPages) {
            if ($start - $this->maxPages >= 0) {
                $res->previousRange = $this->buildPageObject($start - $this->maxPages);
            }

            if ($stop + $this->maxPages <= $this->pages) {
                $res->nextRange = $this->buildPageObject($stop + $this->maxPages - 1);
            }
        }

        return $res;
    }

    /**
     * Build a single pagelink object
     *
     * @param int $num = page number
     *
     * @access protected
     * @return object
     */
    protected function buildPageObject($num)
    {
        $res = (object) array(
            'index' => $num,
            'num' => $num + 1,
            'active'  => $num == $this->pointer,
            'url' => false,
            'alturl' => false,
        );

        if ($this->buildLinkCallback && is_callable($this->buildLinkCallback)) {
            $res->pageurl = call_user_func(
                $this->buildLinkCallback,
                $num
            );
        }

        if ($this->buildAlternateLinkCallback && is_callable($this->buildAlternateLinkCallback)) {
            $res->altpageurl = call_user_func(
                $this->buildAlternateLinkCallback,
                $num
            );
        }

        return $res;
    }
}
