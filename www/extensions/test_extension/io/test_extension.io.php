<?php
/**
 * @package extension
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
 * IO Class of the test extension
 * @package extension
 */
class TestExtensionIO
{
	public static $template_path;
	
	public static function set_template_path($template_path)
	{
		self::$template_path = $template_path;
	}
	
	public static function start($data)
	{
		$template = new HTMLTemplate("start.html", self::$template_path);
		
		$content = "";
		
		foreach($data as $key => $value)
		{
			$content .= " ".$value;
		}
		
		$template->set_var("content", $content);
		
		$template->set_var("retrace_params", http_build_query(Retrace::resolve_retrace_string($_GET['retrace']),'','&'));

		
		$template->output();
	}
	
	public static function home()
	{		
		$template = new HTMLTemplate("home.html", self::$template_path);
		
		require_once("extensions/test_extension/classes/test.class.php");
		$test = new Test(null);
		$content = $test->get_content();
		
		if ($content)
		{
			$content = str_replace("\n","<br />", $content);
			$template->set_var("content", $content);
		}
		else
		{
			$template->set_var("content", "empty");
		}

		$template->output();
	}
}
?>