<?php
/**
 * @package workflow
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
 * Workflow Common IO Class
 * @package workflow
 */
class WorkflowCommonIO
{
	public static function draw_workflow($workflow_array, $limit = null)
	{
		if (is_array($workflow_array) and count($workflow_array) >= 1)
		{
			$element_space_cache = array();
			
			foreach ($workflow_array as $line_key => $line_value)
			{
				$element_counter = count($line_value);
				$element_space_cache_counter = count($element_space_cache);
				$element_space_temp_cache = array();
				
				$max_columns = max(array_keys($line_value));
				
				if (count($element_space_cache) >= 1)
				{
					$max_element_space_cache = max(array_keys($element_space_cache));
				}
				else
				{
					$max_element_space_cache = 0;
				}
				
				$first_spacer = false;
				
				// Bis zum größeren von $max_columns oder $element_space_cache_counter
				for ($i = 0; $i <= max($max_columns,$max_element_space_cache); $i++)
				{	
					if ($limit > $i or $limit == null)
					{					
						$spacer_template = new HTMLTemplate("workflow/horizontal_spacer.html");
					}
					elseif($limit == $i)
					{
						$spacer_template = new HTMLTemplate("workflow/right_element_horizontal_spacer.html");
					}
					
					if ($element_space_cache[$i] == true or $first_spacer == true)
					{
						if ($first_spacer == false)
						{
							if ($element_counter == 1 and $element_space_cache_counter == 1)
							{
								if ($limit > $i or $limit == null)
								{
									$spacer_template->set_var("spacer_class", "WorkflowElementHorizontalSpacerVerticalLine");
								}
							}
							else
							{
								// OB Unterschied
								if (isset($line_value[$i+1]) and isset($element_space_cache[$i+1])
									 or ($i == max(array_keys($line_value)) and $i ==  max(array_keys($element_space_cache))))
								{
									if ($limit > $i or $limit == null)
									{
										$spacer_template->set_var("spacer_class", "WorkflowElementHorizontalSpacerVerticalLine");
									}
									$element_counter--;
									$element_space_cache_counter--;
								}
								else
								{
									$first_spacer = true;
									if ($limit > $i or $limit == null)
									{
										$spacer_template->set_var("spacer_class", "WorkflowElementHorizontalSpacerLeftT");
									}
								}
							}
						}
						else
						{
							// While Schleife überalle zukünftigen Elemente bis 
							// 1) Oben neues Element ex.
							// 		-> Exisitiert bis dahin unten ein EL. ?
							//			dann: 
							//				-> Existiert unten ein El.
							//				dann: 
							//					-> Ist lertes EL.
							//					dann: TopRightCorner
							//					ansonsten: T
							//				ansonsten: hor-line
							//			ansonsten: nichts
							// 2) Ende der Zeile
							//		-> Prüfung wie 1)
							
							$n = $i;
							$next_top_element = null;
							$last_bottom_element = null;
							
							while ($n <= max($max_columns,$max_element_space_cache))
							{
								if ($element_space_cache[$n] and $i != $n)
								{
									$next_top_element = $n;
									break;
								}
								else
								{
									if ($line_value[$n])
									{
										$last_bottom_element = $n;
									}
								}
								$n = $n+1;	
							}
			
							if ($last_bottom_element == $i or ($next_top_element == ($i+1) and $i <= $max_columns) or ($i == $max_element_space_cache and $i > $max_columns))
							{
								if ($limit > $i or $limit == null)
								{
									if ($line_value[$i])
									{
										$spacer_template->set_var("corner_spacer_class", "WorkflowElementCornerSpacerHorizontalLine");
										$spacer_template->set_var("spacer_class", "WorkflowElementHorizontalSpacerTopRightCorner");
									}
									else
									{
										$spacer_template->set_var("corner_spacer_class", "WorkflowElementCornerSpacerHorizontalLine");
										$spacer_template->set_var("spacer_class", "WorkflowElementHorizontalSpacerBottomRightCorner");
									}	
								}
								elseif($limit == $i)
								{
									if ($line_value[$i])
									{
										$spacer_template->set_var("spacer_class", "WorkflowRightElementHorizontalSpacerTopRight");
										$right_element = false;
									}
									else
									{
										$spacer_template->set_var("spacer_class", "WorkflowRightElementHorizontalSpacerBottomRight");
									}
								}
									
								$first_spacer = false;
							}
							else
							{
								if ($limit > $i or $limit == null)
								{
									if ($line_value[$i])
									{
										$spacer_template->set_var("corner_spacer_class", "WorkflowElementCornerSpacerHorizontalLine");
										$spacer_template->set_var("spacer_class", "WorkflowElementHorizontalSpacerT");
									}
									else
									{
										if ($element_space_cache[$i])
										{
											$spacer_template->set_var("corner_spacer_class", "WorkflowElementCornerSpacerHorizontalLine");
											$spacer_template->set_var("spacer_class", "WorkflowElementHorizontalSpacerReversedT");
										}
										else
										{
											$spacer_template->set_var("corner_spacer_class", "WorkflowElementCornerSpacerHorizontalLine");
											$spacer_template->set_var("spacer_class", "WorkflowElementHorizontalSpacerHorizontalLine");
										}
									}
								}
								elseif($limit == $i)
								{
									if ($line_value[$i])
									{
										$spacer_template->set_var("spacer_class", "WorkflowRightElementHorizontalSpacerT");
										$right_element = false;
									}
									else
									{
										if ($element_space_cache[$i])
										{
											$spacer_template->set_var("spacer_class", "WorkflowRightElementHorizontalSpacerReversedT");
										}
										else
										{
											// El unter oder oben ?
											if ($i < $max_columns)
											{
												$spacer_template->set_var("spacer_class", "WorkflowRightElementHorizontalSpacerTopRight");
											}
											else
											{
												$spacer_template->set_var("spacer_class", "WorkflowRightElementHorizontalSpacerBottomRight");
											}
										}
									}
								}
							}
						}
					}
					
					if ($limit >= $i or $limit == null)
					{
						$element_string .= $spacer_template->get_string();
					}	
					
					if ($line_value[$i])
					{
						$element_value = $line_value[$i];	
						
						if ($limit > $i or $limit == null)
						{
							$element_template = new HTMLTemplate("workflow/element.html");
						}
						elseif($limit == $i)
						{
							$element_template = new HTMLTemplate("workflow/right_element.html");
						}
						
						if (is_object($element_value[0]))
						{
							if ($element_value[0] instanceof WorkflowElementActivity)
							{
								if ($limit > $i or $limit == null)
								{
									$element_template->set_var("name", $element_value[0]->get_attachment("name"));
									if ($element_value[1] == true)
									{
										$element_template->set_var("class", "WorkflowElementStatus WorkflowElementStatusActive");
									}
									else
									{
										$element_template->set_var("class", "WorkflowElementStatus");
									}
								}
								elseif($limit == $i)
								{
									if ($right_element == false)
									{
										$element_template->set_var("class", "WorkflowRightElementTop");
										$right_element = true;
									}
								}
								$element_space_temp_cache[$i] = true;
							}
							
							if ($element_value[0] instanceof WorkflowElementOr)
							{
								if ($limit > $i or $limit == null)
								{
									$element_template->set_var("name", "or");
									$element_template->set_var("class", "WorkflowElementOr");
								}
								elseif($limit == $i)
								{
									if ($right_element == false)
									{
										$element_template->set_var("class", "WorkflowRightElementTop");
										$right_element = true;
									}
								}
								$element_space_temp_cache[$i] = true;
							}
						}
						else
						{
							if ($limit > $i or $limit == null)
							{
								$element_template->set_var("name", "");
								$element_template->set_var("class", "WorkflowElementLine");
							}
							$element_space_temp_cache[$i] = true;
						}
						
						if ($limit >= $i or $limit == null)
						{
							$element_string .= $element_template->get_string();
						}
					}
					else
					{
						if ($limit > $i or $limit == null)
						{
							$element_template = new HTMLTemplate("workflow/empty_element.html");
							$element_string .= $element_template->get_string();
						}
						elseif($limit == $i)
						{
							$element_template = new HTMLTemplate("workflow/right_element.html");
							if ($right_element == false)
							{
								$element_template->set_var("class", "WorkflowRightElementTop");
								$right_element = true;
							}
							$element_string .= $element_template->get_string();
						}
					}
				}
				
				$element_space_cache = $element_space_temp_cache;
						
				// Break
				
				$element_template = new HTMLTemplate("workflow/clear_element.html");
				$element_string .= $element_template->get_string(); 
			}
			
			return $element_string;
		}
		else
		{
			
		}
	}
}