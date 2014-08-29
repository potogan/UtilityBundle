<?php

namespace Potogan\UtilityBundle\CSV;

use Potogan\UtilityBundle\CSV\Exception\WrongColumnCountException;

class NamedCSVIterator extends CSVIterator
{
    protected $columns;

    public function setColumns($columns)
    {
        $this->columns = $columns;

        return $this;
    }

    public function current()
    {
        $res = parent::current();

        if ($this->columns) {
            if (count($this->columns) != count($res)) {
                throw new WrongColumnCountException(count($this->columns), $res);
            }

            $res = array_combine($this->columns, $res);
        }

        return $res;
    }

}
