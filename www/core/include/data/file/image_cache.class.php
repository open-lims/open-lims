<?php
/**
 * @package data
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
// require_once("interfaces/file.interface.php");
 
if (constant("UNIT_TEST") == false or !defined("UNIT_TEST"))
{
	require_once("access/file_version.access.php");
	require_once("access/file_image_cache.access.php");
}

/**
 * File Class for Management of Files in Folders
 * @package data
 */
class ImageCache // implements ImageCacheInterface, EventListenerInterface
{
	private $file_id;
	private $file_version_id;
	private $file_version_extension;
	private $internal_revision;
	
	private $max_width = null;
	private $max_height = null;
	
	/**
	 * @see ImageCacheInterface::__construct()
	 * @param integer $file_id
	 * @param integer $internal_revision
	 * @throws FileNotFoundException
	 */
	function __construct($file_id, $internal_revision = null)
	{
		if (is_numeric($file_id))
		{
			if (is_numeric($internal_revision))
			{
				$this->file_version_id = FileVersion_Access::get_entry_by_toid_and_internal_revision($file_id, $internal_revision);
				$this->internal_revision = $internal_revision;
			}
			else
			{
				$this->file_version_id = FileVersion_Access::get_current_entry_by_toid($file_id);
				$this->internal_revision = null;
			}
			$this->file_id = $file_id;
			$this->file_version_extension = FileVersion_Access::get_file_extension_by_toid($file_id);
		}
		else
		{
			$this->file_version_id = null;
		}
	}
	
	function __destruct()
	{
		unset($this->file_version_id);
	}
	
	/**
	 * @see ImageCacheInterface::get_image()
	 * @param integer $width
	 * @param integer $height
	 * @return string
	 */
	public function get_image($width = null, $height = null)
	{
		if (is_numeric($width) and $width >= 1)
		{
			if ($this->max_width != null and $this->max_width < $width)
			{
				$width = $this->max_width;
			}
			
			if (($image_cache_id = FileImageCache_Access::get_width_cached($this->file_version_id, $width)) != null)
			{
				$file_image_cache = new FileImageCache_Access($image_cache_id);
				$height = $file_image_cache->get_height();
				$file_image_cache->set_last_access(date("Y-m-d H:i:s"));
				
				if ($this->file_version_extension)
				{
					return $this->file_version_id."-".$width."-".$height.".".$this->file_version_extension;
				}
				else
				{
					return $this->file_version_id."-".$width."-".$height."";
				}
			}
			else
			{
				return $this->register_image($width, null);
			}
		}
		elseif (is_numeric($height) and $height >= 1)
		{
			if ($this->max_height != null and $this->max_height < $height)
			{
				$height = $this->max_height;
			}
			
			if (($image_cache_id = FileImageCache_Access::get_height_cached($this->file_version_id, $height)) != null)
			{
				$file_image_cache = new FileImageCache_Access($image_cache_id);
				$width = $file_image_cache->get_width();
				$file_image_cache->set_last_access(date("Y-m-d H:i:s"));
				
				if ($this->file_version_extension)
				{
					return $this->file_version_id."-".$width."-".$height.".".$this->file_version_extension;
				}
				else
				{
					return $this->file_version_id."-".$width."-".$height."";
				}
			}
			else
			{
				return $this->register_image(null, $height);
			}
		}
		else
		{
			$image = $this->open_image();
			$width = $image->getImageWidth();		
			return $this->get_image($width);
		}
	}
	
	/**
	 * @param integer $width
	 */
	public function set_max_width($width)
	{
		$this->max_width = $width;
	}
	
	/**
	 * @param integer $height
	 */
	public function set_max_height($height)
	{
		$this->max_height = $height;
	}
	
	/**
	 * @return object
	 */
	private function open_image()
	{
		if ($this->file_id)
		{
			$file = File::get_instance($this->file_id);
					
			if ($this->internal_revision)
			{
				$file->open_internal_revision($this->internal_revision);
			}
			
			if ($file->is_read_access() == true)
			{
				$folder = Folder::get_instance($file->get_parent_folder_id());
				$folder_path = $folder->get_path();
				
				$extension_array = explode(".",$file->get_name());
				$extension_array_length = substr_count($file->get_name(),".");
				
	
				$file_path = constant("BASE_DIR")."/".$folder_path."/".$file->get_data_entity_id()."-".$file->get_internal_revision().".".$extension_array[$extension_array_length];
				if (file_exists($file_path))
				{
					return new Imagick($file_path);
				}
			}
		}
	}
	
	/**
	 * @param integer $width
	 * @param intger $height
	 * @return string
	 */
	private function register_image($width, $height)
	{
		if ($this->file_id and ($width or $height))
		{
			$image = $this->open_image();
			
			if ($image->getImageFormat() != "PNG")
			{
				$image->setImageFormat("jpg");	
			}
			
			if ($width)
			{
				$image->thumbnailImage($width,0);
			}
			elseif ($height)
			{
				$image->thumbnailImage(0,$height);
			}
			else
			{
				return null;
			}
			
			if ($this->file_version_extension)
			{
				$cached_file_name = $this->file_version_id."-".$image->getImageWidth()."-".$image->getImageHeight().".".$this->file_version_extension;
			}
			else
			{
				$cached_file_name = $this->file_version_id."-".$image->getImageWidth()."-".$image->getImageHeight()."";
			}
			
			$image->writeImage(constant("BASE_DIR")."/filesystem/temp/".$cached_file_name);
			
			$file_image_cache = new FileImageCache_Access(null);
			$file_image_cache->create($this->file_version_id, $image->getImageWidth(), $image->getImageHeight(), filesize(constant("BASE_DIR")."/filesystem/temp/".$cached_file_name));

			return $cached_file_name;
		}
		else
		{
			return null;
		}
	}
}
?>