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
//require_once("interfaces/language.interface.php");

if (constant("UNIT_TEST") == false or !defined("UNIT_TEST"))
{
	require_once("access/language.access.php");
}

/**
 * Language Class
 * @package base
 */
class Language // implements LanguageInterface
{
	private $language_id;
	private $language;
	
	private static $specific_lang_exception_array;
	private static $fallback_lang_exception_array;
	
	/**
	 * @param integer $language_id
	 */
	function __construct($language_id)
	{
		if (is_numeric($language_id))
		{
			$this->language_id = $language_id;
			$this->language = new Language_Access($language_id);
		}
		else
		{
			$this->language_id = null;
			$this->language = new Language_Access(null);
		}
	}	
	
	function __destruct()
	{
		unset($this->language_id);
		unset($this->language);
	}
	
	public function get_full_name()
    {
    	if ($this->language and $this->language_id)
		{
    		return $this->language->get_english_name()."/".$this->language->get_language_name()." (".$this->language->get_iso_639()."-".$this->language->get_iso_3166().")";
    	}
    	else
    	{
    		return null;
    	}
    }
	
	/**
	 * @return string
	 */
	public function get_folder_name()
	{
		if ($this->language and $this->language_id)
		{
			$iso_639 = $this->language->get_iso_639();
			$iso_3166 = $this->language->get_iso_3166();
			
			return strtolower(trim($iso_639))."-".strtolower(trim($iso_3166));
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @return string
	 */
	public function get_tsvector_name()
	{
		if ($this->language and $this->language_id)
		{
			return $this->language->get_tsvector_name();
		}
		else
		{
			return null;
		}
	}
	
	
	/**
	 * @return array
	 */
	public static function list_languages()
    {
    	return Language_Access::list_entries();
    }
	
	/**
	 * @param integer $language_id
	 * @return array
	 */
	private static function scan_language($language_id, $path)
	{
		$LANG = array();
		
		$module_array = SystemHandler::list_modules();
		$language = new Language($language_id);
		
		if (is_array($module_array)and count($module_array) >= 1)
		{
			$lanugage_folder = constant("WWW_DIR")."/languages/".$language->get_folder_name();
			
			foreach ($module_array as $key => $value)
			{
				if (file_exists($lanugage_folder."/".trim($value['folder'])."/".$path))
				{
					include($lanugage_folder."/".trim($value['folder'])."/".$path);
				}
			}
		}
		
		return $LANG;
	}
	
	/**
	 * @param string $class_name
	 * @return string
	 */
	public static function get_message($address, $type, $path = null)
	{
		global $session;
		
		switch($type):
		
			case "exception":
				$path = "generic/exceptions.lang.php";
			break;
			
			case "navigation":
				$path = "generic/navigation.lang.php";
			break;
			
			case "dialog":
				$path = "generic/dialog.lang.php";
			break;
			
		endswitch;
		
		if ($address)
		{
			$address = trim($address);
			
			self::$fallback_lang_exception_array = self::scan_language(1, $path);
			
			if (is_numeric($language_id = $session->read_value("LANGUAGE")))
			{
				self::$specific_lang_exception_array = self::scan_language($language_id, $path);
			}
			else
			{
				self::$specific_lang_exception_array = self::scan_language(1, $path);
			}
			
			if (self::$specific_lang_exception_array[''.$address.''])
			{
				return self::$specific_lang_exception_array[''.$address.''];
			}
			elseif (self::$fallback_lang_exception_array[''.$address.''])
			{
				return self::$fallback_lang_exception_array[''.$address.''];
			}
			else
			{
				return "$address".null;
			}
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * Functions returns a valid path of a language templaet file
	 * Returns english (en-gb), if no file was found.
	 * @param string $path
	 * @return string
	 */
	public static function get_current_global_language_path($language_id = null)
	{
		global $session;
			
		if (is_numeric($language_id) and Language_Access::exist_id($language_id))
		{
			$language = new Language($language_id);
		}
		else
		{
			if (is_numeric($session_language_id = $session->read_value("LANGUAGE")))
			{
				$language = new Language($session_language_id);
			}
			else
			{
				$language = new Language(1);
			}
		}
						
		$language_file = constant("WWW_DIR")."/languages/".$language->get_folder_name()."/base/generic/global.lang.php";
		
		if (file_exists($language_file))
		{
			return $language_file;
		}
		else
		{
			$language = new Language(1);				
			$language_file = constant("WWW_DIR")."/languages/".$language->get_folder_name()."/base/generic/global.lang.php";
		
			if (file_exists($language_file))
			{
				return $language_file;
			}
			else
			{
				return null;
			}
		}
	}
	
	/**
	 * Functions returns a valid path of a language templaet file
	 * Returns english (en-gb), if no file was found.
	 * @param string $path
	 * @return string
	 */
	public static function get_current_language_path($path, $language_id = null)
	{
		global $session;
		
		if ($path)
		{
			$path = str_replace(constant("WWW_DIR"),"",$path);
			$path = str_replace("\\","/",$path);
			
			$path_token = explode("/", $path);
			$path_token_counter = count($path_token);
			
			$language_path_token = array();
			
			if (($path_token_counter-1) > 0)
			{
				for ($i = ($path_token_counter-1); $i>=0; $i--)
				{
					if ($i > 0 and $path_token[($i-1)] == "template")
					{
						break;
					}
					else
					{
						if ($i == ($path_token_counter-1))
						{
							$path_token[$i] = str_replace(".html",".lang.php",$path_token[$i]);
						}
						array_unshift($language_path_token, $path_token[$i]);
					}
				}
				
				if (is_numeric($language_id) and Language_Access::exist_id($language_id))
				{
					$language = new Language($language_id);
				}
				else
				{
					if (is_numeric($session_language_id = $session->read_value("LANGUAGE")))
					{
						$language = new Language($session_language_id);
					}
					else
					{
						$language = new Language(1);
					}
				}
								
				$language_file = constant("WWW_DIR")."/languages/".$language->get_folder_name()."/".implode("/", $language_path_token);
				
				if (file_exists($language_file))
				{
					return $language_file;
				}
				else
				{
					$language = new Language(1);				
					$language_file = constant("WWW_DIR")."/languages/".$language->get_folder_name()."/".implode("/", $language_path_token);
				
					if (file_exists($language_file))
					{
						return $language_file;
					}
					else
					{
						return null;
					}
				}
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