<?php 
/**
 * @package project
 * @version 0.4.0.0
 * @author Roman Quiring
 * @copyright (c) 2008-2010 by Roman Konertz, Roman Quiring
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
 * Project Navigation IO Class
 * @package project
 */
class ProjectNavigationIO
{
	public static function get_active() 
	{
		if (is_numeric($_GET[project_id]))
		{
			if (Project::exist_project($_GET[project_id]))
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		else
		{
			return false;
		}
	}
	
	public static function get_icon()
	{
		if(self::get_active()==true) 
		{
			return "images/icons/project.png";
		}
		else 
		{
			return "images/icons/project_na.png";
		}
	}
	
	public static function get_ajax_url()
	{
		return "core/modules/project/navigation/project_navigation.ajax.php";
	}
}

?>