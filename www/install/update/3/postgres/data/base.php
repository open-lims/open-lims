<?php
/**
 * @package install
 * @version 0.4.0.0
 * @author Roman Konertz <konertz@open-lims.org>
 * @copyright (c) 2008-2016 by Roman Konertz
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

$statement[] = "UPDATE core_base_includes SET db_version = '0.4.0.0' WHERE name='base'";

$statement[] = "INSERT INTO core_base_include_tables (id,include,table_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'base','core_base_measuring_unit_categories', NULL);";
$statement[] = "INSERT INTO core_base_include_tables (id,include,table_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'base','core_base_measuring_unit_ratios', NULL);";
$statement[] = "INSERT INTO core_base_include_tables (id,include,table_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'base','core_base_measuring_units', NULL);";
$statement[] = "INSERT INTO core_base_include_tables (id,include,table_name,db_version) VALUES (nextval('core_base_include_tables_id_seq'::regclass), 'base','core_countries', NULL);";


// Measuring Unit Categories
$statement[] = "INSERT INTO core_base_measuring_unit_categories (id, name, created_by_user) VALUES (1, 'Lenght', 'f');";
$statement[] = "INSERT INTO core_base_measuring_unit_categories (id, name, created_by_user) VALUES (2, 'Mass', 'f');";
$statement[] = "INSERT INTO core_base_measuring_unit_categories (id, name, created_by_user) VALUES (3, 'Time', 'f');";
$statement[] = "INSERT INTO core_base_measuring_unit_categories (id, name, created_by_user) VALUES (4, 'Electric Current', 'f');";
$statement[] = "INSERT INTO core_base_measuring_unit_categories (id, name, created_by_user) VALUES (5, 'Temperature', 'f');";
$statement[] = "INSERT INTO core_base_measuring_unit_categories (id, name, created_by_user) VALUES (6, 'Amount of Substance', 'f');";
$statement[] = "INSERT INTO core_base_measuring_unit_categories (id, name, created_by_user) VALUES (7, 'Luminous Intensity', 'f');";
$statement[] = "INSERT INTO core_base_measuring_unit_categories (id, name, created_by_user) VALUES (8, 'Angle', 'f');";
$statement[] = "INSERT INTO core_base_measuring_unit_categories (id, name, created_by_user) VALUES (9, 'Area', 'f');";
$statement[] = "INSERT INTO core_base_measuring_unit_categories (id, name, created_by_user) VALUES (10, 'Volume', 'f');";
$statement[] = "INSERT INTO core_base_measuring_unit_categories (id, name, created_by_user) VALUES (11, 'Force', 'f');";
$statement[] = "INSERT INTO core_base_measuring_unit_categories (id, name, created_by_user) VALUES (12, 'Pressure', 'f');";
$statement[] = "INSERT INTO core_base_measuring_unit_categories (id, name, created_by_user) VALUES (13, 'Voltage', 'f');";
$statement[] = "INSERT INTO core_base_measuring_unit_categories (id, name, created_by_user) VALUES (14, 'Electric Resistance', 'f');";
$statement[] = "INSERT INTO core_base_measuring_unit_categories (id, name, created_by_user) VALUES (15, 'Electric Conductance', 'f');";
$statement[] = "INSERT INTO core_base_measuring_unit_categories (id, name, created_by_user) VALUES (16, 'Electric Charge', 'f');";
$statement[] = "INSERT INTO core_base_measuring_unit_categories (id, name, created_by_user) VALUES (17, 'Electric Capacitance', 'f');";
$statement[] = "INSERT INTO core_base_measuring_unit_categories (id, name, created_by_user) VALUES (18, 'Frequency', 'f');";
$statement[] = "INSERT INTO core_base_measuring_unit_categories (id, name, created_by_user) VALUES (19, 'Amount', 'f');";
$statement[] = "INSERT INTO core_base_measuring_unit_categories (id, name, created_by_user) VALUES (20, 'Percent/Per Mill', 'f');";
$statement[] = "SELECT pg_catalog.setval('core_base_measuring_unit_categories_id_seq', 21, true);";

// Measuring Units
$statement[] = "INSERT INTO core_base_measuring_units (id, base_id, category_id, name, unit_symbol, min_value, max_value, min_prefix_exponent, max_prefix_exponent, prefix_calculation_exponent, calculation, type, created_by_user) VALUES (2, NULL, 1, 'meter', 'm', NULL, NULL, 12, 3, 1, 'B', 'metric', 'f');";
$statement[] = "INSERT INTO core_base_measuring_units (id, base_id, category_id, name, unit_symbol, min_value, max_value, min_prefix_exponent, max_prefix_exponent, prefix_calculation_exponent, calculation, type, created_by_user) VALUES (4, 2, 1, 'foot', 'ft', NULL, NULL, NULL, NULL, 1, 'B[div]0.3048', 'aa', 'f');";
$statement[] = "INSERT INTO core_base_measuring_units (id, base_id, category_id, name, unit_symbol, min_value, max_value, min_prefix_exponent, max_prefix_exponent, prefix_calculation_exponent, calculation, type, created_by_user) VALUES (5, 2, 1, 'yard', 'yd', NULL, NULL, NULL, NULL, 1, 'B[div]0.9144', 'aa', 'f');";
$statement[] = "INSERT INTO core_base_measuring_units (id, base_id, category_id, name, unit_symbol, min_value, max_value, min_prefix_exponent, max_prefix_exponent, prefix_calculation_exponent, calculation, type, created_by_user) VALUES (6, 2, 1, 'mile', 'Mi', NULL, NULL, NULL, NULL, 1, 'B[div]1609.344', 'aa', 'f');";
$statement[] = "INSERT INTO core_base_measuring_units (id, base_id, category_id, name, unit_symbol, min_value, max_value, min_prefix_exponent, max_prefix_exponent, prefix_calculation_exponent, calculation, type, created_by_user) VALUES (3, 2, 1, 'inch', 'in', NULL, NULL, NULL, NULL, 1, 'B[div]0.0254', 'aa', 'f');";
$statement[] = "INSERT INTO core_base_measuring_units (id, base_id, category_id, name, unit_symbol, min_value, max_value, min_prefix_exponent, max_prefix_exponent, prefix_calculation_exponent, calculation, type, created_by_user) VALUES (1, NULL, NULL, 'pH-Value', 'pH (-lg(aH))', 0, 14, NULL, NULL, NULL, NULL, NULL, 'f');";
$statement[] = "INSERT INTO core_base_measuring_units (id, base_id, category_id, name, unit_symbol, min_value, max_value, min_prefix_exponent, max_prefix_exponent, prefix_calculation_exponent, calculation, type, created_by_user) VALUES (7, NULL, 2, 'gramm', 'g', NULL, NULL, 12, 3, NULL, 'B', 'metric', 'f');";
$statement[] = "INSERT INTO core_base_measuring_units (id, base_id, category_id, name, unit_symbol, min_value, max_value, min_prefix_exponent, max_prefix_exponent, prefix_calculation_exponent, calculation, type, created_by_user) VALUES (8, NULL, 3, 'second', 's', NULL, NULL, 12, NULL, NULL, 'B', NULL, 'f');";
$statement[] = "INSERT INTO core_base_measuring_units (id, base_id, category_id, name, unit_symbol, min_value, max_value, min_prefix_exponent, max_prefix_exponent, prefix_calculation_exponent, calculation, type, created_by_user) VALUES (10, NULL, 5, 'kelvin', 'K', NULL, NULL, NULL, NULL, NULL, 'B', NULL, 'f');";
$statement[] = "INSERT INTO core_base_measuring_units (id, base_id, category_id, name, unit_symbol, min_value, max_value, min_prefix_exponent, max_prefix_exponent, prefix_calculation_exponent, calculation, type, created_by_user) VALUES (13, NULL, 8, 'radiant', 'rad', NULL, NULL, NULL, NULL, NULL, 'B', NULL, 'f');";
$statement[] = "INSERT INTO core_base_measuring_units (id, base_id, category_id, name, unit_symbol, min_value, max_value, min_prefix_exponent, max_prefix_exponent, prefix_calculation_exponent, calculation, type, created_by_user) VALUES (17, NULL, 12, 'pascal', 'Pa', NULL, NULL, 12, 3, NULL, 'B', 'metric', 'f');";
$statement[] = "INSERT INTO core_base_measuring_units (id, base_id, category_id, name, unit_symbol, min_value, max_value, min_prefix_exponent, max_prefix_exponent, prefix_calculation_exponent, calculation, type, created_by_user) VALUES (18, NULL, 13, 'volt', 'V', NULL, NULL, 12, 3, NULL, 'B', NULL, 'f');";
$statement[] = "INSERT INTO core_base_measuring_units (id, base_id, category_id, name, unit_symbol, min_value, max_value, min_prefix_exponent, max_prefix_exponent, prefix_calculation_exponent, calculation, type, created_by_user) VALUES (20, NULL, 15, 'siemens', 'S', NULL, NULL, NULL, NULL, NULL, 'B', NULL, 'f');";
$statement[] = "INSERT INTO core_base_measuring_units (id, base_id, category_id, name, unit_symbol, min_value, max_value, min_prefix_exponent, max_prefix_exponent, prefix_calculation_exponent, calculation, type, created_by_user) VALUES (21, NULL, 16, 'coulomb', 'C', NULL, NULL, NULL, NULL, NULL, 'B', NULL, 'f');";
$statement[] = "INSERT INTO core_base_measuring_units (id, base_id, category_id, name, unit_symbol, min_value, max_value, min_prefix_exponent, max_prefix_exponent, prefix_calculation_exponent, calculation, type, created_by_user) VALUES (22, NULL, 17, 'farad', 'F', NULL, NULL, 12, 9, NULL, 'B', NULL, 'f');";
$statement[] = "INSERT INTO core_base_measuring_units (id, base_id, category_id, name, unit_symbol, min_value, max_value, min_prefix_exponent, max_prefix_exponent, prefix_calculation_exponent, calculation, type, created_by_user) VALUES (23, NULL, 18, 'hertz', 'Hz', NULL, NULL, 12, 12, NULL, 'B', NULL, 'f');";
$statement[] = "INSERT INTO core_base_measuring_units (id, base_id, category_id, name, unit_symbol, min_value, max_value, min_prefix_exponent, max_prefix_exponent, prefix_calculation_exponent, calculation, type, created_by_user) VALUES (9, NULL, 4, 'ampere', 'A', NULL, NULL, 12, 3, NULL, 'B', NULL, 'f');";
$statement[] = "INSERT INTO core_base_measuring_units (id, base_id, category_id, name, unit_symbol, min_value, max_value, min_prefix_exponent, max_prefix_exponent, prefix_calculation_exponent, calculation, type, created_by_user) VALUES (11, NULL, 6, 'mol', 'mol', NULL, NULL, 12, 3, NULL, 'B', NULL, 'f');";
$statement[] = "INSERT INTO core_base_measuring_units (id, base_id, category_id, name, unit_symbol, min_value, max_value, min_prefix_exponent, max_prefix_exponent, prefix_calculation_exponent, calculation, type, created_by_user) VALUES (12, NULL, 7, 'candela', 'cd', NULL, NULL, 12, 3, NULL, 'B', NULL, 'f');";
$statement[] = "INSERT INTO core_base_measuring_units (id, base_id, category_id, name, unit_symbol, min_value, max_value, min_prefix_exponent, max_prefix_exponent, prefix_calculation_exponent, calculation, type, created_by_user) VALUES (16, NULL, 11, 'newton', 'N', NULL, NULL, 12, 3, NULL, 'B', NULL, 'f');";
$statement[] = "INSERT INTO core_base_measuring_units (id, base_id, category_id, name, unit_symbol, min_value, max_value, min_prefix_exponent, max_prefix_exponent, prefix_calculation_exponent, calculation, type, created_by_user) VALUES (19, NULL, 14, 'ohm', '&Omega;', NULL, NULL, 12, 9, NULL, 'B', NULL, 'f');";
$statement[] = "INSERT INTO core_base_measuring_units (id, base_id, category_id, name, unit_symbol, min_value, max_value, min_prefix_exponent, max_prefix_exponent, prefix_calculation_exponent, calculation, type, created_by_user) VALUES (14, NULL, 9, 'square meter', 'm2', NULL, NULL, 12, 3, 2, 'B', 'metric', 'f');";
$statement[] = "INSERT INTO core_base_measuring_units (id, base_id, category_id, name, unit_symbol, min_value, max_value, min_prefix_exponent, max_prefix_exponent, prefix_calculation_exponent, calculation, type, created_by_user) VALUES (15, NULL, 10, 'cubic meter', 'm3', NULL, NULL, 12, 3, 3, 'B', 'metric', 'f');";
$statement[] = "INSERT INTO core_base_measuring_units (id, base_id, category_id, name, unit_symbol, min_value, max_value, min_prefix_exponent, max_prefix_exponent, prefix_calculation_exponent, calculation, type, created_by_user) VALUES (26, 15, 10, 'deciliter', 'dl', NULL, NULL, NULL, NULL, NULL, 'B[mul]10000', 'metric', 'f');";
$statement[] = "INSERT INTO core_base_measuring_units (id, base_id, category_id, name, unit_symbol, min_value, max_value, min_prefix_exponent, max_prefix_exponent, prefix_calculation_exponent, calculation, type, created_by_user) VALUES (24, 15, 10, 'liter', 'l', NULL, NULL, 18, NULL, NULL, 'B[mul]1000', 'metric', 'f');";
$statement[] = "INSERT INTO core_base_measuring_units (id, base_id, category_id, name, unit_symbol, min_value, max_value, min_prefix_exponent, max_prefix_exponent, prefix_calculation_exponent, calculation, type, created_by_user) VALUES (25, NULL, 19, 'amount', '#', NULL, NULL, NULL, NULL, NULL, 'B', NULL, 'f');";
$statement[] = "INSERT INTO core_base_measuring_units (id, base_id, category_id, name, unit_symbol, min_value, max_value, min_prefix_exponent, max_prefix_exponent, prefix_calculation_exponent, calculation, type, created_by_user) VALUES (27, NULL, 20, 'percent', '%', NULL, NULL, NULL, NULL, NULL, 'B', NULL, 'f');";
$statement[] = "INSERT INTO core_base_measuring_units (id, base_id, category_id, name, unit_symbol, min_value, max_value, min_prefix_exponent, max_prefix_exponent, prefix_calculation_exponent, calculation, type, created_by_user) VALUES (28, NULL, 20, 'per mill', '&#8240;', NULL, NULL, NULL, NULL, NULL, 'B[div]10', NULL, 'f');";
$statement[] = "INSERT INTO core_base_measuring_units (id, base_id, category_id, name, unit_symbol, min_value, max_value, min_prefix_exponent, max_prefix_exponent, prefix_calculation_exponent, calculation, type, created_by_user) VALUES (29, 25, 19, 'thousand', 'thous', NULL, NULL, NULL, NULL, NULL, 'B[mul]1000', NULL, 'f');";
$statement[] = "INSERT INTO core_base_measuring_units (id, base_id, category_id, name, unit_symbol, min_value, max_value, min_prefix_exponent, max_prefix_exponent, prefix_calculation_exponent, calculation, type, created_by_user) VALUES (30, 25, 19, 'million', 'mil', NULL, NULL, NULL, NULL, NULL, 'B[mul]1000000', NULL, 'f');";
$statement[] = "SELECT pg_catalog.setval('core_base_measuring_units_id_seq', 31, true);";

// Ratios
$statement[] = "INSERT INTO core_base_measuring_unit_ratios (id, numerator_unit_id, numerator_unit_exponent, denominator_unit_id, denominator_unit_exponent) VALUES (1, 29, NULL, 24, -6);";
$statement[] = "INSERT INTO core_base_measuring_unit_ratios (id, numerator_unit_id, numerator_unit_exponent, denominator_unit_id, denominator_unit_exponent) VALUES (2, 30, NULL, 24, -6);";
$statement[] = "INSERT INTO core_base_measuring_unit_ratios (id, numerator_unit_id, numerator_unit_exponent, denominator_unit_id, denominator_unit_exponent) VALUES (3, 7, NULL, 26, NULL);";
$statement[] = "SELECT pg_catalog.setval('core_base_measuring_unit_ratios_id_seq', 4, true);";

// Countries
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Afghanistan','Afghanestan','AF');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Egypt','Misr','EG');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Albania','Shqiperia','AL');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Algeria','Al Jaza''ir','DZ');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Andorra','Andorra','AD');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Angola','Angola','AO');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Antigua and Barbuda','Antigua and Barbuda','AG');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Equatorial Guinea','Guinea Ecuatorial','GQ');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Argentina','Argentina','AR');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Armenia','Hayastan','AM');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Azerbaijan','Azarbaycan','AZ');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Ethiopia','Ityop''iya','ET');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Australia','Australia','AU');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Bahamas','Bahamas','BS');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Bahrain','Al Bahrayn','BH');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Bangladesh','Bangladesh','BD');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Barbados','Barbados','BB');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Belgium','Belgique/Belgie','BE');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Belize','Belize','BZ');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Benin','Benin','BJ');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Bhutan','Bhutan','BT');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Bolivia','Bolivia','BO');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Bosnia and Herzegovina','Bosna i Hercegovina','BA');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Botswana','Botswana','BW');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Brazil','Brasil','BR');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Brunei Darussalam','Negara Brunei Darussalam','BN');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Bulgaria','Bulgaria','BG');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Burkina Faso','Burkina Faso','BF');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Burundi','Burundi','BI');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Chile','Chile','CL');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Taiwan (Republic of China)','T''ai-wan','TW');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'China','Zhong Guo','CN');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Cook Islands','Cook Islands','CK');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Costa Rica','Costa Rica','CR');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Denmark','Danmark','DK');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Germany','Deutschland','DE');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Dominica','Dominica','DM');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Dominican Republic','Dominicana, Republica','DO');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Djibouti','Djibouti','DJ');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Ecuador','Ecuador','EC');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'El Salvador','El Salvador','SV');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Ivory Coast','Cote d''Ivoire','CI');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Eritrea','Hagere Ertra','ER');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Estonia','Eesti Vabariik','EE');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Fiji','Fiji','FJ');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Finland','Suomen Tasavalta','FI');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'France','France','FR');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Gabon','Gabon','GA');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Gambia','The Gambia','GM');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Georgia','Sak''art''velo','GE');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Ghana','Ghana','GH');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Grenada','Grenada','GD');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Greece','Ellas or Ellada','GR');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Guatemala','Guatemala','GT');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Guinea','Guinee','GN');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Guinea-Bissau','Guine-Bissau','GW');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Guyana','Guyana','GY');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Haiti','Haiti','HT');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Honduras','Honduras','HN');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'India','Bharat','IN');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Indonesia','Indonesia','ID');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Iraq','Iraq','IQ');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Iran','Iran','IR');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Ireland','ire','IE');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Iceland','Lyoveldio Island','IS');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Israel','Yisra''el','IL');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Italy','Italia','IT');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Jamaica','Jamaica','JM');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Japan','Nippon, Nihon','JP');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Yemen','Al Yaman','YE');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Jordan','Al Urdun','JO');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Cambodia','Kampuchea','KH');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Cameroon','Cameroon','CM');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Canada','Canada','CA');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Cape Verde','Cabo Verde','CV');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Kazakhstan','Qazaqstan','KZ');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Qatar','Dawlat Qatar','QA');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Kenya','Kenya','KE');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Kyrgyzstan','Kyrgyz Respublikasy','KG');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Kiribati','Kiribati, (Kiribas)','KI');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Colombia','Colombia','CO');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Comoros','Comores','KM');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Democratic Republic of the Congo (Kinshasa)','Republique Democratique du Congo','CD');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Republic of Congo (Brazzaville)','Republique du Congo','CG');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'North Korea','Choson','KP');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'South Korea','Han-guk','KR');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Croatia','Hrvatska','HR');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Cuba','Cuba','CU');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Kuwait','Al Kuwayt','KW');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Lao','Lao','LA');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Lesotho','Lesotho','LS');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Latvia','Latvija','LV');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Lebanon','Lubnan','LB');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Liberia','Liberia','LR');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Libya','Libiyah','LY');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Liechtenstein','Liechtenstein','LI');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Lithuania','Lietuva','LT');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Luxembourg','Luxembourg/Letzebuerg','LU');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Madagascar','Madagascar','MG');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Malawi','Malawi','MW');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Malaysia','Malaysia','MY');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Maldives','Dhivehi Raajje','MV');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Mali','Mali','ML');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Malta','Malta','MT');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Morocco','Al Maghrib','MA');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Marshall Islands','Marshall Islands','MH');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Mauritania','Muritaniyah','MR');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Mauritius','Mauritius','MU');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Macedonia, Rep. of','Makedonija','MK');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Mexico','Estados Unidos Mexicanos','MX');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Micronesia','Micronesia','FM');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Moldova','Moldova','MD');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Monaco','Monaco','MC');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Mongolia','Mongol Uls','MN');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Montenegro','Crna Gora','ME');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Mozambique','Mocambique','MZ');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Myanmar, Burma','Myanma Naingngandaw','MM');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Namibia','Namibia','NA');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Nauru','Nauru','NR');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Nepal','Nepal','NP');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'New Zealand','Aotearoa','NZ');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Nicaragua','Nicaragua','NI');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Netherlands','Nederland','NL');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Niger','Niger','NE');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Nigeria','Nigeria','NG');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Niue','Niue','NU');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Norway','Norge','NO');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Oman','Saltanat Uman','OM');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Austria','™sterreich','AT');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'East Timor','Timor','TL');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Pakistan','Pakistan','PK');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Palestine','Dawlat Filastin','PS');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Palau','Belau','PW');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Panama','Panama','PA');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Papua New Guinea','Papua Niu Gini','PG');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Paraguay','Paraguay','PY');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Peru','Peru','PE');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Philippines','Pilipinas','PH');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Poland','Polska','PL');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Portugal','Portugal','PT');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Rwanda','Rwanda','RW');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Romania','Romania','RO');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Russian Federation','Rossiya','RU');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Solomon Islands','Solomon Islands','SB');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Zambia','Zambia','ZM');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Samoa','Samoa','WS');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'San Marino','San Marino','SM');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Sao Tome and Pr­ncipe','Sao Tome e Principe','ST');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Saudi Arabia','Al Arabiyah as Suudiyah','SA');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Sweden','Sverige','SE');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Switzerland','Schweiz/Suisse/Svizzera','CH');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Senegal','Senegal','SN');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Serbia','Srbija','RS');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Seychelles','Seychelles','SC');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Sierra Leone','Sierra Leone','SL');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Zimbabwe','Zimbabwe','ZW');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Singapore','Singapore','SG');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Slovakia','Slovensko','SK');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Slovenia','Slovenija','SI');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Somalia','Somalia','SO');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Spain','Espana','ES');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Sri Lanka','Sri Lanka','LK');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Saint Kitts and Nevis','Saint Kitts and Nevis','KN');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Saint Lucia','Saint Lucia','LC');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Saint Vincent and the Grenadines','Saint Vincent and the Grenadines','VC');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'South Africa','South Africa','ZA');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Sudan','As-Sudan','SD');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Suriname','Suriname','SR');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Swaziland','Swaziland','SZ');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Syria, Syrian Arab Republic','Suriyah','SY');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Tajikistan','Jumhurii Tojikiston','TJ');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Tanzania','Tanzania','TZ');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Thailand','Prathet Thai','TH');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Togo','Republique Togolaise','TG');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Tonga','Tonga','TO');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Trinidad and Tobago','Trinidad, Tobago','TT');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Chad','Tchad','TD');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Czech Republic','Ceska Republika','CZ');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Tunisia','Tunis','TN');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Turkey','Turkiye','TR');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Turkmenistan','Turkmenistan','TM');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Tuvalu','Tuvalu','TV');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Uganda','Uganda','UG');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Ukraine','Ukrayina','UA');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Hungary','Magyarorszag','HU');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Uruguay','Republica Oriental del Uruguay','UY');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Uzbekistan','Uzbekiston Respublikasi','UZ');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Vanuatu','Vanuatu','VU');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Vatican City','Status Civitatis Vatican','VA');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Venezuela','Venezuela','VE');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'United Arab Emirates','Al Imarat al Arabiyah al Muttahidah','AE');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'United States','United States','US');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'United Kingdom','United Kingdom','GB');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Vietnam','Viet Nam','VN');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Belarus','Byelarus','BY');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Western Sahara','Western Sahara','EH');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Central African Republic','Republique Centrafricaine','CF');";
$statement[] = "INSERT INTO core_countries (id, english_name, local_name, iso_3166) VALUES (nextval('core_countries_id_seq'::regclass), 'Cyprus','Kibris/Kypros','CY');";


?>