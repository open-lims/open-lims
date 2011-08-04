<?php
/**
 * @package data
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
require_once("interfaces/value_external_var.interface.php"); 

/**
 * Manages external requests of OLVDL var requests
 * @package data
 * @todo create an interface for external information
 */
class ValueExternalVar implements ValueExternalVarInterface
{
    /**
     * @see ValueExternalVarInterface::get_var_content()
     * @param string $address
     * @return mixed
     */
    public function get_var_content($address)
    {    	
    	$number_of_statements = substr_count($address, ".");
    	
    	if ($number_of_statements >= 0)
    	{
    		if ($number_of_statements == 0)
    		{
    			return null;
    		}
    		else
    		{
	    		$statement_array = explode(".", $address);
	    		$current_statement = $statement_array[0];
    		}
	    	
	    	if ($number_of_statements == 0)
	    	{
    			$external_statement = null;
    		}
    		else
    		{
    			if ($number_of_statements == 1)
    			{
    				$external_statement = $statement_array[1];
    			}
    			else
    			{
		    		$statement_array = explode(".", $address);
		    		$statement_string = "";
		    		for ($i=1;$i<=$number_of_statements;$i++)
		    		{
		    			if (!$statement_string)
		    			{
		    				$statement_string = $statement_array[$i];
		    			}
		    			else
		    			{
		    				$statement_string .= ".".$statement_array[$i];
		    			}
		    		}
    			}
	    		$external_statement = $statement_string;
    		}
	    	
	    	switch($current_statement):
		    	case "biological":
		    		require_once("external/biological/biological_main.php");
		    		require_once("external/biological/var_value.class.php");
		    		$bio_var_value = new BioVarValue();
		    		return $bio_var_value->get_var_content($external_statement);
		    	break;
		    	
		    	default:
		    		return null;
		    	break;
	    	endswitch;
    	}
    	else
    	{
    		return null;
    	}
    }
    
}
?>