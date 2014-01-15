<?php
/**
 * @package data
 * @version 0.4.0.0
 * @author Roman Konertz <konertz@open-lims.org>
 * @copyright (c) 2008-2014 by Roman Konertz
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
require_once("interfaces/parameter_non_template_parameter.interface.php");

if (constant("UNIT_TEST") == false or !defined("UNIT_TEST"))
{
	require_once("access/parameter_has_non_template.access.php");
}

/**
 * Parameter Non Template Parameter Management Class
 * @package data
 */
class ParameterNonTemplateParameter extends Parameter implements ParameterNonTemplateParameterInterface
{
	function __construct($parameter_id)
	{
		parent::__construct($parameter_id);
	}
	
	public static function is_non_template_parameter($parameter_id)
    {
    	if (ParameterHasNonTemplate_Access::get_non_template_id_by_parameter_id($parameter_id) !== null)
    	{
    		return true;
    	}
    	else
    	{
    		return false;
    	}
    }
}
?>