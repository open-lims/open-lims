<?php
/**
 * @package extension
 * @version 0.4.0.0
 * @author Roman Konertz <konertz@open-lims.org>
 * @copyright (c) 2008-2013 by Roman Konertz
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
 * Extension IO Class
 * @package extension
 */
class ExtensionIO
{	
	public static function home()
	{
		$extension_handler = new ExtensionHandler();
		
		$template = new HTMLTemplate("base/extension/home.html");	
		
		$extension_array = ExtensionHandler::list_extensions();
		$display_array = array();
		$counter = 0;
		
		if (is_array($extension_array) and count($extension_array) >= 1)
		{
			foreach ($extension_array as $key => $value)
			{
				$display_array[$counter]['name'] = $value['name'];
				
				$paramquery = $_GET;
				$paramquery['username'] = $_GET['username'];
				$paramquery['session_id'] = $_GET['session_id'];
				$paramquery['nav'] = $_GET['nav'];
				$paramquery['extension'] = $value['id'];;
				$params = http_build_query($paramquery, '', '&#38;');
				
				$display_array[$counter]['link'] = $params;
				
				$counter++;
			}
		}
		
		$template->set_var("extension_array", $display_array);
		
		$template->output();
	}
}
?>