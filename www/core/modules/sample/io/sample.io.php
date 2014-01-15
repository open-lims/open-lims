<?php
/**
 * @package sample
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
 * Sample IO Class
 * @package sample
 */
class SampleIO
{
	public static function list_user_related_samples()
	{
		$list = new List_IO("SampleUserRelated", "ajax.php?nav=sample", "list_user_related_samples", "count_user_related_samples", "0", "SampleAjaxMySamples");
		
		$list->add_column("","symbol",false,"16px");
		$list->add_column(Language::get_message("SampleGeneralListColumnSampleID", "general"),"id",true,"11%");
		$list->add_column(Language::get_message("SampleGeneralListColumnSampleName", "general"),"name",true,null);
		$list->add_column(Language::get_message("SampleGeneralListColumnDateTime", "general"),"datetime",true,null);
		$list->add_column(Language::get_message("SampleGeneralListColumnTypeTemplate", "general"),"template",true,null);
		$list->add_column(Language::get_message("SampleGeneralListColumnCurrentLocation", "general"),"location",true,null);
		$list->add_column(Language::get_message("SampleGeneralListColumnAvailable", "general"),"av",false,"20px");
		
		$template = new HTMLTemplate("sample/list_user.html");	
		
		$template->set_var("list", $list->get_list());
		
		$paramquery = array();
		$paramquery['username'] = $_GET['username'];
		$paramquery['session_id'] = $_GET['session_id'];
		$paramquery['nav'] = "sample";
		$paramquery['run'] = "new";
		$params = http_build_query($paramquery, '', '&#38;');
		
		$template->set_var("new_sample_params", $params);
		
		$template->output();
	}

	/**
	 * @throws OrganisationUnitIDMissingException
	 * @throws OrganisationUnitNotFoundException
	 */
	public static function list_organisation_unit_related_samples()
	{
		if ($_GET['ou_id'])
		{
			try
			{
				$organisation_unit_id = $_GET['ou_id'];
				
				$argument_array = array();
				$argument_array[0] = "organisation_unit_id";
				$argument_array[1] = $organisation_unit_id;
				
				$list = new List_IO("SampleOrganisationUnitRelated", "ajax.php?nav=sample", "list_organisation_unit_related_samples", "count_organisation_unit_related_samples", $argument_array, "SampleAjaxMySamples", 12);
				
				$list->add_column("","symbol",false,"16px");
				$list->add_column(Language::get_message("SampleGeneralListColumnSampleID", "general"),"id",true,"11%");
				$list->add_column(Language::get_message("SampleGeneralListColumnSampleName", "general"),"name",true,null);
				$list->add_column(Language::get_message("SampleGeneralListColumnDateTime", "general"),"datetime",true,null);
				$list->add_column(Language::get_message("SampleGeneralListColumnTypeTemplate", "general"),"template",true,null);
				$list->add_column(Language::get_message("SampleGeneralListColumnCurrentLocation", "general"),"location",true,null);
				$list->add_column(Language::get_message("SampleGeneralListColumnAvailable", "general"),"av",false,"16px");
							
				require_once("core/modules/organisation_unit/io/organisation_unit.io.php");
				$organisation_unit_io = new OrganisationUnitIO;
				$organisation_unit_io->detail();
				
				$template = new HTMLTemplate("sample/list.html");
	
				$template->set_var("list", $list->get_list());
				
				$template->output();
			}
			catch (OrganisationUnitNotFoundException $e)
			{
				throw $e;
			}
		}
		else
		{
			throw new OrganisationUnitIDMissingException();
		}
	}
	
