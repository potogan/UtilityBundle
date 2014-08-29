<?php

namespace Potogan\UtilityBundle\CSV;

use Iterator;

class CSVIterator extends AbstractCSVOptions implements Iterator
{
    protected $filename;
    protected $file;

    protected $currentRow = null;
    protected $currentIndex;

    public function __construct($filename)
    {
        $this->filename = $filename;
        $this->file = fopen($this->filename, 'r');

        $this->currentIndex = 0;
    }

    public function __destruct()
    {
        fclose($this->file);
    }

    public function current()
    {
        $this->init();

        return $this->currentRow;
    }

    public function key()
    {
        return $this->currentIndex;
    }

    public function next()
    {
        $this->init();
        if (!feof($this->file)) {
            $this->readRow();
            $this->currentIndex++;
        } else {
            $this->currentRow = false;
        }
    }

    public function valid()
    {
        $this->init();

        return is_array($this->currentRow);
    }

    public function rewind()
    {
        $this->currentIndex = 0;
        rewind($this->file);
        $this->currentRow = null;
    }

    protected function init()
    {
        if (is_null($this->currentRow) && $this->currentIndex === 0) {
            $this->readRow();
        }
    }

    protected function readRow()
    {
        $this->currentRow = fgetcsv($this->file, 0, $this->delimiter, $this->enclosure, $this->escape);
    }
}
