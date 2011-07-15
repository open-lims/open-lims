<?php
/**
 * @package base
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
 * Adming General IO Class
 * @package base
 */
class AdminGeneralIO
{	
	public static function list_languages()
	{
		$template = new Template("template/base/admin/general/list_languages.html");
		$template->output();		
	}
	
	public static function list_timzones()
	{
		
	}
	
	public static function list_paper_sizes()
	{
		$template = new Template("template/base/admin/general/list_paper_sizes.html");
		$template->output();
	}
	
	public static function list_measuring_units()
	{
		
	}
	
	public static function list_currencies()
	{
		
	}
	
	public static function handler()
	{
		$tab_io = new Tab_IO();
	
		
		$paramquery = $_GET;
		$paramquery[action] = "list_languages";
		$params = http_build_query($paramquery,'','&#38;');
		
		$tab_io->add("languages", "Languages", $params, false);
		
		
		$paramquery = $_GET;
		$paramquery[action] = "list_timezones";
		$params = http_build_query($paramquery,'','&#38;');
		
		$tab_io->add("timezones", "Timezones", $params, false);
		
		
		$paramquery = $_GET;
		$paramquery[action] = "list_paper_sizes";
		$params = http_build_query($paramquery,'','&#38;');
		
		$tab_io->add("paper-sizes", "Paper Sizes", $params, false);

		
		$paramquery = $_GET;
		$paramquery[action] = "list_measuring_units";
		$params = http_build_query($paramquery,'','&#38;');
		
		$tab_io->add("measuring-units", "Measur. Un.", $params, false);  
		
				
		$paramquery = $_GET;
		$paramquery[action] = "list_currencies";
		$params = http_build_query($paramquery,'','&#38;');
		
		$tab_io->add("currencies", "Currencies", $params, false);
		
		
		switch($_GET[action]):
			
			case "list_timezones":
				$tab_io->activate("timezones");
			break;
			
			case "list_paper_sizes":
				$tab_io->activate("paper-sizes");
			break;
			
			case "list_measuring_units":
				$tab_io->activate("measuring-units");
			break;
			
			case "list_currencies":
				$tab_io->activate("currencies");
			break;
		
			default:
				$tab_io->activate("languages");
			break;
		
		endswitch;
			
		$tab_io->output();
		
		switch($_GET[action]):
			case "list_timezones":
				self::list_timezones();
			break;
			
			case "list_paper_sizes":
				self::list_paper_sizes();
			break;
			
			case "list_measuring_units":
				self::list_measuring_units();
			break;
			
			case "list_currencies":
				self::list_currencies();
			break;
		
			default:
				self::list_languages();
			break;
		endswitch;
	}
}

?>