	/**
	 * @param integer $item_id
	 * @param bool $in_assistant
	 * @param bool $form_field_name
	 * @throws ItemIDMissingException
	 */
	public static function list_samples_by_item_id($item_id, $in_assistant = false, $form_field_name = null)
	{
		if (is_numeric($item_id))
		{
			$argument_array = array();
			$argument_array[0][0] = "item_id";
			$argument_array[0][1] = $item_id;
			$argument_array[1][0] = "in_assistant";
			$argument_array[1][1] = $in_assistant;
			
			if ($in_assistant == false)
			{
				$list = new List_IO("SampleByItem", "ajax.php?nav=sample", "list_samples_by_item_id", "count_samples_by_item_id", $argument_array, "SampleParentAjax", 20, true, true);
				
				$template = new HTMLTemplate("sample/list_parents.html");
				
				$list->add_column("","symbol",false,"16px");
				$list->add_column(Language::get_message("SampleGeneralListColumnSampleID", "general"),"sid",true,"11%");
				$list->add_column(Language::get_message("SampleGeneralListColumnSampleName", "general"),"name",true,null);
				$list->add_column(Language::get_message("SampleGeneralListColumnDate", "general"),"datetime",true,null);
				$list->add_column(Language::get_message("SampleGeneralListColumnTypeTemplate", "general"),"template",true,null);
				$list->add_column(Language::get_message("SampleGeneralListColumnCurrentLocation", "general"),"location",true,null);
				$list->add_column(Language::get_message("SampleGeneralListColumnOwner", "general"),"owner",true,null);
				$list->add_column(Language::get_message("SampleGeneralListColumnAvailable", "general"),"av",false,"16px");
			}
			else
			{
				$list = new List_IO("SampleByItem", "ajax.php?nav=sample", "list_samples_by_item_id", "count_samples_by_item_id", $argument_array, "SampleParentAjax", 20, false, false);
				
				$template = new HTMLTemplate("sample/list_parents_without_border.html");
				
				$list->add_column("","checkbox",false,"16px", $form_field_name);
				$list->add_column("","symbol",false,"16px");
				$list->add_column(Language::get_message("SampleGeneralListColumnSampleID", "general"),"sid",false,"11%");
				$list->add_column(Language::get_message("SampleGeneralListColumnSampleName", "general"),"name",false,null);
				$list->add_column(Language::get_message("SampleGeneralListColumnDate", "general"),"datetime",false,null);
				$list->add_column(Language::get_message("SampleGeneralListColumnTypeTemplate", "general"),"template",false,null);
				$list->add_column(Language::get_message("SampleGeneralListColumnCurrentLocation", "general"),"location",false,null);
				$list->add_column(Language::get_message("SampleGeneralListColumnAvailable", "general"),"owner",false,null);
			}
		
			$template->set_var("list", $list->get_list());
			
			$template->output();
		}
		else
		{
			throw new ItemIDMissingException();
		}
	}
	
