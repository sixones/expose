<?php
/******************************************************************************
Expose

Developer      : Adam Livesley
Plug-in Name   : Expose

[84degrees.com](http://www.84degrees.com/)

******************************************************************************/

$installPepper = "AL_Expose";
define('AL_EXPOSE_VERSION', '0.0.1.2'); // version 0.0, beta 1, build 2

class AL_Expose extends Pepper
{
	var $version = AL_EXPOSE_VERSION;
	var $info = array
	(
		'pepperName'	=>	'Expose',
		'pepperUrl'		=>	'http://84degrees.com/expose/',
		'pepperDesc'	=>	'Provides an XML based API for accessing Mint data.',
		'developerName'	=>	'Adam Livesley',
		'developerUrl'	=> 	'http://84degrees.com/'
	);
	
	var $prefs = array
	(
		'apiKey' => "{md5(rand() * 388382)}"
	);
	
	/**************************************************************************
	 onDisplayPreferences()
	 **************************************************************************/
	function onDisplayPreferences() 
	{
		$preferences = array();

		$preferences['API Key']	= <<<HERE
		<table>
			<tr>
				<td><input type="text" name="apiKey" id="apiKey" value="{$this->prefs['apiKey']}" style="width: 98%" /></td>
			</tr>
			<tr>
				<td><label for"apiKey">Use the API key to authenticate clients and allow them to connect to expose.</label></td>
			</tr>
			<tr>
				<td>If the API key field is empty, you can save the settings to automatically generate an API key.</td>
			</tr>
		</table>
HERE;
		
		return $preferences;
	}
	
	/**************************************************************************
	 onSavePreferences()
	 **************************************************************************/
	function onSavePreferences() 
	{
		if ($this->prefs['apiKey'] == null || $_POST['apiKey'] == '' || $_POST['apiKey'] == ' ')
		{
			$this->prefs['apiKey'] = md5(rand() * 388382);
		}
		else
		{
			$this->prefs['apiKey'] = $_POST['apiKey'];
		}
	}
	
	/**************************************************************************
	 isCompatible()
	 **************************************************************************/
	function isCompatible()
	{
	    if ($this->Mint->version >= 120)
	    {
	        return array
	        (
	            'isCompatible'  => true
	        );
	    }
	    else
	    {
	        return array
	        (
	            'isCompatible'  => false,
	            'explanation'   => '<p>This Pepper is only compatible with Mint 1.2 and higher.</p>'
	    );
	    }
	}
}

?>