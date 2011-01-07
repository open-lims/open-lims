<?php
/**
 * @package item
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
require_once("interfaces/item_has_sample_gid.interface.php");

if (constant("UNIT_TEST") == false or !defined("UNIT_TEST"))
{
	require_once("access/item_has_sample_gid.access.php");
}

/**
 * Item Has Sample GID Management Class
 * @package item
 */
class ItemHasSampleGid implements ItemHasSampleGidInterface
{
	private $item_id;
	private $item_has_sample_gid;

	/**
	 * @param integer $item_id
	 */
    function __construct($item_id)
    {
    	if (is_numeric($item_id))
    	{
    		$this->item_has_sample_gid = new ItemHasSampleGid_Access($item_id);	    	
	    	$this->item_id = $item_id;	
    	}
    	else
    	{
    		$this->item_has_sample_gid = new ItemHasSampleGid_Access(null);
    		$this->item_id = null;
    	}
    }
    
    function __destruct()
    {
    	unset($this->item_id);
    	unset($this->item_has_sample_gid);
    }
    
    /**
     * Creates a new Item - Sample-GID connection
     * @param integer $item_id
     * @param integer $gid
     * @return integer
     */
   	public function create($item_id, $gid)
   	{
    	if ($this->item_has_sample_gid and is_numeric($item_id) and is_numeric($gid))
    	{
    		return $this->item_has_sample_gid->create($item_id, $gid);
    	}
    	else
    	{
    		return null;
    	}
    }
    
    /**
     * @return bool
     */
    public function delete()
    {
    	if ($this->item_has_sample_gid and $this->item_id)
    	{
    		return $this->item_has_sample_gid->delete();
    	}
    	else
    	{
    		return false;
    	}
    }
    
    /**
     * @return integer
     */    
    public function get_gid()
    {
    	if ($this->item_has_sample_gid and $this->item_id)
    	{
    		return $this->item_has_sample_gid->get_gid();
    	}
    	else
    	{
    		return null;
    	}
    }
    
    /**
     * @param integer $gid
     * @return bool
     */
    public function set_gid($gid)
    {
    	if ($this->item_has_sample_gid and $this->item_id)
    	{
    		return $this->item_has_sample_gid->set_gid($gid);
    	}
    	else
    	{
    		return false;
    	}
    }
    
}
?>