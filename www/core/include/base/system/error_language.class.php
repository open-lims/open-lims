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
 * 
 */
require_once("interfaces/error_language.interface.php");

/**
 * Error Language Class
 * @package base
 */
class ErrorLanguage implements ErrorLanguageInterface
{
	private static $specific_lang_exception_array;
	private static $fallback_lang_exception_array;
	
	/**
	 * @param integer $language_id
	 * @return array
	 */
	private static function scan_language($lanuage_id)
	{
		$module_array = SystemHandler::list_modules();
		$language = new Language($lanuage_id);
		
		if (is_array($module_array)and count($module_array) >= 1)
		{
			$lanugage_folder = $sub_folder = constant("WWW_DIR")."/languages/".$language->get_folder_name();
			
			foreach ($module_array as $key => $value)
			{
				if (file_exists($lanugage_folder."/".trim($value['folder'])."/exceptions.lang.php"))
				{
					include_once($lanugage_folder."/".trim($value['folder'])."/exceptions.lang.php");
				}
			}
		}
		
		return $LANG_EXCEPTION;
	}
	
	/**
	 * @see ErrorLanguageInterface::get_message()
	 * @param string $class_name
	 * @return string
	 */
	public static function get_message($class_name)
	{
		global $session;
		
		if ($class_name)
		{
			self::$fallback_lang_exception_array = self::scan_language(1);
			
			if (is_numeric($language_id = $session->read_value("LANGUAGE")))
			{
				self::$specific_lang_exception_array = self::scan_language($language_id);
			}
			else
			{
				self::$specific_lang_exception_array = self::scan_language(1);
			}
			
			if (self::$specific_lang_exception_array[''.$class_name.''])
			{
				return self::$specific_lang_exception_array[''.$class_name.''];
			}
			elseif (self::$fallback_lang_exception_array[''.$class_name.''])
			{
				return self::$fallback_lang_exception_array[''.$class_name.''];
			}
			else
			{
				return null;
			}
		}
		else
		{
			return null;
		}
	}
}
?>