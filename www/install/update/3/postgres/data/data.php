<?php
/**
 * @package install
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
$statement = array();

$statement[] = "UPDATE core_base_includes SET db_version = '0.4.0.0' WHERE name='data'";

$statement[] = "INSERT INTO core_base_include_tables (id,include,table_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'data','core_data_parameter_field_has_methods', NULL);";
$statement[] = "INSERT INTO core_base_include_tables (id,include,table_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'data','core_data_parameter_field_limits', NULL);";
$statement[] = "INSERT INTO core_base_include_tables (id,include,table_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'data','core_data_parameter_field_values', NULL);";
$statement[] = "INSERT INTO core_base_include_tables (id,include,table_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'data','core_data_parameter_fields', NULL);";
$statement[] = "INSERT INTO core_base_include_tables (id,include,table_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'data','core_data_parameter_has_non_template', NULL);";
$statement[] = "INSERT INTO core_base_include_tables (id,include,table_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'data','core_data_parameter_has_template', NULL);";
$statement[] = "INSERT INTO core_base_include_tables (id,include,table_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'data','core_data_parameter_limits', NULL);";
$statement[] = "INSERT INTO core_base_include_tables (id,include,table_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'data','core_data_parameter_methods', NULL);";
$statement[] = "INSERT INTO core_base_include_tables (id,include,table_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'data','core_data_parameter_non_template_has_fields', NULL);";
$statement[] = "INSERT INTO core_base_include_tables (id,include,table_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'data','core_data_parameter_non_templates', NULL);";
$statement[] = "INSERT INTO core_base_include_tables (id,include,table_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'data','core_data_parameter_template_has_fields', NULL);";
$statement[] = "INSERT INTO core_base_include_tables (id,include,table_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'data','core_data_parameter_templates', NULL);";
$statement[] = "INSERT INTO core_base_include_tables (id,include,table_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'data','core_data_parameter_versions', NULL);";
$statement[] = "INSERT INTO core_base_include_tables (id,include,table_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'data','core_data_parameters', NULL);";


// Parameter Example Data

$statement[] = "INSERT INTO core_data_parameter_templates (id, internal_name, name, created_by, datetime) VALUES (1, 'BC', 'Blood Count', 1, '2011-01-01 08:00:00+01')";
$statement[] = "INSERT INTO core_data_parameter_templates (id, internal_name, name, created_by, datetime) VALUES (2, 'DBC', 'Differential Blood Count', 1, '2011-01-01 08:00:00+01')";
$statement[] = "SELECT pg_catalog.setval('core_data_parameter_templates_id_seq', 3, true);";

$statement[] = "INSERT INTO core_data_parameter_fields (id, name, min_value, max_value, measuring_unit_id, measuring_unit_exponent, measuring_unit_ratio_id) VALUES (1, 'WBC', 0, NULL, NULL, NULL, 1)";
$statement[] = "INSERT INTO core_data_parameter_fields (id, name, min_value, max_value, measuring_unit_id, measuring_unit_exponent, measuring_unit_ratio_id) VALUES (2, 'RBC', 0, NULL, NULL, NULL, 2)";
$statement[] = "INSERT INTO core_data_parameter_fields (id, name, min_value, max_value, measuring_unit_id, measuring_unit_exponent, measuring_unit_ratio_id) VALUES (3, 'HGB', 0, NULL, NULL, NULL, 3)";
$statement[] = "INSERT INTO core_data_parameter_fields (id, name, min_value, max_value, measuring_unit_id, measuring_unit_exponent, measuring_unit_ratio_id) VALUES (4, 'HCT', 0, 100, 27, 0, NULL)";
$statement[] = "INSERT INTO core_data_parameter_fields (id, name, min_value, max_value, measuring_unit_id, measuring_unit_exponent, measuring_unit_ratio_id) VALUES (5, 'MCV', 0, NULL, 24, -15, NULL)";
$statement[] = "INSERT INTO core_data_parameter_fields (id, name, min_value, max_value, measuring_unit_id, measuring_unit_exponent, measuring_unit_ratio_id) VALUES (6, 'MCH', 0, NULL, 7, -12, NULL)";
$statement[] = "INSERT INTO core_data_parameter_fields (id, name, min_value, max_value, measuring_unit_id, measuring_unit_exponent, measuring_unit_ratio_id) VALUES (7, 'MCHC', 0, NULL, NULL, NULL, 3)";
$statement[] = "INSERT INTO core_data_parameter_fields (id, name, min_value, max_value, measuring_unit_id, measuring_unit_exponent, measuring_unit_ratio_id) VALUES (8, 'RDW', 0, 100, 27, 0, NULL)";
$statement[] = "INSERT INTO core_data_parameter_fields (id, name, min_value, max_value, measuring_unit_id, measuring_unit_exponent, measuring_unit_ratio_id) VALUES (9, 'Plt', 0, NULL, NULL, NULL, 1)";
$statement[] = "INSERT INTO core_data_parameter_fields (id, name, min_value, max_value, measuring_unit_id, measuring_unit_exponent, measuring_unit_ratio_id) VALUES (10, 'MPV', 0, NULL, 24, -15, NULL)";
$statement[] = "INSERT INTO core_data_parameter_fields (id, name, min_value, max_value, measuring_unit_id, measuring_unit_exponent, measuring_unit_ratio_id) VALUES (11, 'NE%', 0, 100, 27, 0, NULL)";
$statement[] = "INSERT INTO core_data_parameter_fields (id, name, min_value, max_value, measuring_unit_id, measuring_unit_exponent, measuring_unit_ratio_id) VALUES (12, 'LY%', 0, 100, 27, 0, NULL)";
$statement[] = "INSERT INTO core_data_parameter_fields (id, name, min_value, max_value, measuring_unit_id, measuring_unit_exponent, measuring_unit_ratio_id) VALUES (13, 'MO%', 0, 100, 27, 0, NULL)";
$statement[] = "INSERT INTO core_data_parameter_fields (id, name, min_value, max_value, measuring_unit_id, measuring_unit_exponent, measuring_unit_ratio_id) VALUES (14, 'EO%', 0, 100, 27, 0, NULL)";
$statement[] = "INSERT INTO core_data_parameter_fields (id, name, min_value, max_value, measuring_unit_id, measuring_unit_exponent, measuring_unit_ratio_id) VALUES (15, 'BA%', 0, 100, 27, 0, NULL)";
$statement[] = "INSERT INTO core_data_parameter_fields (id, name, min_value, max_value, measuring_unit_id, measuring_unit_exponent, measuring_unit_ratio_id) VALUES (16, 'NE#', 0, NULL, NULL, NULL, 1)";
$statement[] = "INSERT INTO core_data_parameter_fields (id, name, min_value, max_value, measuring_unit_id, measuring_unit_exponent, measuring_unit_ratio_id) VALUES (17, 'LY#', 0, NULL, NULL, NULL, 1)";
$statement[] = "INSERT INTO core_data_parameter_fields (id, name, min_value, max_value, measuring_unit_id, measuring_unit_exponent, measuring_unit_ratio_id) VALUES (18, 'MO#', 0, NULL, NULL, NULL, 1)";
$statement[] = "INSERT INTO core_data_parameter_fields (id, name, min_value, max_value, measuring_unit_id, measuring_unit_exponent, measuring_unit_ratio_id) VALUES (19, 'EO#', 0, NULL, NULL, NULL, 1)";
$statement[] = "INSERT INTO core_data_parameter_fields (id, name, min_value, max_value, measuring_unit_id, measuring_unit_exponent, measuring_unit_ratio_id) VALUES (20, 'BA#', 0, NULL, NULL, NULL, 1)";
$statement[] = "SELECT pg_catalog.setval('core_data_parameter_fields_id_seq', 21, true);";

$statement[] = "INSERT INTO core_data_parameter_limits (id, name) VALUES (1, 'First Limit')";
$statement[] = "INSERT INTO core_data_parameter_limits (id, name) VALUES (2, 'First Limit')";
$statement[] = "SELECT pg_catalog.setval('core_data_parameter_limits_id_seq', 3, true);";

$statement[] = "INSERT INTO core_data_parameter_field_limits (parameter_limit_id, parameter_field_id, upper_specification_limit, lower_specification_limit) VALUES (1 , 1, 11.1, 3.9)";
$statement[] = "INSERT INTO core_data_parameter_field_limits (parameter_limit_id, parameter_field_id, upper_specification_limit, lower_specification_limit) VALUES (1 , 2, 5.7, 4.2)";
$statement[] = "INSERT INTO core_data_parameter_field_limits (parameter_limit_id, parameter_field_id, upper_specification_limit, lower_specification_limit) VALUES (1 , 3, 16.9, 13.2)";
$statement[] = "INSERT INTO core_data_parameter_field_limits (parameter_limit_id, parameter_field_id, upper_specification_limit, lower_specification_limit) VALUES (1 , 4, 49, 38.5)";
$statement[] = "INSERT INTO core_data_parameter_field_limits (parameter_limit_id, parameter_field_id, upper_specification_limit, lower_specification_limit) VALUES (1 , 5, 97, 80)";
$statement[] = "INSERT INTO core_data_parameter_field_limits (parameter_limit_id, parameter_field_id, upper_specification_limit, lower_specification_limit) VALUES (1 , 6, 33.5, 27.5)";
$statement[] = "INSERT INTO core_data_parameter_field_limits (parameter_limit_id, parameter_field_id, upper_specification_limit, lower_specification_limit) VALUES (1 , 7, 36, 32)";
$statement[] = "INSERT INTO core_data_parameter_field_limits (parameter_limit_id, parameter_field_id, upper_specification_limit, lower_specification_limit) VALUES (1 , 8, 15, 11)";
$statement[] = "INSERT INTO core_data_parameter_field_limits (parameter_limit_id, parameter_field_id, upper_specification_limit, lower_specification_limit) VALUES (1 , 9, 390, 140)";
$statement[] = "INSERT INTO core_data_parameter_field_limits (parameter_limit_id, parameter_field_id, upper_specification_limit, lower_specification_limit) VALUES (1 , 10, 11.5, 7.5)";
$statement[] = "INSERT INTO core_data_parameter_field_limits (parameter_limit_id, parameter_field_id, upper_specification_limit, lower_specification_limit) VALUES (2 , 11, 80, 38)";
$statement[] = "INSERT INTO core_data_parameter_field_limits (parameter_limit_id, parameter_field_id, upper_specification_limit, lower_specification_limit) VALUES (2 , 12, 49, 15)";
$statement[] = "INSERT INTO core_data_parameter_field_limits (parameter_limit_id, parameter_field_id, upper_specification_limit, lower_specification_limit) VALUES (2 , 13, 13, 0)";
$statement[] = "INSERT INTO core_data_parameter_field_limits (parameter_limit_id, parameter_field_id, upper_specification_limit, lower_specification_limit) VALUES (2 , 14, 8, 0)";
$statement[] = "INSERT INTO core_data_parameter_field_limits (parameter_limit_id, parameter_field_id, upper_specification_limit, lower_specification_limit) VALUES (2 , 15, 2, 0)";
$statement[] = "INSERT INTO core_data_parameter_field_limits (parameter_limit_id, parameter_field_id, upper_specification_limit, lower_specification_limit) VALUES (2 , 16, 8, 1.6)";
$statement[] = "INSERT INTO core_data_parameter_field_limits (parameter_limit_id, parameter_field_id, upper_specification_limit, lower_specification_limit) VALUES (2 , 17, 3.5, 1)";
$statement[] = "INSERT INTO core_data_parameter_field_limits (parameter_limit_id, parameter_field_id, upper_specification_limit, lower_specification_limit) VALUES (2 , 18, 0.9, 0.04)";
$statement[] = "INSERT INTO core_data_parameter_field_limits (parameter_limit_id, parameter_field_id, upper_specification_limit, lower_specification_limit) VALUES (2 , 19, 0.6, 0.03)";
$statement[] = "INSERT INTO core_data_parameter_field_limits (parameter_limit_id, parameter_field_id, upper_specification_limit, lower_specification_limit) VALUES (2 , 20, 0.125, 0)";

$statement[] = "INSERT INTO core_data_parameter_template_has_fields (template_id, parameter_field_id, position) VALUES (1 , 1, NULL)";
$statement[] = "INSERT INTO core_data_parameter_template_has_fields (template_id, parameter_field_id, position) VALUES (1 , 2, NULL)";
$statement[] = "INSERT INTO core_data_parameter_template_has_fields (template_id, parameter_field_id, position) VALUES (1 , 3, NULL)";
$statement[] = "INSERT INTO core_data_parameter_template_has_fields (template_id, parameter_field_id, position) VALUES (1 , 4, NULL)";
$statement[] = "INSERT INTO core_data_parameter_template_has_fields (template_id, parameter_field_id, position) VALUES (1 , 5, NULL)";
$statement[] = "INSERT INTO core_data_parameter_template_has_fields (template_id, parameter_field_id, position) VALUES (1 , 6, NULL)";
$statement[] = "INSERT INTO core_data_parameter_template_has_fields (template_id, parameter_field_id, position) VALUES (1 , 7, NULL)";
$statement[] = "INSERT INTO core_data_parameter_template_has_fields (template_id, parameter_field_id, position) VALUES (1 , 8, NULL)";
$statement[] = "INSERT INTO core_data_parameter_template_has_fields (template_id, parameter_field_id, position) VALUES (1 , 9, NULL)";
$statement[] = "INSERT INTO core_data_parameter_template_has_fields (template_id, parameter_field_id, position) VALUES (1 , 10, NULL)";
$statement[] = "INSERT INTO core_data_parameter_template_has_fields (template_id, parameter_field_id, position) VALUES (2 , 11, NULL)";
$statement[] = "INSERT INTO core_data_parameter_template_has_fields (template_id, parameter_field_id, position) VALUES (2 , 12, NULL)";
$statement[] = "INSERT INTO core_data_parameter_template_has_fields (template_id, parameter_field_id, position) VALUES (2 , 13, NULL)";
$statement[] = "INSERT INTO core_data_parameter_template_has_fields (template_id, parameter_field_id, position) VALUES (2 , 14, NULL)";
$statement[] = "INSERT INTO core_data_parameter_template_has_fields (template_id, parameter_field_id, position) VALUES (2 , 15, NULL)";
$statement[] = "INSERT INTO core_data_parameter_template_has_fields (template_id, parameter_field_id, position) VALUES (2 , 16, NULL)";
$statement[] = "INSERT INTO core_data_parameter_template_has_fields (template_id, parameter_field_id, position) VALUES (2 , 17, NULL)";
$statement[] = "INSERT INTO core_data_parameter_template_has_fields (template_id, parameter_field_id, position) VALUES (2 , 18, NULL)";
$statement[] = "INSERT INTO core_data_parameter_template_has_fields (template_id, parameter_field_id, position) VALUES (2 , 19, NULL)";
$statement[] = "INSERT INTO core_data_parameter_template_has_fields (template_id, parameter_field_id, position) VALUES (2 , 20, NULL)";

$statement[] = "INSERT INTO core_data_parameter_methods (id, name) VALUES (1, 'Counting')";
$statement[] = "SELECT pg_catalog.setval('core_data_parameter_methods_id_seq', 3, true);";
?>