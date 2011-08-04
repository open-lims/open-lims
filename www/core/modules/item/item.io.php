<?php
/**
 * @package item
 * @version 0.4.0.0
 * @author Roman Konertz <konertz@open-lims.org>
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
 * Item IO Class
 * @package item
 */
class ItemIO
{
	public static function information($link, $description, $keywords)
	{
		$template = new Template("template/item/information.html");
		
		$template->set_var("link", $link);
		
		if (!$_POST[description] and $_POST[submitbutton] == "next" and $description == true)
		{
			$template->set_var("error","You must enter a description!");	
		}
		elseif (!$_POST[keywords] and $_POST[submitbutton] == "next" and $keywords)
		{
			$template->set_var("error","You must enter keywords!");		
		}
		else
		{
			$template->set_var("error",false);		
		}
		
		if ($_POST[description])
		{
			$template->set_var("description_value",$_POST[description]);	
		}
		else
		{
			$template->set_var("description_value","");	
		}
		
		if ($_POST[keywords])
		{
			$template->set_var("keywords_value",$_POST[keywords]);	
		}
		else
		{
			$template->set_var("keywords_value","");	
		}
		
		if ($description == true)
		{
			$template->set_var("description",true);
		}
		else
		{
			$template->set_var("description",false);
		}
		
		if ($keywords)
		{
			$template->set_var("keywords",true);
		}
		else
		{
			$template->set_var("keywords",false);
		}
		
		$template->output();	
	}
	
	/**
	 * @todo method is not used at the moment
	 */
	private static function administration_folder()
	{
		if ($_GET[folder_id])
		{
			$table_io = new TableIO("OverviewTable");
			
			$table_io->add_row("","symbol",false,16);
			$table_io->add_row("Name","name",false,null);
			$table_io->add_row("Date/Time","datetime",false,null);
			$table_io->add_row("Owner","owner",false,null);
			$table_io->add_row("Number of Items","number",false,null);
			
			$folder = Folder::get_instance($_GET[folder_id]);
			
			$item_class_array_cardinality = 0;
			
			$counter = 0;
	
			if (!$_GET[page] or $_GET[page] == 1)
			{
				$page = 1;
				$counter_begin = 0;
				if ($item_class_array_cardinality > 25)
				{
					$counter_end = 24;
				}
				else
				{
					$counter_end = $item_class_array_cardinality-1;
				}
			}
			else
			{
				if ($_GET[page] >= ceil($item_class_array_cardinality/25))
				{
					$page = ceil($item_class_array_cardinality/25);
					$counter_end = $item_class_array_cardinality;
				}
				else
				{
					$page = $_GET[page];
					$counter_end = (25*$page)-1;
				}
				$counter_begin = (25*$page)-25;
			}
						
			$template = new Template("template/item/item_administration_folder.html");
			
			$template->set_var("title", $folder->get_name());	
				
			$template->set_var("table", $table_io->get_table($page ,$item_class_array_cardinality));		
			
			$template->output();
		}
		else
		{
			$exception = new Exception("", 1);
			$error_io = new Error_IO($exception, 20, 30, 3);
			$error_io->display_error();
		}
	}
	
	public static function method_handler()
	{
		switch($_GET[run]):

			case("administration_folder"):
				$this->administration_folder();
			break;
			
			case("administaration"):
				
			break;
			
			case("add_class"):
				
			break;

			case("edit_class"):
				
			break;
			
			case("delete_class"):
				
			break;
			
			case("add_information"):
				
			break;
			
			case("edit_information"):
				
			break;
			
			case("delete_information"):
				
			break;

			default:
			break;
			
		endswitch;
	}
	
}

?>
