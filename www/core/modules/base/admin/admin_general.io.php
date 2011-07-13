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
	public static function handler()
	{
		$tab_io = new Tab_IO();
	
		
		$paramquery = $_GET;
		$paramquery[action] = "detail";
		$params = http_build_query($paramquery,'','&#38;');
		
		$tab_io->add("languages", "Languages", $params, false);
		
		
		$paramquery = $_GET;
		$paramquery[action] = "detail_owner";
		$params = http_build_query($paramquery,'','&#38;');
		
		$tab_io->add("timezones", "Timezones", $params, false);
		
		
		$paramquery = $_GET;
		$paramquery[action] = "detail_leader";
		$params = http_build_query($paramquery,'','&#38;');
		
		$tab_io->add("paper-sizes", "Paper Sizes", $params, false);

		
		$paramquery = $_GET;
		$paramquery[action] = "detail_member";
		$params = http_build_query($paramquery,'','&#38;');
		
		$tab_io->add("measuring-units", "Measur. Un.", $params, false);  
		
				
		$paramquery = $_GET;
		$paramquery[action] = "detail_leader";
		$params = http_build_query($paramquery,'','&#38;');
		
		$tab_io->add("currencies", "Currencies", $params, false);
		
		
		switch($_GET[action]):
			
			default:
				$tab_io->activate("languages");
			break;
		
		endswitch;
			
		$tab_io->output();
		
		switch($_GET[action]):
			default:
			
			break;
		endswitch;
	}
}

?>