<?php
/**
 * @package extension
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
 * Extension Create Run Event
 * @package extension
 */
class ExtensionCreateRunEvent extends Event
{    
	private $extension_id;
	private $run_id;
	private $event_identifier;
	
	function __construct($extension_id, $run_id, $event_identifier)
    {
    	if (is_numeric($extension_id) and is_numeric($run_id) and $event_identifier)
    	{
    		parent::__construct();
    		$this->extension_id = $extension_id;
    		$this->run_id = $run_id;
    		$this->event_identifier = $event_identifier;
    	}
    	else
    	{
    		$this->extension_id = null;
    		$this->run_id = null;
    		$this->event_identifier = null;
    	}
    }
    
	public function get_extension_id()
    {
    	if ($this->extension_id)
    	{
    		return $this->extension_id;
    	}
    	else
    	{
    		return null;
    	}
    }
    
    public function get_run_id()
    {
    	if ($this->run_id)
    	{
    		return $this->run_id;
    	}
    	else
    	{
    		return null;
    	}
    }
    
	public function get_event_identifier()
    {
    	if ($this->event_identifier)
    	{
    		return $this->event_identifier;
    	}
    	else
    	{
    		return null;
    	}
    }
}

?>