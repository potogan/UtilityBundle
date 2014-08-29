<?php

namespace Potogan\UtilityBundle\CSV;

use ArrayAccess, Iterator;

class CSV extends AbstractCSVOptions implements ArrayAccess, Iterator
{
    protected $position = 0;
    protected $rows = array();

    public function __construct($from = null)
    {
        if ($from) {
            $this->load($from);
        }
    }

    public function load($from)
    {
        if (is_string($from)) {
            $from = new CSVIterator($from);
            $from
                ->setDelimiter($this->delimiter)
                ->setEnclosure($this->enclosure)
                ->setEscape($this->escape)
            ;
        }

        if ($from instanceof CSVIterator) {
            foreach ($from as $row) {
                $this->rows[] = $row;
            }
        } elseif (is_array($from)) {
            $this->rows = $from;
        }
    }

    public function toArray()
    {
        return $this->rows;
    }

    public function toFile($file)
    {
        $res = is_resource($file) ? $file : fopen($file, 'w');

        foreach ($this->rows as $row) {
            fputcsv($res , $row, $this->delimiter, $this->enclosure);
        }

        if (!is_resource($file)) {
            fclose($res);
        }
    }

    // ArrayAccess implementation

    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->rows[] = $value;
        } else {
            $this->rows[$offset] = $value;
        }
    }

    public function offsetExists($offset)
    {
        return isset($this->rows[$offset]);
    }

    public function offsetUnset($offset)
    {
        unset($this->rows[$offset]);
    }

    public function offsetGet($offset)
    {
        return isset($this->rows[$offset]) ? $this->rows[$offset] : null;
    }

    // Iterator implementation

    public function rewind()
    {
        $this->position = 0;
    }

    public function current()
    {
        return $this->rows[$this->position];
    }

    public function key()
    {
        return $this->position;
    }

    public function next()
    {
        ++$this->position;
    }

    public function valid()
    {
        return isset($this->rows[$this->position]);
    }

}
