<?php

class Exposure_Visits extends AL_Expose_Exposure
{
	function fetch()
	{
		global $Mint;

		$this->data = $Mint->data;
	}
}

?>