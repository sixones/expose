<?php
/******************************************************************************
Expose - XML Serialiser

Developer      : Adam Livesley
Plug-in Name   : Expose

[84degrees.com](http://www.84degrees.com/)

******************************************************************************/

/**
 AL_Expose_Serialiser is a custom XML serialiser to take php data and serialise this
 to an xml document. this serialiser does not require any extra extensions in php, and doesnt
 require us to manually serialise the data.

 its all automagically generated
**/

class AL_Expose_Serialiser
{
	var $dom;
	var $ignore = array('Mint');
	
	function AL_Expose_Serialiser()
	{
		$this->dom = new DOMDocument('1.0');
	}
	
	function serialise($data)
	{
		if (is_object($data))
		{
			$parent = $this->writeParentTag(get_Class($data) , $this->dom);

			$this->serialiseObject($parent, $data);
			
			return $this->dom->saveXML();
		}
		else if (is_array($data))
		{
			$parent = $this->writeParentTag('array', $this->dom);

			$this->serialiseArray($parent, $data);
			
			return $this->dom->saveXML();
		}
		
		exit("Serialise Requires data to be type object or string");
	}
	
	function serialiseValue($parent, $val, $tagName, $attr = null)
	{
		if (is_object($val))
		{
			if ($tagName != null)
			{
				$parentObj = $this->writeParentTag($tagName, $parent, $attr);
			}
			else
			{
				$parentObj = $this->writeParentTag(get_class($val), $parent, $attr);
			}
			
			if (in_array($tagName, $this->ignore)) return;
			$this->serialiseObject($parentObj, $val, $tagName);
		}
		else if (is_array($val))
		{
			$parentObj = $this->writeParentTag($tagName, $parent, $attr);
			
			if (in_array($tagName, $this->ignore)) return;
			$this->serialiseArray($parentObj, $val, $tagName);
		}
		else
		{
			$this->writeValueTag($parent, $val, $tagName, $attr);
		}
	}
	
	function serialiseObject($parent, $obj, $tagName = null)
	{
		$props = get_object_vars($obj);
		
		foreach ($props as $key => $val)
		{
			$this->serialiseValue($parent, $val, $key);
		}
	}
	
	function serialiseArray($parent, $array, $tagName = null)
	{
		if (is_array($array))
		{
			foreach ($array as $key => $val)
			{
				$attr = null;
				
				if (!is_int($key))
				{
					$attr = array('name' => $key, 'type' => getType($val));
				}
				
				$key = get_class($val) != null ? get_class($val) : is_int($key) ? gettype($val) : $key;
				
				$this->serialiseValue($parent, $val, $key, $attr);
			}
		}
	}
	
	function writeParentTag($tagName, $parent, $attr = null)
	{
		$tag = $this->dom->createElement(str_replace(' ', '_', strtolower($tagName)));
		
		if ($attr != null)
		{
			foreach ($attr as $key => $val)
			{
				$obj = $this->dom->createAttribute($key);
				$objText = $this->dom->createTextNode($val);
				
				$obj->appendChild($objText);
				$tag->appendChild($obj);
			}
		}
		
		$parent->appendChild($tag);
		
		return $tag;
	}
	
	function writeValueTag($parent, $val, $tagName = null, $attr = null)
	{
		if ($tagName == null)
		{
			$tagName = get_class($val);
		}

		if ($tagName == null) return;
		
		$tag = $this->dom->createElement(str_replace(' ', '_', strtolower($tagName)));
		$text = $this->dom->createTextNode($val);
		
		if ($attr != null)
		{
			foreach ($attr as $key => $val)
			{
				$obj = $this->dom->createAttribute($key);
				$objText = $this->dom->createTextNode($val);
			
				$obj->appendChild($objText);
				$tag->appendChild($obj);
			}
		}
		
		$tag->appendChild($text);
		
		$parent->appendChild($tag);
	}
}

?>