	/**
	 * @param string $item_holder_type
	 * @param integer $item_holder_id
	 * @param bool $as_page
	 * @param bool $in_assistant
	 * @param string $form_field_name
	 * @throws ItemHolderTypeMissingException
	 * @throws ItemHolderIDMissingException
	 */
	public static function list_sample_items($item_holder_type, $item_holder_id, $as_page = true, $in_assistant = false, $form_field_name = null)
	{
		if (!$item_holder_type)
		{
			throw new ItemHolderTypeMissingException();
		}
		
		if (!is_numeric($item_holder_id))
		{
			throw new ItemHolderIDMissingException();
		}
		
		$argument_array = array();
		$argument_array[0][0] = "item_holder_type";
		$argument_array[0][1] = $item_holder_type;
		$argument_array[1][0] = "item_holder_id";
		$argument_array[1][1] = $item_holder_id;
		$argument_array[2][0] = "as_page";
		$argument_array[2][1] = $as_page;
		$argument_array[3][0] = "in_assistant";
		$argument_array[3][1] = $in_assistant;
		
		if ($in_assistant == false)
		{
			$list = new List_IO("SampleItem", "ajax.php?nav=sample", "list_sample_items", "count_sample_items",  $argument_array, "SampleAjax", 20, true, true);
			
			$template = new HTMLTemplate("sample/list.html");
			
			$list->add_column("","symbol",false,"16px");
			$list->add_column(Language::get_message("SampleGeneralListColumnSampleID", "general"),"sid",true,"11%");
			$list->add_column(Language::get_message("SampleGeneralListColumnSampleName", "general"),"name",true,null);
			$list->add_column(Language::get_message("SampleGeneralListColumnDate", "general"),"datetime",true,null);
			$list->add_column(Language::get_message("SampleGeneralListColumnTypeTemplate", "general"),"template",true,null);
			$list->add_column(Language::get_message("SampleGeneralListColumnCurrentLocation", "general"),"location",true,null);
			$list->add_column(Language::get_message("SampleGeneralListColumnOwner", "general"),"owner",true,null);
			$list->add_column(Language::get_message("SampleGeneralListColumnAvailable", "general"),"av",false,"16px");
		}
		else
		{
			$list = new List_IO("SampleItem", "ajax.php?nav=sample", "list_sample_items", "count_sample_items", $argument_array, "SampleAjax", 20, false, false);
			
			$template = new HTMLTemplate("sample/list_without_border.html");
			
			$list->add_column("","checkbox",false,"16px", $form_field_name);
			$list->add_column("","symbol",false,"16px");
			$list->add_column(Language::get_message("SampleGeneralListColumnSampleID", "general"),"sid",false,"11%");
			$list->add_column(Language::get_message("SampleGeneralListColumnSampleName", "general"),"name",false,null);
			$list->add_column(Language::get_message("SampleGeneralListColumnDate", "general"),"datetime",false,null);
			$list->add_column(Language::get_message("SampleGeneralListColumnTypeTemplate", "general"),"template",false,null);
			$list->add_column(Language::get_message("SampleGeneralListColumnCurrentLocation", "general"),"location",false,null);
			$list->add_column(Language::get_message("SampleGeneralListColumnOwner", "general"),"owner",false,null);
		}
		
		$template->set_var("list", $list->get_list());
		
		$template->output();
	}
	
