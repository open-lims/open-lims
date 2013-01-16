<?php
/**
 * @package base
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
 * Login IO Class
 * @package base
 */
class Login_IO
{		
	public static function output($session_expired = false)
	{
		$auth = new Auth();
		
		$template = new HTMLTemplate("login_header.html");
		$template->output();
		
		if (is_numeric($_POST['language_id']))
		{
			$template = new HTMLTemplate("base/login/login.html", null, $_POST['language_id']);
		}
		else
		{
			$template = new HTMLTemplate("base/login/login.html");
		}
		
		if ($_POST[username])
		{
			$template->set_var("username",$_POST[username]);
		}
		else
		{
			$template->set_var("username","");
		}
		
		if ($_POST[password])
		{
			$template->set_var("password",$_POST[password]);
		}
		else
		{
			$template->set_var("password","");
		}
		
		if ($session_expired === true)
		{
			$template->set_var("session_expired","true");
		}
		else
		{
			$template->set_var("session_expired","false");
		}
		
		
		$language_array = Language::list_languages();
		
		$result = array();
		$counter = 0;
		
		if (is_array($language_array))
		{
			foreach($language_array as $key => $value)
			{
				$language = new Language($value);
				
				$result[$counter]['option'] = $value;
				$result[$counter]['name'] = $language->get_full_name();
				
				if ($_POST['language_id'] == $value)
				{
					$result[$counter]['selected'] = "selected='selected'";
				}
				else
				{
					$result[$counter]['selected'] = "";
				}
				
				$counter++;		
			}
		}
		
		$template->set_var("languages",$result);

		$template->set_var("product",constant("PRODUCT"));
		$template->set_var("product_version",constant("PRODUCT_VERSION"));
		$template->set_var("function",Registry::get_value("base_product_function"));
		$template->set_var("user",Registry::get_value("base_product_user"));
		
		$template->output();
	}
}
?>
