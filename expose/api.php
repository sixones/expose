<?php
/******************************************************************************
Expose - API Interface

Developer      : Adam Livesley
Plug-in Name   : Expose

[84degrees.com](http://www.84degrees.com/)

******************************************************************************/

if (!defined('MINT_ROOT')) { define('MINT_ROOT', '../../../'); }
if (isset($_GET['errors'])) { error_reporting(E_ALL); } else { error_reporting(0); }

define('MINT', true);

include_once('classes/serialiser.php');

include(MINT_ROOT.'app/lib/mint.php');
include(MINT_ROOT.'app/lib/pepper.php');
include(MINT_ROOT.'config/db.php');

include_once('class.php');

class AL_Expose_API
{
	var $mint;
	var $pepper;
	var $serialiser;

	function AL_Expose_API()
	{
		global $Mint;
		
		if (!isset($Mint))
		{
			echo 'Could not find Mint db.php';
			return;
		}
		
		$this->serialiser = new AL_Expose_Serialiser();
		
		$this->mint = $Mint;
		$this->mint->loadPepper();
		
		if ($this->is_pepper_okay())
		{
			$this->fetch_expose_prefs();
			
			if ($this->has_authentication())
			{
				if (!$this->run_exposure()) {
					$this->serialise($this->mint);
				}
			}
		}
		else
		{
			// dead ....
			$this->serialise_error(2, 'Expose Pepper not installed in Mint. Please check your Mint installation.');
		}
	}
	
	function get_var($key)
	{
		return (isset($_REQUEST[$key]) ? $_REQUEST[$key] : false);
	}
	
	function is_pepper_okay()
	{
		foreach($this->mint->cfg['pepperShaker'] as $pepper)
		{
			if ($pepper['class'] == 'AL_Expose')
			{
				return true;
			}
		}

		return false;
	}

	function run_exposure()
	{
		if ($this->get_var('method'))
		{
			$method = $this->get_var('method');
			$method = split(':', $method);
			
			$exposure_name = 'Exposure_' . ucwords($method[0]);

			include('classes/exposure.php');
			include('exposures/' . $method[0] . '.php');

			$exposure = new $exposure_name();
			
			if (isset($method[1]))
			{
				$method_name = $method[1];
				$exposure->$method_name();
			}
			
			$this->serialise($exposure->data);
			
			return true;
		}
		
		return false;
	}
	
	function has_authentication()
	{
		// does this exposure want authentication? 
		if ($this->get_var('api') && $this->get_var('api') == $this->pepper->prefs['apiKey'])
		{
			return true;
		}
		else
		{
			$this->serialise_error(1, 'Invalid authentication token, please check your client.');
		}
		
		return false;
	}
	
	function fetch_expose_prefs()
	{
		foreach ($this->mint->pepper as $pepper)
		{
			if ($pepper->info['pepperName'] == "Expose")
			{
				$this->pepper = $pepper;
				return true;
			}
		}
		
		return false;
	}
	
	function load_exposure()
	{
		
	}
	
	function serialise($data)
	{
		header('Content-Type: text/xml');
		
		echo $this->serialiser->serialise($data);
	}
	
	function serialise_error($code, $message)
	{
		$error->code = $code;
		$error->message = $message;
		
		$this->serialise($error);
	}
}

new AL_Expose_API();

?>