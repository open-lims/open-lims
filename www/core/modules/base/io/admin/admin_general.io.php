<?php
/**
 * @package base
 * @version 0.4.0.0
 * @author Roman Konertz <konertz@open-lims.org>
 * @copyright (c) 2008-2016 by Roman Konertz
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
		$list = new List_IO("AdminGeneralLanguage", "ajax.php?nav=base.admin", "admin_general_list_languages", "admin_general_count_languages", $argument_array, "AdminGeneralLanguage");
		
		$list->add_column("","symbol",false,"16px");
		$list->add_column(Language::get_message("BaseGeneralListColumnName", "general"),"language_name",true,null);
		$list->add_column(Language::get_message("BaseGeneralListColumnEnglishName", "general"),"english_name",true,null);
		$list->add_column("ISO 639","iso_639",true,null);
		$list->add_column("ISO 3166","iso_3166",true,null);
		
		$template = new HTMLTemplate("base/admin/general/list_languages.html");
		
		$template->set_var("list", $list->get_list());
		
		$template->output();
	}
	
	public static function list_timezones()
	{
		$list = new List_IO("AdminGeneralTimezone", "ajax.php?nav=base.admin", "admin_general_list_timezones", "admin_general_count_timezones", $argument_array, "AdminGeneralTimezone");
		
		$list->add_column("","symbol",false,"16px");
		$list->add_column(Language::get_message("BaseGeneralListColumnName", "general"),"name",true,null);
		$list->add_column(Language::get_message("BaseGeneralListColumnDeviation", "general"),"deviation",true,null);
		
		$template = new HTMLTemplate("base/admin/general/list_timezones.html");
		
		$template->set_var("list", $list->get_list());
		
		$template->output();
	}
	
	public static function list_paper_sizes()
	{
		$list = new List_IO("AdminGeneralPaperSize", "ajax.php?nav=base.admin", "admin_general_list_paper_sizes", "admin_general_count_paper_sizes", $argument_array, "AdminGeneralPaperSize");
		
		$list->add_column("","symbol",false,"16px");
		$list->add_column(Language::get_message("BaseGeneralListColumnName", "general"),"name",true,null,"BaseGeneralAdminListSortName");
		$list->add_column(Language::get_message("BaseGeneralListColumnWidth", "general"),"width",false,null);
		$list->add_column(Language::get_message("BaseGeneralListColumnHeight", "general"),"height",false,null);
		$list->add_column(Language::get_message("BaseGeneralListColumnLeftM", "general"),"margin_left",false,null);
		$list->add_column(Language::get_message("BaseGeneralListColumnRightM", "general"),"margin_right",false,null);
		$list->add_column(Language::get_message("BaseGeneralListColumnTopM", "general"),"margin_top",false,null);
		$list->add_column(Language::get_message("BaseGeneralListColumnBottomM", "general"),"margin_bottom",false,null);
		$list->add_column("","edit",false,"20px");
		$list->add_column("","delete",false,"20px");
		
		$template = new HTMLTemplate("base/admin/general/list_paper_sizes.html");
		
		$template->set_var("list", $list->get_list());
		
		$template->output();
	}
	
	public static function list_measuring_units()
	{
		$list = new List_IO("AdminGeneralMeasuringUnit", "ajax.php?nav=base.admin", "admin_general_list_measuring_units", "admin_general_count_measuring_units", null, "AdminGeneralMeasuringUnit");
		
		$list->add_column("","symbol",false,"20px");
		$list->add_column(Language::get_message("BaseGeneralListColumnName", "general"),"name",true,null);
		$list->add_column(Language::get_message("BaseGeneralListColumnSymbol", "general"),"unit_symbol",false,null);
		$list->add_column("Secondary Units","secondary_units",false,null);
		$list->add_column("Min-Value","min_value",true,null);
		$list->add_column("Max-Value","max_value",true,null);
		$list->add_column("Category","category",true,null);
		$list->add_column(Language::get_message("BaseGeneralListColumnType", "general"),"type",true,null);
		$list->add_column("", "edit", false, "20px");
		$list->add_column("", "delete", false, "20px");
		
		
		
		$template = new HTMLTemplate("base/admin/general/list_measuring_units.html");
		
		$template->set_var("list", $list->get_list());
		
		$template->set_var("measuring_unit_categories", MeasuringUnitCategory::list_categories());
		
		$template->output();
	}
	
	public static function list_measuring_unit_ratios()
	{
		$list = new List_IO("AdminGeneralMeasuringUnitRatio", "ajax.php?nav=base.admin", "admin_general_list_measuring_unit_ratios", "admin_general_count_measuring_unit_ratios", null, "AdminGeneralMeasuringUnitRatio");
		
		$list->add_column("","symbol",false,"16px");
		$list->add_column(Language::get_message("BaseGeneralListColumnName", "general"),"name",false);
		$list->add_column("Numerator","numerator");
		$list->add_column("Numerator-Exp.","numerator_exp");
		$list->add_column("Denominator","denominator");
		$list->add_column("Denominator-Exp.","denominator_exp",true,null);
		$list->add_column("","delete",false,"16px");
		
		$template = new HTMLTemplate("base/admin/general/list_measuring_unit_ratios.html");
		
		$template->set_var("list", $list->get_list());
		
		$result = array();
		$counter = 0;
		
		$measuring_unit_array = MeasuringUnit::get_categorized_list();
				
		if (is_array($measuring_unit_array) and count($measuring_unit_array) >= 1)
		{
			foreach($measuring_unit_array as $key => $value)
			{
				if ($value['headline'] == true)
				{
					$result[$counter]['disabled'] = "disabled='disabled'";
				}
				else
				{
					$result[$counter]['disabled'] = "";
				}
				
				$result[$counter]['value'] = $value['id']."-".$value['exponent'];
				
				$result[$counter]['selected'] = "";
				$result[$counter]['content'] = $value['name'];
				$counter++;
			}
		}
		
		$template->set_var("measuring_units",$result);
		
		$template->output();
	}
	
	public static function list_measuring_unit_categories()
	{
		$list = new List_IO("AdminGeneralMeasuringUnit", "ajax.php?nav=base.admin", "admin_general_list_measuring_unit_categories", "admin_general_count_measuring_unit_categories", null, "AdminGeneralMeasuringUnit");
		
		$list->add_column("","symbol",false,"16px");
		$list->add_column(Language::get_message("BaseGeneralListColumnName", "general"),"name",true);
		$list->add_column("", "delete", false, "20px");
		
		$template = new HTMLTemplate("base/admin/general/list_measuring_unit_categories.html");
		
		$template->set_var("list", $list->get_list());
		
		$template->output();
	}
	
	public static function list_currencies()
	{
		$list = new List_IO("AdminGeneralCurrency", "ajax.php?nav=base.admin", "admin_general_list_currencies", "admin_general_count_currencies", $argument_array, "AdminGeneralCurrency");
		
		$list->add_column("","symbol",false,"16px");
		$list->add_column(Language::get_message("BaseGeneralListColumnName", "general"),"name",true,null);
		$list->add_column(Language::get_message("BaseGeneralListColumnSymbol", "general"),"currency_symbol",false,null);
		$list->add_column("ISO 4217","iso_4217",true,null);
		
		$template = new HTMLTemplate("base/admin/general/list_currencies.html");
		
		$template->set_var("list", $list->get_list());
		
		$template->output();
	}
	
	public static function handler()
	{
		$tab_io = new Tab_IO();
	
		
		$paramquery = $_GET;
		$paramquery['action'] = "list_languages";
		$params = http_build_query($paramquery,'','&#38;');
		
		$tab_io->add("languages", Language::get_message("BaseGeneralAdminGeneralTabLanguages", "general"), $params, false);
		
		
		$paramquery = $_GET;
		$paramquery['action'] = "list_timezones";
		$params = http_build_query($paramquery,'','&#38;');
		
		$tab_io->add("timezones", Language::get_message("BaseGeneralAdminGeneralTabTimezones", "general"), $params, false);
		
		
		$paramquery = $_GET;
		$paramquery['action'] = "list_paper_sizes";
		$params = http_build_query($paramquery,'','&#38;');
		
		$tab_io->add("paper-sizes", Language::get_message("BaseGeneralAdminGeneralTabPaperSizes", "general"), $params, false);

		
		$paramquery = $_GET;
		$paramquery['action'] = "list_measuring_units";
		$params = http_build_query($paramquery,'','&#38;');
		
		$tab_io->add("measuring-units", Language::get_message("BaseGeneralAdminGeneralTabMeasuringUnits", "general"), $params, false);  
		
		
		$paramquery = $_GET;
		$paramquery['action'] = "list_measuring_unit_ratios";
		$params = http_build_query($paramquery,'','&#38;');
		
		$tab_io->add("measuring-unit-ratios", Language::get_message("BaseGeneralAdminGeneralTabMeasuringUnitRatios", "general"), $params, false);  
		
		
		$paramquery = $_GET;
		$paramquery['action'] = "list_measuring_unit_categories";
		$params = http_build_query($paramquery,'','&#38;');
		
		$tab_io->add("measuring-unit-categories", Language::get_message("BaseGeneralAdminGeneralTabMeasuringUnitCategories", "general"), $params, false);  

		
		$paramquery = $_GET;
		$paramquery['action'] = "list_currencies";
		$params = http_build_query($paramquery,'','&#38;');
		
		$tab_io->add("currencies", Language::get_message("BaseGeneralAdminGeneralTabCurrencies", "general"), $params, false);
		
		
		switch($_GET['action']):
			
			case "list_timezones":
				$tab_io->activate("timezones");
			break;
			
			case "list_paper_sizes":
				$tab_io->activate("paper-sizes");
			break;
			
			case "list_measuring_units":
				$tab_io->activate("measuring-units");
			break;
			
			case "list_measuring_unit_ratios":
				$tab_io->activate("measuring-unit-ratios");
			break;
			
			case "list_measuring_unit_categories":
				$tab_io->activate("measuring-unit-categories");
			break;
			
			case "list_currencies":
				$tab_io->activate("currencies");
			break;
		
			default:
				$tab_io->activate("languages");
			break;
		
		endswitch;
			
		$tab_io->output();
		
		switch($_GET['action']):
			case "list_timezones":
				self::list_timezones();
			break;
			
			case "list_paper_sizes":
				self::list_paper_sizes();
			break;
			
			case "list_measuring_units":
				self::list_measuring_units();
			break;
			
			case "list_measuring_unit_ratios":
				self::list_measuring_unit_ratios();
			break;
			
			case "list_measuring_unit_categories":
				self::list_measuring_unit_categories();
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