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
require_once("interfaces/currency.interface.php");

if (constant("UNIT_TEST") == false or !defined("UNIT_TEST"))
{
	require_once("access/currency.access.php");
}

/**
 * Currency Class
 * @package base
 */
class Currency implements CurrencyInterface
{
	private $currency_id;
	private $currency;
	
	/**
	 * @see CurrencyInterface::__construct()
	 * @param integer $currency_id
	 * @throws BaseEnvironmentCurrencyNotFoundException
	 */
	function __construct($currency_id)
	{
		if (is_numeric($currency_id))
		{
			if (Currency_Access::exist_id($currency_id) == true)
			{
				$this->currency_id = $currency_id;
   	   			$this->currency = new Currency_Access($currency_id);
			}
			else
			{
				throw new BaseEnvironmentCurrencyNotFoundException();
			}
    	}
    	else
    	{
    		$this->currency_id = null;
   	   		$this->currency = new Currency_Access(null);
    	}
	}
	
	/**
	 * @see CurrencyInterface::__destruct()
	 */
	function __destruct()
	{
		unset($this->currency_id);
		unset($this->currency);
	}
	
	/**
	 * @see CurrencyInterface::create()
	 * @param string $name
	 * @param string $symbol
	 * @param stirng $iso_4217
	 * @return integer
	 */
	public function create($name, $symbol, $iso_4217)
	{
		if ($this->currency)
		{
			return $this->currency->create($name, $symbol, $iso_4217);
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @see CurrencyInterface::delete()
	 * @return bool
	 */
	public function delete()
	{
		if ($this->currency_id and $this->currency)
		{
			return $this->currency->delete();
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * @see CurrencyInterface::get_name()
	 * @return string
	 */
	public function get_name()
	{
		if ($this->currency_id and $this->currency)
    	{
    		return $this->currency->get_name();	
    	}
    	else
    	{
    		return null;
    	}
	}
	
	/**
	 * @see CurrencyInterface::get_symbol()
	 * @return string
	 */
	public function get_symbol()
	{
		if ($this->currency_id and $this->currency)
    	{
    		return $this->currency->get_symbol();	
    	}
    	else
    	{
    		return null;
    	}
	}
	
	/**
	 * @see CurrencyInterface::get_iso_4217()
	 * @return string
	 */
	public function get_iso_4217()
	{
		if ($this->currency_id and $this->currency)
    	{
    		return $this->currency->get_iso_4217();	
    	}
    	else
    	{
    		return null;
    	}
	}
	
	
	/**
	 * @see CurrencyInterface::list_entries()
	 * @return array
	 */
	public static function list_entries()
	{
		return Currency_Access::list_entries();
	}
}