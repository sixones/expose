<?php

class Exposure_Mint extends AL_Expose_Exposure
{
	var $_cfgIgnore = array('password', 'activationKey', 'manifest', 'preferences', 'pepperShaker', 'update', 'moderate', 'debug', 'installDir', 'installTrim', 'installDomain', 'installed', 'panes', 'pepperLookUp');
	
	function info()
	{
		global $Mint;
		
		$cfg = $Mint->cfg;
		
		$this->data['expose_version'] = AL_EXPOSE_VERSION;
		
		foreach ($cfg as $key => $val)
		{
			if (!in_array($key, $this->_cfgIgnore))
			{
				$this->data[$key] = $val;
			}
		}
	}
}

?>