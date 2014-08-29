<?php
namespace Potogan\UtilityBundle\CSV\Exception;

use Exception;

class WrongColumnCountException extends Exception {

	public function __construct($expected, $found, $code = 0)
	{
		$count = count($found);
		parent::__construct(
			"CSV : Wrong column count ! Expected : $expected, found : $count (row : " . print_r($found, true) . ')',
			$code
		);
	}
}