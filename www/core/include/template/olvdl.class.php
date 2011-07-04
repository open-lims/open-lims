<?php
/**
 * @package template
 * @version 0.4.0.0
 * @author Roman Konertz
 * @copyright (c) 2008-2010 by Roman Konertz
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
require_once("interfaces/olvdl.interface.php");

if (constant("UNIT_TEST") == false or !defined("UNIT_TEST"))
{
	require_once("access/olvdl_template.access.php");
}

/**
 * OLVDL Class
 * @package template
 */
class Olvdl
{
	private $olvdl;

	private $olvdl_id;
    private $data_entity_id;

    private $xml_array;
    
    /**
     * @param integer $olvdl_id
     */
    function __construct($olvdl_id)
    {
    	if ($olvdl_id == null)
    	{
    		$this->olvdl_id = null;
    		$this->olvdl = new OlvdlTemplate_Access(null);
    	}
    	else
    	{
    		$this->olvdl_id = $olvdl_id;
    		$this->olvdl = new OlvdlTemplate_Access($olvdl_id);
    		
    		$this->data_entity_id = $this->olvdl->get_data_entity_id();
    		
    		$xml_cache = new XmlCache($this->data_entity_id);
    		$this->xml_array = $xml_cache->get_xml_array();
    	}
    }
    
    function __destruct()
    {
    	if ($this->olvdl_id)
    	{
    		unset($this->olvdl_id);
    		unset($this->olvdl);
    		unset($this->data_entity_id);
    		unset($this->xml_array);
    	}
    }   
    
    /**
     * Creates a new OLVDL-Template in DB
     * @param integer $data_entity_id
     * @return integer
     */
    public function create($data_entity_id)
    {
    	if ($this->olvdl and is_numeric($data_entity_id))
    	{
    		if (OlvdlTemplate_Access::is_data_entity_id($data_entity_id) == true)
    		{
    			return null;	
    		}
    		else
    		{
    			return $this->olvdl->create($data_entity_id);	
    		}
    	}
    	else
    	{
    		return null;
    	}
    }
    
    /**
     * Deletes an OLVDL-Template from DB
     * @return bool
     */
    public function delete()
    {
    	if ($this->olvdl and $this->olvdl_id)
    	{
    		return $this->olvdl->delete();	
    	}
    	else
    	{
    		return false;
    	}
    }
    
    /**
     * @return array
     */     
    public function get_xml_array()
    {
    	if ($this->xml_array)
    	{
    		return $this->xml_array;
    	}
    	else
    	{
    		return null;
    	}
    }
}
?>