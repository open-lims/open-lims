<?php
/**
 * @package project
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
 * 
 */
require_once("interfaces/project_item_factory.interface.php");

/**
 * Project Item Factory Class
 * @package project
 */
class ProjectItemFactory implements ProjectItemFactoryInterface
{
	/**
	 * @todo check over time tasks via event
	 */
	public static function create($project_id, $item_id, $gid, $keywords, $description)
	{
		global $transaction;
		
		if ($transaction->is_in_transction() == true)
		{
			$project = new Project($project_id);
			$project_item = new ProjectItem($project_id);
			
			if ($project_item->set_gid($gid) == false)
			{
				return false;
			}
			
			if ($project_item->set_status_id($project->get_current_status_id()) == false)
			{
				return false;	
			}
			
			if ($project_item->set_item_id($item_id) == false)
			{
				return false;	
			}
			
			if ($project_item->link_item() == false)
			{
				return false;	
			}
			
			if ($project_item->set_item_status() == false)
			{
				return false;	
			}
		
			if (($class_name = $project_item->is_classified()) == true)
			{
				if ($project_item->set_class($class_name) == false)
				{
					return false;
				}
			}
			
			if ($description_required == true xor $keywords_required == true)
			{
				if ($description_required == false and $keywords_required == true)
				{
					if ($project_item->set_information(null,$_POST[keywords]) == false)
					{
						return false;
					}
				}
				else
				{
					if ($project_item->set_information($_POST[description],null) == false)
					{
						return false;
					}
				}
			}
			else
			{
				if ($description_required == true and $keywords_required == true)
				{
					if ($project_item->set_information($_POST[description],$_POST[keywords]) == false)
					{
						return false;
					}
				}
			}
			
			if ($project_item->create_log_entry() == false)
			{
				return false;
			}
			else
			{
				ProjectTask::check_over_time_tasks($project_id);
				return true;
			}
		}
		else
		{
			return false;
		}
	}
}
?>