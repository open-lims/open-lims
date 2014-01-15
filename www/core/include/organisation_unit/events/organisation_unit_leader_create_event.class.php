<?php
/**
 * @package organisation_unit
 * @version 0.4.0.0
 * @author Roman Konertz <konertz@open-lims.org>
 * @copyright (c) 2008-2014 by Roman Konertz
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
 * Organisation Unit Leader Create Event
 * @package organisation_unit
 */
class OrganisationUnitLeaderCreateEvent extends Event
{    
	private $organisation_unit_id;
	private $leader_id;
	
	function __construct($organisation_unit_id, $leader_id)
    {
    	if (is_numeric($organisation_unit_id) and is_numeric($leader_id))
    	{
    		parent::__construct();
    		$this->organisation_unit_id = $organisation_unit_id;
    		$this->leader_id = $leader_id;
    	}
    	else
    	{
    		$this->organisation_unit_id = null;
    		$this->leader_id = null;
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
    
  	public function get_leader_id()
    {
    	if ($this->leader_id)
    	{
    		return $this->leader_id;
    	}
    	else
    	{
    		return null;
    	}
    }
}

?>