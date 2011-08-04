<?php
/**
 * @package organisation_unit
 * @version 0.4.0.0
 * @author Roman Konertz <konertz@open-lims.org>
 * @copyright (c) 2008-2011 by Roman Konertz
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
 * Organisation Unit Create Event
 * @package organisation_unit
 */
class OrganisationUnitCreateEvent extends Event
{    
	private $organisation_unit_id;
	private $stores_data;
	
	function __construct($organisation_unit_id, $stores_data)
    {
    	if (is_numeric($organisation_unit_id))
    	{
    		parent::__construct();
    		$this->organisation_unit_id = $organisation_unit_id;
    		$this->stores_data = $stores_data;
    	}
    	else
    	{
    		$this->organisation_unit_id = null;
    		$this->stores_data = false;
    	}
    }
    
    public function get_organisation_unit_id()
    {
    	if ($this->organisation_unit_id)
    	{
    		return $this->organisation_unit_id;
    	}
    	else
    	{
    		return null;
    	}
    }
    
	public function get_stores_data()
    {
    	if ($this->stores_data)
    	{
    		return $this->stores_data;
    	}
    	else
    	{
    		return false;
    	}
    }
}

?>