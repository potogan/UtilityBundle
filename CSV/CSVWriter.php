<?php

namespace Potogan\UtilityBundle\CSV;

use ArrayAccess, Iterator;

class CSVWriter extends AbstractCSVOptions
{
    protected $handle;

    public function __construct($file)
    {
        if (is_resource($file)) {
            $this->handle = $file;
        } else {
            $this->handle = fopen($file, 'w');
        }
    }

    public function write($row)
    {
        return fputcsv($this->handle, $row, $this->delimiter, $this->enclosure);
    }

    public function close()
    {
        if (is_resource($this->handle)) {
            fclose($this->handle);
            $this->handle = null;
        }
    }
}
