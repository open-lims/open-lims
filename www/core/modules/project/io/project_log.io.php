<?php
/**
 * @package project
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
 * Project Log IO Class
 * @package project
 */
class ProjectLogIO
{
	/**
	 * @throws ProjectIDMissingException
	 * @throws ProjectSecurityAccessDeniedException
	 */
	public static function list_project_related_logs()
	{
		global $project_security;
		
		if ($_GET['project_id'])
		{
			if ($project_security->is_access(1, false) == true)
			{
				$template = new HTMLTemplate("project/log.html");
				$template->set_var("get_array",serialize($_GET));
				
				if ($project_security->is_access(3, false) == true)
				{
					$template->set_var("write",true);
				}
				else
				{
					$template->set_var("write",false);
				}
				
				$project_log_array = ProjectLog::list_entries_by_project_id($_GET['project_id']);
				$entry_count = count($project_log_array);
				$number_of_pages = ceil($entry_count/constant("PROJECT_LOG_ENTRIES_PER_PAGE"));
				
				$template->output();
			}
			else
			{
				throw new ProjectSecurityAccessDeniedException();
			}
		}
		else
		{
			throw new ProjectIDMissingException();
		}
	}	
}
	
?>
