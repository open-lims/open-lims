<?php
/**
 * @package organisation_unit
 * @version 0.4.0.0
 * @author Roman Konertz
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
 * Organisation Unit Quality Manager Create Event
 * @package organisation_unit
 */
class OrganisationUnitQualityManagerCreateEvent extends Event
{    
	private $organisation_unit_id;
	private $quality_manager_id;
	
	function __construct($organisation_unit_id, $quality_manager_id)
    {
    	if (is_numeric($organisation_unit_id) and is_numeric($quality_manager_id))
    	{
    		parent::__construct();
    		$this->organisation_unit_id = $organisation_unit_id;
    		$this->quality_manager_id = $quality_manager_id;
    	}
    	else
    	{
    		$this->organisation_unit_id = null;
    		$this->quality_manager_id = null;
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
    
  	public function get_quality_manager_id()
    {
    	if ($this->quality_manager_id)
    	{
    		return $this->quality_manager_id;
    	}
    	else
    	{
    		return null;
    	}
    }
}

?>