	/**
	 * @param array $type_array
	 * @param array $category_array
	 * @param integer $organisation_id
	 * @param string $holder_class
	 * @param integer $holder_id
	 */
	public static function create($type_array = null, $category_array = null, $organisation_unit_id = null, $holder_class = null, $holder_id = null)
	{		
		global $session;

		if($_GET['run'] == "new_subsample" and is_numeric($_GET['sample_id']))
		{
			$sample = new Sample($_GET['sample_id']);
			
			$organisation_unit_id = $sample->get_organisation_unit_id();
			$holder_class = "Sample";
			$holder_id = $_GET['sample_id'];
			
			$paramquery = array();
			$paramquery['username'] = $_GET['username'];
			$paramquery['session_id'] = $_GET['session_id'];
			$paramquery['nav'] = "sample";
			$paramquery['run'] = "detail";
			$paramquery['sample_id'] = $_GET['sample_id'];
			$_GET['retrace'] = base64_encode(serialize($paramquery));
		}
		
		if($_GET['run'] == "item_add" or ($_GET['run'] == "new_subsample" and is_numeric($_GET['sample_id'])))
		{	
			if ($session->is_value("ADD_ITEM_TEMP_KEYWORDS_".$_GET['idk_unique_id']) == true)
			{
				$session->write_value("SAMPLE_ITEM_KEYWORDS", $session->read_value("ADD_ITEM_TEMP_KEYWORDS_".$_GET['idk_unique_id']));
			}
			else
			{
				$session->write_value("SAMPLE_ITEM_KEYWORDS", null);
			}
			
			if ($session->is_value("ADD_ITEM_TEMP_DESCRIPTION_".$_GET['idk_unique_id']) == true)
			{
				$session->write_value("SAMPLE_ITEM_DESCRIPTION", $session->read_value("ADD_ITEM_TEMP_DESCRIPTION_".$_GET['idk_unique_id']));
			}
			else
			{
				$session->write_value("SAMPLE_ITEM_DESCRIPTION", null);
			}
			
			if ($_GET['dialog'] == "parentsample")
			{
				$session->write_value("SAMPLE_ADD_ROLE", "item_parent", true);
			}
			else
			{
				$session->write_value("SAMPLE_ADD_ROLE", "item", true);
				$session->write_value("SAMPLE_ADD_ITEM_HOLDER_CLASS", $holder_class, true);
				$session->write_value("SAMPLE_ADD_ITEM_HOLDER_ID", $holder_id, true);
			}
			
			$session->write_value("SAMPLE_ITEM_RETRACE", $_GET['retrace']);
			$session->write_value("SAMPLE_ITEM_GET_ARRAY", $_GET);
			$session->write_value("SAMPLE_ITEM_TYPE_ARRAY", $type_array);
			$session->write_value("SAMPLE_ORGANISATION_UNIT", $organisation_unit_id);
		}
		else
		{
			$session->write_value("SAMPLE_ADD_ROLE", "sample", true);
			
			$session->delete_value("SAMPLE_RETRACE");
			$session->delete_value("SAMPLE_ITEM_GET_ARRAY");
			$session->delete_value("SAMPLE_ITEM_KEYWORDS");
			$session->delete_value("SAMPLE_ITEM_TYPE_ARRAY");
			$session->delete_value("SAMPLE_ITEM_DESCRIPTION");
		}
		
		$template = new HTMLTemplate("sample/create_sample.html");	
		
		require_once("core/modules/base/common/io/assistant.io.php");
		
		$assistant_io = new AssistantIO("ajax.php?nav=sample&run=create_sample", "SampleCreateAssistantField");
		
		$assistant_io->add_screen(Language::get_message("SampleGeneralCreateTabOrganisationUnit", "general"));
		$assistant_io->add_screen(Language::get_message("SampleGeneralCreateTabSampleType", "general"));
		$assistant_io->add_screen(Language::get_message("SampleGeneralCreateTabSampleInformation", "general"));
		$assistant_io->add_screen(Language::get_message("SampleGeneralCreateTabSampleSpecificInformation", "general"));
		$assistant_io->add_screen(Language::get_message("SampleGeneralCreateTabSummary", "general"));

		$template->set_var("content", $assistant_io->get_content());
		
		$template->output();
	}

