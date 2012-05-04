<?php
/**
 * @package data
 * @version 0.4.0.0
 * @author Roman Konertz <konertz@open-lims.org>
 * @copyright (c) 2008-2012 by Roman Konertz
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
 * Data Entity Link Event
 * @package data
 */
class DataEntityLinkEvent extends Event
{
	private $data_entity_id;    
	private $get_array;
	
	function __construct($data_entity_id, $get_array)
    {
    	if (is_numeric($data_entity_id) and is_array($get_array))
    	{
    		parent::__construct();
    		$this->data_entity_id = $data_entity_id;
    		$this->get_array = $get_array;
    	}
    	else
    	{
    		$this->data_entity_id = null;
    		$this->get_array = null;
    	}
    }
    
 	public function get_data_entity_id()
    {
    	if ($this->data_entity_id)
    	{
    		return $this->data_entity_id;
    	}
    	else
    	{
    		return null;
    	}
    }
    
    public function get_get_array()
    {
    	if ($this->get_array)
    	{
    		return $this->get_array;
    	}
    	else
    	{
    		return null;
    	}
    }

}

?>