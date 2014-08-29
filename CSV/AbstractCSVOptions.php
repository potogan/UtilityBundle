<?php
namespace Potogan\UtilityBundle\CSV;

abstract class AbstractCSVOptions {
	protected $delimiter = ',';
	protected $enclosure = '"';
	protected $escape    = '\\';


	// option setters
	public function setDelimiter($delimiter)
	{
		$this->delimiter = $delimiter;

		return $this;
	}

	public function setEnclosure($enclosure)
	{
		$this->enclosure = $enclosure;

		return $this;
	}

	public function setEscape($escape)
	{
		$this->escape = $escape;

		return $this;
	}

}