	/**
	 * @param array $type_array
	 * @param array $category_array
	 */
	public static function clone_sample($type_array = null, $category_array = null)
	{
		global $session;
		
		if($_GET['run'] == "item_add")
		{	
			if ($session->is_value("ADD_ITEM_TEMP_KEYWORDS_".$_GET['idk_unique_id']) == true)
			{
				$session->write_value("SAMPLE_ITEM_KEYWORDS", $session->read_value("ADD_ITEM_TEMP_KEYWORDS_".$_GET['idk_unique_id']));
			}
			else
			{
				$session->write_value("SAMPLE_ITEM_KEYWORDS", null);
			}
			
			if ($session->is_value("ADD_ITEM_TEMP_DESCRIPTION_".$_GET['idk_unique_id']) == true)
			{
				$session->write_value("SAMPLE_ITEM_DESCRIPTION", $session->read_value("ADD_ITEM_TEMP_DESCRIPTION_".$_GET['idk_unique_id']));
			}
			else
			{
				$session->write_value("SAMPLE_ITEM_DESCRIPTION", null);
			}
			
			if ($_GET['dialog'] == "parentsample")
			{
				$session->write_value("SAMPLE_CLONE_ROLE", "item_parent", true);
			}
			else
			{
				$session->write_value("SAMPLE_CLONE_ROLE", "item", true);
			}
			
			$session->write_value("SAMPLE_ITEM_RETRACE", $_GET['retrace']);
			$session->write_value("SAMPLE_ITEM_GET_ARRAY", $_GET);
			$session->write_value("SAMPLE_ITEM_TYPE_ARRAY", $type_array);
			$session->write_value("SAMPLE_ORGANISATION_UNIT", $organisation_unit_id);
		}
		else
		{
			$session->write_value("SAMPLE_CLONE_ROLE", "sample", true);
			
			$session->delete_value("SAMPLE_RETRACE");
			$session->delete_value("SAMPLE_ITEM_GET_ARRAY");
			$session->delete_value("SAMPLE_ITEM_KEYWORDS");
			$session->delete_value("SAMPLE_ITEM_TYPE_ARRAY");
			$session->delete_value("SAMPLE_ITEM_DESCRIPTION");
		}
		
		if ($type_array)
		{
			$session->write_value("SAMPLE_CLONE_TYPE_ARRAY", $type_array, true);
		}
		
		if ($category_array)
		{
			$session->write_value("SAMPLE_CLONE_CATEGORY_ARRAY", $type_array, true);
		}
		
		$template = new HTMLTemplate("sample/clone_sample.html");	
		
		require_once("core/modules/base/common/io/assistant.io.php");
		
		$assistant_io = new AssistantIO("ajax.php?nav=sample&run=clone_sample", "SampleCloneAssistantField");
		
		$assistant_io->add_screen(Language::get_message("SampleGeneralCloneTabSourceSample", "general"));
		$assistant_io->add_screen(Language::get_message("SampleGeneralCloneTabSampleInformation", "general"));
		$assistant_io->add_screen(Language::get_message("SampleGeneralCloneTabSampleValues", "general"));
		$assistant_io->add_screen(Language::get_message("SampleGeneralCloneTabSampleItems", "general"));
		$assistant_io->add_screen(Language::get_message("SampleGeneralCloneTabSummary", "general"));

		$template->set_var("content", $assistant_io->get_content());
		
		$template->output();
	}
	
	/**
	 * @param array $type_array
	 * @param array $category_array
	 * @param string $holder_class
	 * @param integer $holder_id
	 * @param intger $position_id
	 * @return integer
	 */
	public static function add_sample_item($type_array, $category_array, $holder_class, $holder_id, $position_id)
	{
		global $session;
				
		if (class_exists($holder_class))
		{
			$item_holder = new $holder_class($holder_id);
			
			if ($item_holder instanceof ItemHolderInterface)
			{
				$organisation_unit_id = $item_holder->get_item_holder_value("organisation_unit_id", $position_id);
			}
		}
		
		if (!$_GET['selectpage'])
		{
			$unique_id = uniqid();
			
			if ($_POST['keywords'])
			{
				$session->write_value("ADD_ITEM_TEMP_KEYWORDS_".$unique_id, $_POST['keywords'], true);
			}
			
			if ($_POST['description'])
			{
				$session->write_value("ADD_ITEM_TEMP_DESCRIPTION_".$unique_id, $_POST['description'], true);
			}
			
			$template = new HTMLTemplate("sample/add_as_item.html");
		
			$result = array();
			$counter = 0;
			
			foreach ($_GET as $key => $value)
			{
				$result[$counter]['name'] = $key;
				$result[$counter]['value'] = $value;
				$counter++;
			}
		
			$template->set_var("get_value", $result);
			$template->set_var("unique_id", $unique_id);
			
			$template->output();
		}
		else
		{			
			if ($_GET['selectpage'] == 1)
			{
				return self::create($type_array, $category_array, $organisation_unit_id, $holder_class, $holder_id);
			}
			elseif ($_GET['selectpage'] == 2)
			{
				return self::associate($type_array, $category_array);
			}
			else
			{
				return self::clone_sample($type_array, $category_array);
			}
		}
	}

