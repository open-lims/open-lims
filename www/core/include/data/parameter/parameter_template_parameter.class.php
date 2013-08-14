<?php
/**
 * @package data
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
 * 
 */
require_once("interfaces/parameter_template_parameter.interface.php");

if (constant("UNIT_TEST") == false or !defined("UNIT_TEST"))
{
	require_once("access/parameter_has_template.access.php");
}

/**
 * Parameter Template Management Class
 * @package data
 */
class ParameterTemplateParameter extends Parameter implements ParameterTemplateParameterInterface
{	
	/**
	 * @see ParameterTemplateParameterInterface::__construct()
	 * @param integer $parameter_id
	 */
	function __construct($parameter_id)
	{
		parent::__construct($parameter_id);
	}
	
	/**
	 * @see ParameterTemplateParameterInterface::__destruct()
	 */
	function __destruct()
	{
		
	}
	
	/**
	 * @see ParameterTemplateParameterInterface::create()
	 * @param integer $folder_id
	 * @param integer $owner_id
	 * @param integer $template_id
	 * @param integer $limit_id
	 * @param array $parameter_array
	 * @return integer
	 * @throws ParameterCreateTemplateLinkFailedException
	 * @throws ParameterCreateIDMissingException
	 */
	public function create($folder_id, $owner_id, $template_id, $limit_id, $parameter_array)
	{
		global $transaction;
		
		if (is_numeric($folder_id) and is_numeric($owner_id) and is_numeric($template_id) and is_numeric($limit_id) and $parameter_array)
		{			
			if (is_array($parameter_array))
			{
				$transaction_id = $transaction->begin();
				
				try
				{
					$parameter_id = parent::create($folder_id, $limit_id, $parameter_array, $owner_id);
					
					$parameter_has_template = new ParameterHasTemplate_Access(null, null);
					if ($parameter_has_template->create($parameter_id, $template_id) == null)
					{
						throw new ParameterCreateTemplateLinkFailedException();
					}
				}
				catch(BaseException $e)
				{
					if ($transaction_id != null)
					{
						$transaction->rollback($transaction_id);
					}
					throw $e;
				}
				
				if ($transaction_id != null)
				{
					$transaction->commit($transaction_id);
				}
				return $parameter_id;
			}
			else
			{
				throw new ParameterCreateIDMissingException();
			}
		}
		else
		{
			throw new ParameterCreateIDMissingException();
		}
	}

	/**
	 * @see ParameterTemplateParameterInterface::delete()
	 * @return bool
	 * @throws ParameterDeleteTemplateLinkFailedException
	 * @throws ParameterDeleteIDMissingException
	 */
	public function delete()
	{
		global $transaction;
		
		if ($this->parameter_id)
		{
			$transaction_id = $transaction->begin();
				
			try
			{
				$parameter_template_id = ParameterHasTemplate_Access::get_template_id_by_parameter_id($this->parameter_id);		
				$parameter_has_template = new ParameterHasTemplate_Access($this->parameter_id, $parameter_template_id);
				
				if ($parameter_has_template->delete() == false)
				{
					throw new ParameterDeleteTemplateLinkFailedException();
				}
				
				parent::delete();
			}
			catch(BaseException $e)
			{
				if ($transaction_id != null)
				{
					$transaction->rollback($transaction_id);
				}
				throw $e;
			}
			
			if ($transaction_id != null)
			{
				$transaction->commit($transaction_id);
			}
			return true;
		}
		else
		{
			throw new ParameterDeleteIDMissingException();
		}
	}
	
	/**
	 * @see ParameterTemplateParameterInterface::get_template_id()
	 * @return integer
	 */
	public function get_template_id()
	{
		if ($this->parameter_id)
		{
			return ParameterHasTemplate_Access::get_template_id_by_parameter_id($this->parameter_id);		
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @see ParameterTemplateParameterInterface::get_name()
	 * @return string
	 */
	public function get_name()
	{
		if ($this->parameter_id)
		{
			$parameter_template = new ParameterTemplate($this->get_template_id());
			return $parameter_template->get_name();	
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @see ParameterTemplateParameterInterface::get_instance()
	 * @param integer $parameter_id
	 * @param bool $force_new_instance
	 * @return object
	 */
	public static function get_instance($parameter_id, $force_new_instance = false)
    {    
    	if (is_numeric($parameter_id) and $parameter_id > 0)
    	{
    		if ($force_new_instance == true)
    		{
    			return new ParameterTemplateParameter($parameter_id);
    		}
    		else
    		{
	    		if (self::$parameter_object_array[$parameter_id])
				{
					return self::$parameter_object_array[$parameter_id];
				}
				else
				{
					$parameter = new ParameterTemplateParameter($parameter_id);
					self::$parameter_object_array[$parameter_id] = $parameter;
					return $parameter;
				}
    		}
    	}
    	else
    	{
    		return new ParameterTemplateParameter(null);
    	}
    }
    
    /**
     * @see ParameterTemplateParameterInterface::is_template_parameter()
     * @param integer $parameter_id
     * @return bool
     */
    public static function is_template_parameter($parameter_id)
    {
    	if (ParameterHasTemplate_Access::get_template_id_by_parameter_id($parameter_id) !== null)
    	{
    		return true;
    	}
    	else
    	{
    		return false;
    	}
    }
    
    /**
     * @see ParameterTemplateParameterInterface::is_template_linked()
     * @param integer $template_id
     * @return bool
     */
	public static function is_template_linked($template_id)
	{
		$parameter_array = ParameterHasTemplate_Access::list_parameter_ids_by_template_id($template_id);
		
		if(is_array($parameter_array) and count($parameter_array) >= 1)
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