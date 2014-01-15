<?php
/**
 * @package base
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
 * Cron Event
 * @package base
 */
class CronEvent extends Event
{   
	private $cron_id;
	private $daily;
	private $weekly;
	
	function __construct($cron_id, $daily = false, $weekly = false)
    {
    	if (is_numeric($cron_id))
    	{
    		$this->cron_id = $cron_id;
    	}
    	else
    	{
    		$this->cron_id = null;
    	}
    	
    	$this->daily = $daily;
    	$this->weekly = $weekly;
    }
    
    public function get_cron_id()
    {
    	if ($this->cron_id)
    	{
    		return $this->cron_id;
    	}
    	else
    	{
    		return null;
    	}
    }
    
	public function get_daily()
    {
    	if ($this->daily)
    	{
    		return $this->daily;
    	}
    	else
    	{
    		return null;
    	}
    }
    
	public function get_weeky()
    {
    	if ($this->weekly)
    	{
    		return $this->weekly;
    	}
    	else
    	{
    		return null;
    	}
    }
}

?>