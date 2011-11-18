<?php
/**
 * @package base
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
 * 
 */
$GLOBALS['autoload_prefix'] = "../";
require_once("../../base/ajax.php");

/**
 * Organisation Unit AJAX IO Class
 * @package base
 */
class AdminGeneralAjax extends Ajax
{	
	function __construct()
	{
		parent::__construct();
	}
	
	public function list_languages($json_row_array, $json_argument_array, $get_array, $css_page_id, $css_row_sort_id, $entries_per_page, $page, $sortvalue, $sortmethod)
	{		
		$argument_array = json_decode($json_argument_array);
		
		$list_request = new ListRequest_IO();
		$list_request->set_row_array($json_row_array);
	
		if (!is_numeric($entries_per_page) or $entries_per_page < 1)
		{
			$entries_per_page = 20;
		}
		
		$list_array = Environment_Wrapper::list_languages($sortvalue, $sortmethod, ($page*$entries_per_page)-$entries_per_page, ($page*$entries_per_page));
	
		
		if (is_array($list_array) and count($list_array) >= 1)
		{
			foreach($list_array as $key => $value)
			{
				$list_array[$key][symbol] = "<img src='images/icons/language.png' alt='N' border='0' />";
			}
		}
		else
		{
			$list_request->override_last_line("<span class='italic'>No results found!</span>");
		}
			
		$list_request->set_array($list_array);
		
		return $list_request->get_page($page);
	}
	
	public function count_languages($json_argument_array)
	{
		return Environment_Wrapper::count_languages();
	}
	
	public function list_timezones($page, $sortvalue, $sortmethod)
	{
		
	}
	
	public function list_paper_sizes($page, $sortvalue, $sortmethod)
	{
		$list = new ListStat_IO(Environment_Wrapper::count_paper_sizes(), 20, "OrganisationUnitAdminListPage");

		$list->add_row("","symbol",false,"16px");
		$list->add_row("Name","name",true,null,"BaseGeneralAdminListSortName");
		$list->add_row("Width","width",false,null);
		$list->add_row("Height","height",false,null);
		$list->add_row("Left-M.","margin_left",false,null);
		$list->add_row("Right-M.","margin_right",false,null);
		$list->add_row("Top-M.","margin_top",false,null);
		$list->add_row("Bottom-M.","margin_bottom",false,null);
		$list->add_row("","edit",false,"20px");
		$list->add_row("","delete",false,"20px");
		
		if ($page)
		{
			if ($sortvalue and $sortmethod)
			{
				$result_array = Environment_Wrapper::list_paper_sizes($sortvalue, $sortmethod, ($page*20)-20, ($page*20));
			}
			else
			{
				$result_array = Environment_Wrapper::list_paper_sizes(null, null, ($page*20)-20, ($page*20));
			}				
		}
		else
		{
			if ($sortvalue and $sortmethod)
			{
				$result_array = Environment_Wrapper::list_paper_sizes($sortvalue, $sortmethod, 0, 20);
			}
			else
			{
				$result_array = Environment_Wrapper::list_paper_sizes(null, null, 0, 20);
			}	
		}
		
		if (is_array($result_array) and count($result_array) >= 1)
		{
			foreach($result_array as $key => $value)
			{
				$result_array[$key][symbol] = "<img src='images/icons/text.png' alt='' />";
				
				if ($value['standard'] == 't')
				{
					$result_array[$key][name] = $result_array[$key][name]." <img src='images/icons/status_ok.png' alt='Standard' />";
				}
				
				$result_array[$key][width] = $result_array[$key][width]." mm";
				$result_array[$key][height] = $result_array[$key][height]." mm";
				$result_array[$key][margin_left] = $result_array[$key][margin_left]." mm";
				$result_array[$key][margin_right] = $result_array[$key][margin_right]." mm";
				$result_array[$key][margin_top] = $result_array[$key][margin_top]." mm";
				$result_array[$key][margin_bottom] = $result_array[$key][margin_bottom]." mm";
				
				$result_array[$key]['edit'] = "<a href='#' class='BaseAdminPaperSizeEdit' id='BaseAdminPaperSizeEdit".$result_array[$key][id]."'><img src='images/icons/edit.png' alt='' style='border: 0;' /></a>";
				
				if ($value['base'] == 'f')
				{
					$result_array[$key]['delete'] = "<a href='#' class='BaseAdminPaperSizeDelete' id='BaseAdminPaperSizeDelete".$result_array[$key][id]."'><img src='images/icons/delete.png' alt='' style='border: 0;' /></a>";
				}
			}
		}
		else
		{
			$list->override_last_line("<span class='italic'>No results found!</span>");
		}
			
		echo $list->get_list($result_array, $page);
	}
	
