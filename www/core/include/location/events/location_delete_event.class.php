<?php
/**
 * @package location
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
 * Location Delete Event
 * @package location
 */
class LocationDeleteEvent extends Event
{    
	private $location_id;
	
	function __construct($location_id)
    {
    	if (is_numeric($location_id))
    	{
    		parent::__construct();
    		$this->location_id = $location_id;
    	}
    	else
    	{
    		$this->location_id = null;
    	}
    }
    
    public function get_location_id()
    {
    	if ($this->location_id)
    	{
    		return $this->location_id;
    	}
    	else
    	{
    		return null;
    	}
    }
}

?>