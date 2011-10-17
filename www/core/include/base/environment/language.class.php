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
require_once("interfaces/language.interface.php");

if (constant("UNIT_TEST") == false or !defined("UNIT_TEST"))
{
	require_once("access/language.access.php");
}

/**
 * Runtime Data Class
 * Saves calculated data via runtime and avoids recalculation
 * @package base
 */
class Language implements LanguageInterface
{
	private $language_id;
	private $language;
	
	/**
	 * @param integer $language_id
	 */
	function __construct($lauguage_id)
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
	 * Functions returns a valid path of a language templaet file
	 * Returns english (en-gb), if no file was found.
	 * @param string $path
	 * @return string
	 * @todo LATER: implementation (multi-language-support)
	 */
	public static function get_current_user_path($path)
	{
		
	}
}