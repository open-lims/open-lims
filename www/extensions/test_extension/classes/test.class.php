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
 * Business Logic Class of the test extension
 * @package extension
 */
class Test
{
	private $file = "data.txt";
	private $handle;
	private $content;
	private $id;
	private $last_id;
	
	function __construct($id)
	{
		$this->handle = fopen($this->file, "w+");
		
		if (filesize($this->file) < 0)
		{
			$this->content = fread($this->handle, filesize($this->file));
		}
		
		if (is_numeric($id))
		{
			$this->id = $id;
		}
		
		if ($this->content)
		{
			$analysis_array = explode("\n",$this->content);
			$analysis_count = count($this->content, "\n");
			$analysis_line_array = explode(",",$analyis_array[$analysis_count]);
			$this->last_id = $analysis_line_array[0];
		}
	}
	
	public function start_analysis()
	{
		if ($this->handle)
		{
			$microtime = mktime();
			
			if ($this->content and $this->last_id)
			{
				$new_id = $this->last_id+1;
				fwrite($this->handle, "\n".$new_id.",".$microtime);
				$this->content .= "\n".$new_id.",".$microtime;
				return $new_id;
			}
			else
			{
				fwrite($this->handle, "1,".$microtime);
				$this->content = "1,".$microtime;
				return 1;
			}
		}
	}
	
	public function get_status()
	{
		if ($this->content)
		{
			$analysis_array = explode("\n",$this->content);
			
			if (is_array($analysis_array) and count($analysis_array) >= 1)
			{
				$microtime = mktime();
				
				foreach($analysis_array as $key => $value)
				{
					$analysis_line_array = explode(",",$value);
					if ($analysis_line_array[0] == $this->id)
					{
						if (($microtime+60) < $analysis_line_array[1])
						{
							return 1;
						}
						else
						{
							return 0;
						}
					}
				}
				return -1;
			}
			else
			{
				return -1;
			}
		}
		else
		{
			return -1;
		}
	}
	
	public function get_content()
	{
		if ($this->content)
		{
			return $this->content;
		}
		else
		{
			return null;
		}
	}
}
?>