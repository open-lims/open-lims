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
 * List IO Class
 * @package base
 */
class List_IO
{
	private $ajax_handler;
	private $ajax_run;
	private $argument_array;
	private $css_main_id;
	
	private $rows = array();
	
    function __construct($ajax_handler, $ajax_run, $argument_array, $css_main_id)
    {
    	if ($ajax_handler and $ajax_run)
    	{
    		$this->ajax_handler = $ajax_handler;
    		$this->ajax_run = $ajax_run;
    		
    		$this->argument_array = $argument_array;
    		$this->css_main_id = $css_main_id;
    	}
    }
    
 	public function add_row($title, $address, $sortable, $width, $row_css_id = null)
    {
    	if ($address)
    	{
    		$row_array = array();
    		$row_array[0] = $title;
    		$row_array[1] = $address;
    		
    		if ($width != null)
    		{
    			$row_array[2] = $width;
    		}
    		else
    		{
    			$row_array[2] = null;
    		}
    			    		
    		if ($sortable == true)
    		{
    			$row_array[3] = true;
    		}
    		else
    		{
    			$row_array[3] = false;
    		}
    		
    		if ($row_css_id)
    		{
    			$row_array[4] = $row_css_id;
    		}
    		else
    		{
    			$row_array[4] = null;
    		} 
    		
    		array_push($this->rows, $row_array);
    	}
    	else
    	{
    		return false;
    	}
    }
    
    public function run()
    {
	    if ($GLOBALS['autoload_prefix'])
		{
			$path_prefix = $GLOBALS['autoload_prefix'];
		}
		else
		{
			$path_prefix = "";
		}
	
    	$template = new Template($path_prefix."template/base/list/list.html");	
		
    	$template->set_var("ajax_handler", $this->ajax_handler);
    	$template->set_var("ajax_run", $this->ajax_run);
    	$template->set_var("argument_array", json_encode($this->argument_array));
    	$template->set_var("css_main_id", $this->css_main_id);
    	$template->set_var("css_page_id", $this->css_main_id."Page");
    	$template->set_var("css_row_sort_id", $this->css_main_id."Row");
    	$template->set_var("row_array", json_encode($this->rows));
    	
		$template->output();
    }
}