	/**
	 * @param array $type_array
	 * @param array $category_array
	 */
	public static function associate($type_array, $category_array)
	{
		global $user, $session;
					
		$template = new HTMLTemplate("sample/associate.html");

		$template->set_var("username", $_GET['username']);
		$template->set_var("session_id", $_GET['session_id']);
		$template->set_var("get_array", serialize($_GET));
		
		$result = array();
		$sample_array = Sample::list_user_related_samples($user->get_user_id());
		
		if (!is_array($type_array) or count($type_array) == 0)
		{
			$type_array = null;
		}

		if (is_array($sample_array) and count($sample_array) >= 1)
		{
			$counter = 0;
			
			foreach($sample_array as $key => $value)
			{
				$sample = new Sample($value);
				
				if ($type_array == null or in_array($sample->get_template_id(), $type_array))
				{
					$result[$counter]['value'] = $value;
					$result[$counter]['content'] = $sample->get_name();
					if ($_POST['sample'] == $value)
					{
						$result[$counter]['selected'] = "selected";
					}
					else
					{
						$result[$counter]['selected'] = "";
					}
					$counter++;
				}
			}
		}
		else
		{
			$result[0]['value'] = 0;
			$result[0]['content'] = "You have no samples";
			$result[0]['selected'] = "";
		}
		$template->set_var("sample", $result);
		
		$template->output();
	}

	/**
	 * @throws SampleIDMissingException
	 * @throws SampleSecurityAccessDeniedException
	 */
	public static function detail()
	{
		global $sample_security, $user;
		
		if ($_GET['sample_id'])
		{
			if ($sample_security->is_access(1, false))
			{
				$sample = new Sample($_GET['sample_id']);
							
				$template = new HTMLTemplate("sample/detail.html");
				
				$paper_size_array = PaperSize::list_entries();
				
				$template->set_var("title", $sample->get_formatted_id()." : ".$sample->get_name());
				
				$template->set_var("paper_size_array", $paper_size_array);
				$template->set_var("get_array", serialize($_GET));
				$template->set_var("id", $sample->get_formatted_id());
				
				$template->output();
			}
			else
			{
				throw new SampleSecurityAccessDeniedException();
			}
		}
		else
		{
			throw new SampleIDMissingException();
		}
	}

	/**
	 * @throws SampleIDMissingException
	 * @throws SampleSecurityAccessDeniedException
	 */
	public static function move()
	{
		global $user, $sample_security;

		if ($_GET['sample_id'])
		{
			if ($sample_security->is_access(2, false))
			{
				$sample_id = $_GET['sample_id'];		
				$sample = new Sample($sample_id);
				
				if ($_GET['nextpage'] == 1)
				{
					if (is_numeric($_POST['location']))
					{
						$page_1_passed = true;
					}
					else
					{
						$page_1_passed = false;
						$error = "You must select a location.";
					}
				}
				elseif($_GET['nextpage'] > 1)
				{
					$page_1_passed = true;
				}
				else
				{
					$page_1_passed = false;
					$error = "";
				}
				
				if ($page_1_passed == false)
				{
					$template = new HTMLTemplate("sample/move.html");
						
					$paramquery = $_GET;
					$paramquery['nextpage'] = "1";
					$params = http_build_query($paramquery,'','&#38;');
					
					$template->set_var("params",$params);
					
					$template->set_var("error",$error);
					
					$result = array();
					$counter = 0;
					
					$sample_location_array = Location::list_entries();
						
					if (is_array($sample_location_array) and count($sample_location_array) >= 1)
					{
						foreach($sample_location_array as $key => $value)
						{
							$sample_location_obj = new Location($value);
											
							$result[$counter]['value'] = $value;
							$result[$counter]['content'] = $sample_location_obj->get_name(true);		
		
							$counter++;
						}
					}
					else
					{
						$result[$counter]['value'] = "0";
						$result[$counter]['content'] = "NO LOCATIONS FOUND!";
					}

					$template->set_var("option",$result);
					
					$template->output();
				}
				else
				{
					$paramquery = $_GET;
					unset($paramquery['nextpage']);
					$paramquery['run'] = "detail";
					$params = http_build_query($paramquery);
					
					if ($sample->add_location($_POST['location']))
					{
						Common_IO::step_proceed($params, "Move Sample", "Operation Successful", null);
					}
					else
					{
						Common_IO::step_proceed($params, "Move Sample", "Operation Failed" ,null);	
					}
				}
			}
			else
			{
				throw new SampleSecurityAccessDeniedException();
			}
		}
		else
		{
			throw new SampleIDMissingException();
		}
	}
	
