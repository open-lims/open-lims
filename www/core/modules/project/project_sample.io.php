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
 * Project Sample Controller Class
 * @package project
 */
class ProjectSampleIO {
		
	public static function list_project_related_samples()
	{
		global $user, $project_security;

		if ($_GET[project_id])
		{
			if ($project_security->is_access(1, false) == true)
			{
				$project = new Project($_GET[project_id]);
				$project_item = new ProjectItem($_GET[project_id]);
		
				$item_array = $project_item->get_project_items();
		
				$template = new Template("languages/en-gb/template/projects/samples.html");
				
				if ($project_security->is_access(3, false) == true)
				{
					$template->set_var("add_sample", true);	
				}
				else
				{
					$template->set_var("add_sample", false);	
				}
				
				$paramquery = $_GET;
				$paramquery[nav] = "samples";
				$paramquery[run] = "add_to_project";
				unset($paramquery[nextpage]);
				$params = http_build_query($paramquery,'','&#38;');
				
				$template->set_var("add_sample_params", $params);
				
				
				$table_io = new TableIO("OverviewTable");
			
				$table_io->add_row("","symbol",false,16);
				$table_io->add_row("Sample ID","id",false,null);
				$table_io->add_row("Sample Name","name",false,null);
				$table_io->add_row("Date/Time","datetime",false,null);
				$table_io->add_row("Type/Template","template",false,null);
				$table_io->add_row("Curr. Depository","depository",false,null);
				$table_io->add_row("Owner","owner",false,null);
				$table_io->add_row("AV","av",false,16);
				
				if ($user->is_admin())
				{
					$table_io->add_row("","delete",false,16);
				}
				
				$content_array = array();	
				$entry_found = false;
				
				if (is_array($item_array) and count($item_array) >= 1)
				{	
					foreach($item_array as $key => $value)
					{
						$item = new Item($value);
					
						if (($sample_id = $item->get_sample_id()) == true)
						{
							$sample = new Sample($sample_id);
							$owner = new User($sample->get_owner_id());
					
							$column_array = array();
					
							$paramquery = $_GET;
							$paramquery[nav] = "samples";
							$paramquery[run] = "detail";
							$paramquery[sample_id] = $sample_id;
							unset($paramquery[page]);
							unset($paramquery[project_id]);
							$params = http_build_query($paramquery,'','&#38;');
							
							$column_array[symbol][link] 	= $params;
							$column_array[symbol][content]	= "<img src='images/icons/sample.png' alt='' style='border: 0;' />";
							$column_array[id][link] 		= $params;
							$column_array[id][content] 		= $sample->get_formatted_id();	
							$column_array[name][content] 	= $sample->get_name();;	
							$column_array[datetime] 		= $sample->get_datetime();
							$column_array[template]			= $sample->get_template_name();
							$column_array[depository]		= $sample->get_current_depository_name();
							$column_array[owner] 			= $owner->get_full_name(true);
							$column_array[av]				= "<img src='images/icons/grey_point.png' alt='' style='border: 0;' />";
	
							$delete_paramquery = $_GET;
							$delete_paramquery[nav] = "samples";
							$delete_paramquery[run] = "delete_project_association";
							$delete_paramquery[project_id] = $_GET[project_id];
							$delete_paramquery[sample_id] = $sample_id;
							$delete_params = http_build_query($delete_paramquery,'','&#38;');
						
							$column_array[delete][link] 	= $delete_params;
							$column_array[delete][content]	= "<img src='images/icons/delete_sample.png' alt='' style='border: 0;' />";
			
			
							array_push($content_array, $column_array);
							
							$entry_found = true;
						}	
					}				
				}
				
				if ($entry_found == false)
				{
					$content_array = null;
					$table_io->override_last_line("<span class='italic'>No Samples Found!</span>");
				}
				
				$table_io->add_content_array($content_array);	
				
				$template->set_var("table", $table_io->get_content($_GET[page]));		
				
				$template->output();
			}
			else
			{
				$exception = new Exception("", 1);
				$error_io = new Error_IO($exception, 200, 40, 2);
				$error_io->display_error();
			}
		}
		else
		{
			$exception = new Exception("", 1);
			$error_io = new Error_IO($exception, 200, 40, 3);
			$error_io->display_error();
		}
	}
}

?>