	public function list_measuring_units($page, $sortvalue, $sortmethod)
	{
		
	}
	
	public function list_currencies($page, $sortvalue, $sortmethod)
	{
		
	}
	
	public function add_paper_size($name, $width, $height, $margin_left, $margin_right, $margin_top, $margin_bottom)
	{
		$paper_size = new PaperSize(null);
		if ($paper_size->create($name, $width, $height, $margin_left, $margin_right, $margin_top, $margin_bottom) == true)
		{
			echo "1";
		}
		else
		{
			echo "0";
		}
	}
	
	public function get_paper_size($id)
	{
		if (is_numeric($id))
		{
			$paper_size = new PaperSize($id);
			
			$return_array = array();
			
			$return_array[0] = $paper_size->get_base();
			$return_array[1] = $paper_size->get_name();
			$return_array[2] = $paper_size->get_width();
			$return_array[3] = $paper_size->get_height();
			$return_array[4] = $paper_size->get_margin_left();
			$return_array[5] = $paper_size->get_margin_right();
			$return_array[6] = $paper_size->get_margin_top();
			$return_array[7] = $paper_size->get_margin_bottom();
			
			echo json_encode($return_array);
		}
		else
		{
			echo null;
		}
	}
	
	public function set_paper_size($id, $name, $width, $height, $margin_left, $margin_right, $margin_top, $margin_bottom)
	{
		if (is_numeric($id) and $name and is_numeric($width) and is_numeric($height) and is_numeric($margin_left) and is_numeric($margin_right) and is_numeric($margin_top) and is_numeric($margin_bottom))
		{
			$paper_size = new PaperSize($id);
			
			if ($paper_size->set_name($name) == false)
			{
				echo "0";
			}
			
			if ($paper_size->set_width($width) == false)
			{
				echo "0";
			}
			
			if ($paper_size->set_height($height) == false)
			{
				echo "0";
			}
			
			if ($paper_size->set_margin_left($margin_left) == false)
			{
				echo "0";
			}
			
			if ($paper_size->set_margin_right($margin_right) == false)
			{
				echo "0";
			}
			
			if ($paper_size->set_margin_top($margin_top) == false)
			{
				echo "0";
			}
			
			if ($paper_size->set_margin_bottom($margin_bottom) == false)
			{
				echo "0";
			}
			
			echo "1";
		}
		else
		{
			echo "0";
		}
	}
	
	public function delete_paper_size($id)
	{
		if (is_numeric($id))
		{
			$paper_size = new PaperSize($id);
			if ($paper_size->delete() == true)
			{
				echo "1";
			}
			else
			{
				echo "0";
			}
		}
		else
		{
			echo "0";
		}
	}
	
	public function add_currency()
	{
		
	}
	
	public function delete_currency($id)
	{
		
	}
	
	public function method_handler()
	{
		global $session;
		
		if ($session->is_valid())
		{
			switch($_GET[run]):
	
				case "list_languages":
					echo $this->list_languages($_POST[row_array], $_POST[argument_array], $_POST[get_array], $_POST[css_page_id],  $_POST[css_row_sort_id], $_POST[entries_per_page], $_GET[page], $_GET[sortvalue], $_GET[sortmethod]);
				break;
				
				case "count_languages":
					echo $this->count_languages($_POST[argument_array]);
				break;
				
				
				case "list_timezones":
					$this->list_timezones($_GET[page], $_GET[sortvalue], $_GET[sortmethod]);
				break;
				
				case "list_paper_sizes":
					$this->list_paper_sizes($_GET[page], $_GET[sortvalue], $_GET[sortmethod]);
				break;
				
				case "add_paper_size":
					$this->add_paper_size($_POST[name], $_POST[width], $_POST[height], $_POST[margin_left], $_POST[margin_right], $_POST[margin_top], $_POST[margin_bottom]);
				break;
				
				case "get_paper_size":
					$this->get_paper_size($_GET[id]);
				break;
				
				case "set_paper_size":
					$this->set_paper_size($_POST[id], $_POST[name], $_POST[width], $_POST[height], $_POST[margin_left], $_POST[margin_right], $_POST[margin_top], $_POST[margin_bottom]);
				break;
				
				case "delete_paper_size":
					$this->delete_paper_size($_GET[id]);
				break;
				
				case "list_measuring_units":
					$this->list_measuring_units($_GET[page], $_GET[sortvalue], $_GET[sortmethod]);
				break;
				
				case "list_currencies":
					$this->list_currencies($_GET[page], $_GET[sortvalue], $_GET[sortmethod]);
				break;
							
				default:
				break;
			
			endswitch;
		}
	}	
}

$admin_general_ajax = new AdminGeneralAjax;
$admin_general_ajax->method_handler();

?>