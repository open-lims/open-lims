<?php
/**
 * @package base
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
 * 
 */
require_once("interfaces/cron.interface.php");

if (constant("UNIT_TEST") == false or !defined("UNIT_TEST"))
{
	require_once("events/cron_event.class.php");
}

/**
 * Cron Class
 * @package base
 */
class Cron implements CronInterface
{
	private static $last_run_datetime;
	private static $last_run_id;
	
	private static function init()
	{
		if (!self::$last_run_datetime)
		{
			self::$last_run_datetime = Registry::get_value("base_cron_last_run_datetime");
		}
		
		if (!self::$last_run_id)
		{
			self::$last_run_id = Registry::get_value("base_cron_last_run_id");
		}
	}
	
	/**
	 * @see CronInterface::run()
	 */
	public static function run()
	{
		if (self::check() == true)
		{
			$last_run_daily_datetime = Registry::get_value("base_cron_last_run_daily_datetime");
			$last_run_weekly_datetime = Registry::get_value("base_cron_last_run_weekly_datetime");
			
			$last_run_daily_datetime_handler = new DatetimeHandler($last_run_daily_datetime);
			$last_run_weekly_datetime_handler = new DatetimeHandler($last_run_weekly_datetime);
			$current_datetime_handler = new DatetimeHandler(date("Y-m-d H:i:s"));
			
			if ($last_run_daily_datetime_handler->distance($current_datetime_handler) >= 86400)
			{
				$daily = true;
			}
			else
			{
				$daily = false;
			}
			
			if ($last_run_weekly_datetime_handler->distance($current_datetime_handler) >= 604800)
			{
				$weekly = true;
			}
			else
			{
				$weekly = false;
			}
			
			
			$cron_event = new CronEvent(self::$last_run_id, $daily, $weekly);
			$event_handler = new EventHandler($cron_event);
			
			if ($event_handler->get_success() == true)
			{
				if ((self::$last_run_id+1) > 256)
				{
					Registry::set_value("base_cron_last_run_id", 1);
					self::$last_run_id = 1;
				}
				else
				{
					Registry::set_value("base_cron_last_run_id", (self::$last_run_id+1));
					self::$last_run_id = (self::$last_run_id+1);
				}
				Registry::set_value("base_cron_last_run_datetime", date("Y-m-d H:i:s"));
				self::$last_run_datetime = date("Y-m-d H:i:s");
				 
				if ($daily == true)
				{
				 	Registry::set_value("base_cron_last_run_daily_datetime", date("Y-m-d H:i:s"));
				}
				 
				if ($weekly == true)
				{
				 	Registry::set_value("base_cron_last_run_weekly_datetime", date("Y-m-d H:i:s"));
				}
			}
		}
	}
	
	/**
	 * @see CronInterface::check()
	 * @return bool
	 */
	public static function check()
	{
		self::init();
		
		if (self::$last_run_datetime and is_numeric(self::$last_run_id))
		{
			$last_run_datetime_handler = new DatetimeHandler(self::$last_run_datetime);
			$current_datetime_handler = new DatetimeHandler(date("Y-m-d H:i:s"));
			
			if ($last_run_datetime_handler->distance($current_datetime_handler) >= 3600)
			{
				return true;
			}
			else
			{
				return false;
			}
		}
	}
}
?>