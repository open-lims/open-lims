<?php
/**
 * @package organisation_unit
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
 * Organisation Unit Post Delete Event
 * @package organisation_unit
 */
class OrganisationUnitPostDeleteEvent extends Event
{    
	private $organisation_unit_id;
	private $contains_projects;
	
	function __construct($organisation_unit_id, $contains_projects)
    {
    	if (is_numeric($organisation_unit_id))
    	{
    		parent::__construct();
    		$this->organisation_unit_id = $organisation_unit_id;
    		$this->contains_projects = $contains_projects;
    	}
    	else
    	{
    		$this->organisation_unit_id = null;
    		$this->contains_projects = false;
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
    
	public function get_contains_projects()
    {
    	if ($this->contains_projects)
    	{
    		return $this->contains_projects;
    	}
    	else
    	{
    		return false;
    	}
    }
}

?>