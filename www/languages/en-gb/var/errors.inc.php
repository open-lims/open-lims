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


	$error = array();
	
	// Base
	
	$error[1][40][1][10]	= "System-Log-ID Not Found!";
	$error[1][40][1][11]	= "System-Log-IP Not Found!";
	$error[1][40][1][12]	= "System-Message Not Found!";
	
	$error[1][40][2][1]		= "Access Denied!";
	
	$error[1][40][3][10]	= "System-Log-ID is missing!";
	$error[1][40][3][11]	= "System-Log-IP is missing!";
	$error[1][40][3][12]	= "System-Message-ID is missing!";
	
	
	// DB
	
	$error[2][10][1][1]		= "Database connection failed!";
	$error[2][10][1][2]		= "Database query failed!";
	
	
	// User
	
	$error[3][30][1][0]		= "Undefined User BL Error!";
	$error[3][30][1][1]		= "An error occours during user creation!";
	$error[3][30][1][2]		= "User already exists!";
	$error[3][30][1][3]		= "An error occours during group creation!";
	$error[3][30][1][4]		= "Group already exists!";
	
	$error[3][40][1][1]		= "User Not Found!";
	$error[3][40][1][2]		= "User cannot deleted! Existing Dependencies!";
	$error[3][40][1][3]		= "Group Not Found!";
	$error[3][40][1][4]		= "Group cannot deleted! Existing Dependencies!";
	
	$error[3][40][3][1]		= "User-ID is missing!";
	$error[3][40][3][2]		= "Group-ID is missing!";
	
	// Data
	
	$error[20][40][1][1]	= "Folder Not Found!";
	$error[20][40][1][2]	= "File Not Found!";
	$error[20][40][1][3]	= "Value Not Found!";
	$error[20][40][1][4]	= "This folder does not contain images!";
	$error[20][40][1][5]	= "File-Version Not Found!";
	$error[20][40][1][6]	= "Value-Version Not Found!";
	$error[20][40][1][7]	= "Value-Type Not Found!";
	
	$error[20][40][2][1]	= "Folder Access Denied!";
	$error[20][40][2][2]	= "File Access Denied!";
	$error[20][40][2][3]	= "Value Access Denied!";
	
	$error[20][40][3][0]	= "An ID is missing!";
	$error[20][40][3][1]	= "Folder-ID is missing!";
	$error[20][40][3][2]	= "File-ID is missing!";
	$error[20][40][3][3]	= "Value-ID is missing!";
	$error[20][40][3][4]	= "Project-ID is missing!";
	$error[20][40][3][5]	= "Sample-ID is missing!";
	$error[20][40][3][6]	= "Value-Type-ID is missing!";
	
	
	// Item
	
	$error[30][40][3][1]	= "Folder-ID is missing!";
	
	
	// Organisation-Unit
	
	$error[40][30][1][0]	= "Undefined Organisation-Unit BL Error!";
	$error[40][30][1][1]	= "An error occours during organisation-unit creation!";
	$error[40][30][1][2]	= "Organisation-Unit already exists!";
	
	$error[40][40][1][1]	= "Organisation-Unit Not Found!";
	$error[40][40][1][2]	= "Organisation-Unit cannot deleted! Existing Dependencies!";
	
	$error[40][40][3][1]	= "Organisation-Unit-ID is missing!";
	
	// Method
	
	$error[50][40][1][1]	= "Project Not Found!";
	$error[50][40][1][2]	= "Method Not Found!";
	$error[50][40][1][3]	= "Sample Not Found!";
	$error[50][40][1][4]	= "Method-Category Not Found!";
	$error[50][40][1][5]	= "Method-Type Not Found!";
	
	$error[50][40][3][1]	= "Project-ID is missing!";
	$error[50][40][3][2]	= "Method-ID is missing!";
	$error[50][40][3][3]	= "Sample-ID is missing!";
	$error[50][40][3][4]	= "Method-Category-ID is missing!";
	$error[50][40][3][5]	= "Method-Type-ID is missing!";
	
	// Location
	$error[60][40][1][1]	= "Location Not Found!";
	$error[60][40][3][1]	= "Location-ID is missing!";
	
	
	// Project
	
	$error[200][30][1][0]	= "Undefined Project BL Error!";
	$error[200][30][1][1]	= "An error occours during project creation!";
	
	$error[200][30][2][1]	= "Project Access Denied (BL)!";
	
	$error[200][40][1][1]	= "Project Not Found!";
	$error[200][40][1][2]	= "Project-Status Not Found!";
	$error[200][40][1][3]	= "Project-Template Not Found!";
	$error[200][40][1][4]	= "Project-Template-Category Not Found!";
	
	$error[200][40][2][1]	= "Project Access Denied!";
	
	$error[200][40][3][0]	= "An arguement is missing!";
	$error[200][40][3][1]	= "Project-ID is missing!";
	$error[200][40][3][2]	= "Project-Status-ID is missing!";
	$error[200][40][3][3]	= "Project-Template-ID is missing!";
	$error[200][40][3][4]	= "Project-Template-Category-ID is missing!";
	
	// Sample

	$error[250][30][1][1]	= "An error occours during sample creation!";
	$error[250][30][2][1]	= "Sample Access Denied (BL)!";
	
	$error[250][40][1][1]	= "Sample Not Found!";
	$error[250][40][1][2]	= "Location Not Found!";
	$error[250][40][1][3]	= "Sample-Template Not Found!";
	$error[250][40][1][4]	= "Sample-Template-Category Not Found!";
	
	$error[250][40][2][1]	= "Sample Access Denied!";

	$error[250][40][3][0]	= "An arguement is missing!";
	$error[250][40][3][1]	= "Sample-ID is missing!";
	$error[250][40][3][2]	= "Project-ID is missing!";
	$error[250][40][3][3]	= "Sample-Template-ID is missing!";
	$error[250][40][3][4]	= "Location-ID is missing!";
	$error[250][40][3][5]	= "Sample-Template-Category-ID is missing!";

?>