	/**
	 * @throws SampleIDMissingException
	 * @throws SampleSecurityAccessDeniedException
	 */
	public static function set_availability()
	{
		global $sample_security;
		
		if ($_GET['sample_id'])
		{
			if ($sample_security->is_access(2, false))
			{
				if ($_GET['sure'] != "true")
				{
					$template = new HTMLTemplate("sample/set_availability.html");
					
					$paramquery = $_GET;
					$paramquery['sure'] = "true";
					$params = http_build_query($paramquery);
					
					$template->set_var("yes_params", $params);
							
					$paramquery = $_GET;
					unset($paramquery['nextpage']);
					unset($paramquery['id']);
					$paramquery['run'] = "admin_permission";
					$params = http_build_query($paramquery);
					
					$template->set_var("no_params", $params);
					
					$template->output();
				}
				else
				{
					$sample = new Sample($_GET['sample_id']);
					
					$paramquery = $_GET;
					unset($paramquery['nextpage']);
					unset($paramquery['sure']);
					$paramquery['run'] = "detail";
					$params = http_build_query($paramquery);
					
					if ($sample->get_availability() == true)
					{
						if ($sample->set_availability(false))
						{							
							Common_IO::step_proceed($params, "Delete Permission", "Operation Successful" ,null);
						}
						else
						{							
							Common_IO::step_proceed($params, "Delete Permission", "Operation Failed" ,null);
						}
					}
					else
					{
						if ($sample->set_availability(true))
						{							
							Common_IO::step_proceed($params, "Delete Permission", "Operation Successful" ,null);
						}
						else
						{							
							Common_IO::step_proceed($params, "Delete Permission", "Operation Failed" ,null);
						}
					}		
				}
			}
			else
			{
				throw new SampleSecurityAccessDeniedException();
			}
		}
		else
		{
			throw new SampleIDMissingException();
		}
	}

	/**
	 * @throws SampleIDMissingException
	 * @throws SampleSecurityAccessDeniedException
	 */
	public static function location_history()
	{
		global $sample_security;
	
		if ($_GET['sample_id'])
		{
			if ($sample_security->is_access(1, false))
			{
				$argument_array = array();
				$argument_array[0] = "sample_id";
				$argument_array[1] = $_GET['sample_id'];
				
				$list = new List_IO("SampleLocationHistory", "ajax.php?nav=sample", "list_location_history", "count_location_history", $argument_array, "SampleLocationHistory");
		
				$list->add_column("","symbol",false,"16px");
				$list->add_column(Language::get_message("SampleGeneralListColumnName", "general"),"name",true,null);
				$list->add_column(Language::get_message("SampleGeneralListColumnDateTime", "general"),"datetime",true,null);
				$list->add_column(Language::get_message("SampleGeneralListColumnUser", "general"),"user",true,null);

				$template = new HTMLTemplate("sample/location_history.html");
				
				$sample = new Sample($_GET['sample_id']);
				
				$template->set_var("sample_id",$sample->get_formatted_id());
				$template->set_var("sample_name","(".$sample->get_name().")");
				
				$template->set_var("list", $list->get_list());
				
				$template->output();
			}
			else
			{
				throw new SampleSecurityAccessDeniedException();
			}
		}
		else
		{
			throw new SampleIDMissingException();
		}
	}
	
}

?>
