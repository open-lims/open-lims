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
 * Adming General IO Class
 * @package base
 */
class AdminGeneralIO
{	
	public static function list_languages()
	{
		$list = new List_IO("AdminGeneralLanguage", "/core/modules/base/ajax/admin/admin_general.ajax.php", "list_languages", "count_languages", $argument_array, "AdminGeneralLanguage");
		
		$list->add_column("","symbol",false,"16px");
		$list->add_column("Name","language_name",true,null);
		$list->add_column("English Name","english_name",true,null);
		$list->add_column("ISO 639","iso_639",true,null);
		$list->add_column("ISO 3166","iso_3166",true,null);
		
		$template = new HTMLTemplate("base/admin/general/list_languages.html");
		
		$template->set_var("list", $list->get_list());
		
		$template->output();
	}
	
	public static function list_timezones()
	{
		$list = new List_IO("AdminGeneralTimezone", "/core/modules/base/ajax/admin/admin_general.ajax.php", "list_timezones", "count_timezones", $argument_array, "AdminGeneralTimezone");
		
		$list->add_column("","symbol",false,"16px");
		$list->add_column("Name","name",true,null);
		$list->add_column("Deviation","deviation",true,null);
		
		$template = new HTMLTemplate("base/admin/general/list_timezones.html");
		
		$template->set_var("list", $list->get_list());
		
		$template->output();
	}
	
	public static function list_paper_sizes()
	{
		$template = new HTMLTemplate("base/admin/general/list_paper_sizes.html");
		$template->output();
	}
	
	public static function list_measuring_units()
	{
		$list = new List_IO("AdminGeneralMeasuringUnit", "/core/modules/base/ajax/admin/admin_general.ajax.php", "list_measuring_units", "count_measuring_units", $argument_array, "AdminGeneralMeasuringUnit");
		
		$list->add_column("","symbol",false,"16px");
		$list->add_column("Name","name",true,null);
		$list->add_column("Type","type",true,null);
		$list->add_column("Symbol","unit_symbol",false,null);
		
		$template = new HTMLTemplate("base/admin/general/list_measuring_units.html");
		
		$template->set_var("list", $list->get_list());
		
		$template->output();
	}
	
	public static function list_currencies()
	{
		$list = new List_IO("AdminGeneralCurrency", "/core/modules/base/ajax/admin/admin_general.ajax.php", "list_currencies", "count_currencies", $argument_array, "AdminGeneralCurrency");
		
		$list->add_column("","symbol",false,"16px");
		$list->add_column("Name","name",true,null);
		$list->add_column("Symbol","currency_symbol",false,null);
		$list->add_column("ISO 4217","iso_4217",true,null);
		
		$template = new HTMLTemplate("base/admin/general/list_currencies.html");
		
		$template->set_var("list", $list->get_list());
		
		$template->output();
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