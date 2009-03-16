<?php

class Exposure_Peppers extends AL_Expose_Exposure
{
	function fetch()
	{
		global $Mint;
		
		$this->data = $Mint->pepper;
	}
}

?>