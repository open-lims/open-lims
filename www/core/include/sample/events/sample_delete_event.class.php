<?php
/**
 * @package sample
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
 * Sample Folder Create Event
 * @package sample
 */
class SampleDeleteEvent extends DeleteEvent
{    
	private $sample_id;
	
	function __construct($sample_id)
    {
    	if (is_numeric($sample_id))
    	{
    		parent::__construct();
    		$this->sample_id = $sample_id;
    	}
    	else
    	{
    		$this->sample_id = null;
    	}
    }
    
    public function get_sample_id()
    {
    	if ($this->sample_id)
    	{
    		return $this->sample_id;
    	}
    	else
    	{
    		return null;
    	}
    }
}

?>