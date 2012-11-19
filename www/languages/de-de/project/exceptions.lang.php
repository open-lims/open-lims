<?php
/**
 * @package project
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
$LANG_EXCEPTION['ProjectException'] 												= "Ein Projekt-Fehler !";

$LANG_EXCEPTION['ProjectIDMissingException'] 										= "Projekt-ID fehlt!";
$LANG_EXCEPTION['ProjectNotFoundException'] 										= "Das angegebene Projekt konnte nicht gefunden werden!";
$LANG_EXCEPTION['ProjectSetNextStatusException'] 									= "Nächster Projekt-Status konnte nicht gesetzt werden!";
$LANG_EXCEPTION['ProjectUserSetQuotaException'] 									= "Nutzerkontingent konnte nicht gesetzt werden!";

$LANG_EXCEPTION['ProjectCreateException'] 											= "Projekt konnte nicht erstellt werden!";
$LANG_EXCEPTION['ProjectCreateProjectExistsException'] 								= "Projekt existiert bereits!";
$LANG_EXCEPTION['ProjectCreateStatusException'] 									= "Projekt Status konnte nicht erstellt werden!";
$LANG_EXCEPTION['ProjectCreateFolderException'] 									= "Projekt Ordner konnte nicht erstellt werden!";
$LANG_EXCEPTION['ProjectCreateStatusFolderException'] 								= "Projekt Status Ordner konnte nicht erstellt werden!";
$LANG_EXCEPTION['ProjectCreateStatusSubFolderException'] 							= "Projekt Status Unterordner konnte nicht erstellt werden!";
$LANG_EXCEPTION['ProjectCreateSupplementaryFolderException'] 						= "Projekt Anhang-Ordner konnte nicht erstellt werden!";
$LANG_EXCEPTION['ProjectCreateDescriptionException'] 								= "Projektbeschreibung konnte nicht erstellt werden!";
$LANG_EXCEPTION['ProjectCreateMasterDataException'] 								= "Projekt 'master-data' konnte nicht erstellt werden!";
$LANG_EXCEPTION['ProjectCreatePermissionUserException'] 							= "Eigentümer-Recht konnten nicht erstellt werden!";
$LANG_EXCEPTION['ProjectCreatePermissionLeaderException'] 							= "Guppen-Leiter-Rechte konnten nicht erstellt werden!";
$LANG_EXCEPTION['ProjectCreatePermissionGroupException'] 							= "Gruppen-Rechte konnte nicht erstellt werden!";
$LANG_EXCEPTION['ProjectCreatePermissionOrganisationUnitException'] 				= "Organisationseinheits-Rechte konnten nicht erstellt werden!";
$LANG_EXCEPTION['ProjectCreatePermissionQualityManagerException'] 					= "Qualitäts-Manager-Rechte konnten nicht erstellt werden!";

$LANG_EXCEPTION['ProjectDeleteException'] 											= "Projekt konnte nicht gelöscht werden!";
$LANG_EXCEPTION['ProjectDeleteContainsSubProjectsException'] 						= "Löschen fehlgeschlagen - Projekt enthält noch Unterprojekte!";
$LANG_EXCEPTION['ProjectDeleteFolderException'] 									= "Projekt-Ordner konnte nicht gelöscht werden!";
$LANG_EXCEPTION['ProjectDeleteItemException'] 										= "Projekt-'Items' konnten nicht gelöscht werden!";
$LANG_EXCEPTION['ProjectDeleteLinkException'] 										= "Projekt-Links konnten nicht gelöscht werden!";
$LANG_EXCEPTION['ProjectDeleteLogException'] 										= "Projekt-Log konnte nicht gelöscht werden!";
$LANG_EXCEPTION['ProjectDeletePermissionException'] 								= "Projekt-Rechte konnten nicht gelöscht werden!";
$LANG_EXCEPTION['ProjectDeleteStatusException'] 									= "Projekt-Status konnte(n) nicht gelöscht werden!";
$LANG_EXCEPTION['ProjectDeleteTaskException'] 										= "Projekt-Aufgaben konnten nicht gelöscht werden!";

$LANG_EXCEPTION['ProjectMoveException'] 											= "Projekt konnte nicht verschoben werden!";
$LANG_EXCEPTION['ProjectMoveProjectExistsException'] 								= "Projekt konnte nicht verschoben werden - Das Projekt existiert bereits!";
$LANG_EXCEPTION['ProjectMovePermissionException'] 									= "Projekt-Recht konnten nicht gesetzt werden!";
$LANG_EXCEPTION['ProjectMoveFolderException'] 										= "Projekt-Ordner konnte nicht verschoben werden!";

$LANG_EXCEPTION['ProjectSecurityException'] 										= "Eine Sicherheitsverletzung ist aufgetreten!";
$LANG_EXCEPTION['ProjectAccessDeniedException'] 									= "Projektzugriff verweigert!";
$LANG_EXCEPTION['ProjectChangeException'] 											= "Projekt-Rechte konnten nicht gesetzt werden!";

$LANG_EXCEPTION['ProjectItemException'] 											= "Ein Projekt-'Item' Fehler ist aufgetreten!";
$LANG_EXCEPTION['ProjectItemLinkException'] 										= "'Item' konnte nicht an das Projekt gebunden werden!";
$LANG_EXCEPTION['ProjectItemUnlinkException'] 										= "'Item' konnte nicht vom Projekt gelößt werden!";
$LANG_EXCEPTION['ProjectItemNotFoundException'] 									= "Das angefragte Projekt-'Item' konnte nicht gefunden werden!";

$LANG_EXCEPTION['ProjectLogException'] 												= "Ein Projekt-Log Fehler ist aufgetreten!";
$LANG_EXCEPTION['ProjectLogCreateException'] 										= "Das Projekt-Log konnte nicht erzeugt werden!";
$LANG_EXCEPTION['ProjectLogDeleteException'] 										= "Das Projekt-Log konnte nicht gelöscht werden!";
$LANG_EXCEPTION['ProjectLogNotFoundException'] 										= "Das Projekt-Log wurde nicht gefunden!";
$LANG_EXCEPTION['ProjectLogIDMissingException'] 									= "Die Projekt-Log ID fehlt!";
$LANG_EXCEPTION['ProjectLogItemLinkException'] 										= "Ein 'Item' konnte nicht an das Projekt-Log gebunden werden!";

$LANG_EXCEPTION['ProjectPermissionException'] 										= "Ein Projekt-Rechte Fehler ist aufgetreten!";
$LANG_EXCEPTION['ProjectPermissionUserException'] 									= "Ein Nutzer-Projekt-Rechte Fehler ist aufgetreten!";
$LANG_EXCEPTION['ProjectPermissionUserCreateException'] 							= "Nutzerberechtigung konnte nicht erstellt werden!";
$LANG_EXCEPTION['ProjectPermissionUserCreateVirtualFolderException'] 				= "Der virtuelle Ordner konnte nicht erstellt werden!";
$LANG_EXCEPTION['ProjectPermissionUserDeleteException'] 							= "Nutzerberechtigung konnte nicht gelöscht werden!";
$LANG_EXCEPTION['ProjectPermissionUserDeleteVirtualFolderException'] 				= "Der virtuelle Ordner konnte nicht gelösct werden!";
$LANG_EXCEPTION['ProjectPermissionOrganisationUnitException'] 						= "Ein Organisationseinheiten-Projekt-Rechte Fehler ist aufgetreten!";
$LANG_EXCEPTION['ProjectPermissionOrganisationUnitCreateException'] 				= "Organisationseinheitenberechtigung konnte nicht erstellt werden!";
$LANG_EXCEPTION['ProjectPermissionOrganisationUnitCreateVirtualFolderException'] 	= "Der virtuelle Ordner konnte nicht erstellt werden!";
$LANG_EXCEPTION['ProjectPermissionOrganisationUnitDeleteException'] 				= "Organisationseinheitenberechtigung konnte nicht gelöscht werden!";
$LANG_EXCEPTION['ProjectPermissionOrganisationUnitDeleteVirtualFolderException'] 	= "Der virtuelle Ordner konnte nicht gelösct werden!";
$LANG_EXCEPTION['ProjectPermissionGroupException'] 									= "Ein Gruppen-Projekt-Rechte Fehler ist aufgetreten!";
$LANG_EXCEPTION['ProjectPermissionGroupCreateException'] 							= "Gruppenberechtigung konnte nicht erstellt werden!";
$LANG_EXCEPTION['ProjectPermissionGroupCreateVirtualFolderException'] 				= "Der virtuelle Ordner konnte nicht erstellt werden!";
$LANG_EXCEPTION['ProjectPermissionGroupDeleteException'] 							= "Gruppenberechtigugn konnte nicht gelöscht werden!";
$LANG_EXCEPTION['ProjectPermissionGroupDeleteVirtualFolderException'] 				= "Der virtuelle Ordner konnte nicht gelösct werden!";
$LANG_EXCEPTION['ProjectPermissionNotFoundException'] 								= "Projekt-Recht wurde nicht gefunden!";
$LANG_EXCEPTION['ProjectPermissionIDMissingException'] 								= "Die Projekt-Rechte-ID fehlt!";

$LANG_EXCEPTION['ProjectSecurityException'] 										= "Projekt Sicherheitsverletzung!";
$LANG_EXCEPTION['ProjectSecurityChangeException'] 									= "Projekt Sicherheitsänderung fehlgeschlagen!";
$LANG_EXCEPTION['ProjectSecurityAccessDeniedException'] 							= "Projekt-Zugriff verweigert!";

$LANG_EXCEPTION['ProjectStatusException'] 											= "Ein Projekt-Status Fehler ist aufgetreten!";
$LANG_EXCEPTION['ProjectStatusCreateException'] 									= "Der Projekt-Status konnte nicht erstellt werden!";
$LANG_EXCEPTION['ProjectStatusDeleteException'] 									= "Der Projekt-Status konnte nicht gelöscht werden!";
$LANG_EXCEPTION['ProjectStatusNotFoundException'] 									= "Projekt Status wurde nicht gefunden!";
$LANG_EXCEPTION['ProjectStatusIDMissingException'] 									= "Die Projekt-Status-ID fehlt!";

$LANG_EXCEPTION['ProjectTaskException'] 											= "Ein Projektaufgaben bezogener Fehler ist aufgetreten!";
$LANG_EXCEPTION['ProjectTaskCreateException'] 										= "Die Projektaufgabe konnte nicht erstellt werden!";
$LANG_EXCEPTION['ProjectTaskCreateAttachException'] 								= "Die Projektaufgabe konnte nicht angehangen werden!";
$LANG_EXCEPTION['ProjectTaskDeleteException'] 										= "Die Projektaufgabe konnte nicht gelöscht werden!";
$LANG_EXCEPTION['ProjectTaskNotFoundException'] 									= "Projektaufgabe wurde nicht gefunden!";
$LANG_EXCEPTION['ProjectTaskIDMissingException'] 									= "Die Projektaufgaben-ID fehlt!";

$LANG_EXCEPTION['ProjectTemplateException'] 										= "Ein Projekt-Template Fehler ist aufgetreten!";
$LANG_EXCEPTION['ProjectTemplateCreateException'] 									= "Projekt-Template konnte nicht erzeugt werden!";
$LANG_EXCEPTION['ProjectTemplateCreateOLDLNotFoundException'] 						= "Die OLDL Datei nicht gefunden!";
$LANG_EXCEPTION['ProjectTemplateCreateOLDLCreateException'] 						= "Der OLDL Eintrag konnte nicht erzeugt werden!";
$LANG_EXCEPTION['ProjectTemplateDeleteException'] 									= "Das Projekt-Template konnte nicht gelöscht werden!";
$LANG_EXCEPTION['ProjectTemplateDeleteInUseException'] 								= "Das Projekt-Template ist noch in verwendung!";
$LANG_EXCEPTION['ProjectTemplateDeleteOLDLDeleteException'] 						= "Der OLDL Eintrag konnte nicht gelöscht werden!";
$LANG_EXCEPTION['ProjectTemplateNotFoundException'] 								= "Projekt-Template nicht gefunden!";
$LANG_EXCEPTION['ProjectTemplateIDMissingException'] 								= "Die Projekt-Template ID fehlt!";
$LANG_EXCEPTION['ProjectTemplateCategoryCreateException'] 							= "Die Projekt-Template-Kategorie konnte nicht erzeugt werden!";
$LANG_EXCEPTION['ProjectTemplateCategoryDeleteException'] 							= "Die Projekt-Template-Kategorie konnte nicht gelöscht werden!";
$LANG_EXCEPTION['ProjectTemplateCategoryNotFoundException'] 						= "Projekt-Template-Kategorie wurde nicht gefunden!";
$LANG_EXCEPTION['ProjectTemplateCategoryIDMissingException'] 						= "Die Projekt-Template-Kaegorie-ID fehlt!";

?>
