<?php
/**
 * @package template
 * @version 0.4.0.0
 * @author Roman Konertz <konertz@open-lims.org>
 * @copyright (c) 2008-2013 by Roman Konertz
 * @license GPLv3
 * 
 * This file is part of Open-LIMS
 * Available at http://www.open-lims.org
 * 
 * This program is free software;
 * you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation;
 * version 3 of the License.
 * 
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
 * See the GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, see <http://www.gnu.org/licenses/>.
 */

/**
 * 
 */
require_once("interfaces/xml_cache.interface.php");

if (constant("UNIT_TEST") == false or !defined("UNIT_TEST"))
{
	require_once("access/xml_cache.access.php");
	require_once("access/xml_cache_element.access.php");
}

/**
 * XML Caching Class
 * @package template
 */
class XmlCache implements XmlCacheInterface
{
	private $data_entity_id;
	private $xml_cache;
	private $xml_cache_id;
	private $xml_array;
	
	/**
	 * @see XmlCacheInterface::__construct()
	 * @param integer $data_entity_id
	 */
    function __construct($data_entity_id)
    {
    	if (is_numeric($data_entity_id))
    	{
	    	$this->data_entity_id = $data_entity_id;
	    	$this->xml_cache_id = XmlCache_Access::get_id_by_data_entity_id($data_entity_id);
	    	
	    	if (is_numeric($this->xml_cache_id))
	    	{
		    	$this->xml_cache = new XmlCache_Access($this->xml_cache_id);
		    	
		    	if ($this->is_file() == true)
		    	{
		    		if ($this->is_checksum() == true)
		    		{
		    			$this->xml_array = XmlCacheElement_Access::get_all_content_by_toid($this->xml_cache_id);
		    		}
		    		else
		    		{
		    			$this->rewrite();
		    		}
		    	}
		    	else
		    	{
		    		$this->rewrite();
		    	}
	    	}
	    	else
	    	{
	    		$this->init();
	    	}
    	}
    }
    
    /**
     * @see XmlCacheInterface::get_xml_array()
     * @return array
     */
    public function get_xml_array()
    {
    	return $this->xml_array;
    }

	/**
	 * Deletes all entries and init caching again
	 * @return bool
	 */
	private function rewrite()
	{
		if ($this->xml_cache_id)
		{
			if (XmlCacheElement_Access::delete_all_by_toid($this->xml_cache_id) == true)
			{
				if ($this->xml_cache->delete() == true)
				{
					return $this->init();
				}
			}
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * Inits a caching procedure
	 * @return bool
	 */
	private function init()
	{
		global $transaction;
		
		if ($this->data_entity_id)
		{
			$transaction_id = $transaction->begin();

    		$file_id = File::get_file_id_by_data_entity_id($this->data_entity_id);
    		$file = File::get_instance($file_id);
    		
    		$folder = Folder::get_instance($file->get_parent_folder());
			$folder_path = $folder->get_path();

			$extension_array = explode(".",$file->get_name());
			$extension_array_length = substr_count($file->get_name(),".");
			
			$file_path = constant("BASE_DIR")."/".$folder_path."/".$this->data_entity_id."-1.".$extension_array[$extension_array_length];
    		
    		$this->xml_string = $file->get_file_content();

    		if (strlen($this->xml_string) > 0)
    		{
	    		$xml = new Xml($this->xml_string);
	    		$xml->parser();
	    		$this->xml_array = $xml->get_array();
			
				if (is_array($this->xml_array) and count($this->xml_array) >= 1)
				{
					$this->xml_cache = new XmlCache_Access(null);
					$id = $this->xml_cache->create($this->data_entity_id, $file_path, md5_file($file_path));
					
					foreach($this->xml_array as $key => $value)
					{
						$xml_cache_element = new XmlCacheElement_Access(null);
						$xml_cache_element->create($id, $value[0], $value[1], $value[2], $value[3]);
					}
					
					$this->__construct($this->data_entity_id);
					
					if ($transaction_id != null)
					{
						$transaction->commit($transaction_id);
					}
					return true;
				}
				else
				{
					if ($transaction_id != null)
					{
						$transaction->rollback($transaction_id);
					}
					return false;
				}
    		}
    		else
    		{
    			if ($transaction_id != null)
				{
					$transaction->rollback($transaction_id);
				}
				return false;
    		}
		}
	}

	/**
	 * @return bool
	 */
    private function is_checksum()
    {
    	if ($this->xml_cache)
    	{
    		$path 		= $this->xml_cache->get_path();
    		$checksum 	= $this->xml_cache->get_checksum();
    		
    		if (md5_file($path) == $checksum)
    		{
    			return true;
    		}
    		else
    		{
    			return false;
    		}
    	}
    	else
    	{
    		return false;
    	}
    }
    
    /**
     * @return bool
     */
    private function is_file()
    {
    	if ($this->xml_cache)
    	{
    		$path = $this->xml_cache->get_path();
    		
    		if (file_exists($path))
    		{
    			return true;
    		}
    		else
    		{
    			return false;
    		}
    	}
    	else
    	{
    		return false;
    	}
    }
    
}
?>