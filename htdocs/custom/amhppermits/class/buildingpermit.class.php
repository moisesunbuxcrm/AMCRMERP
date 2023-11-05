<?php
/* Copyright (C) 2017  Laurent Destailleur <eldy@users.sourceforge.net>
 * Copyright (C) ---Put here your own copyright and developer email---
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * \file        class/buildingpermit.class.php
 * \ingroup     amhppermits
 * \brief       This file is a CRUD class file for BuildingPermit (Create/Read/Update/Delete)
 */

// Put here all includes required by your class file
require_once DOL_DOCUMENT_ROOT . '/core/class/commonobject.class.php';
require_once DOL_DOCUMENT_ROOT . '/custom/amhpestimates/class/eaproductionorders.class.php';
require_once DOL_DOCUMENT_ROOT . '/custom/amhpestimatesv2/class/eaestimate.class.php';
require_once DOL_DOCUMENT_ROOT . '/custom/amhppermits/include/utils.inc.php';

/**
 * Class for BuildingPermit
 */
class BuildingPermit extends CommonObject
{
	/**
	 * @var string ID to identify managed object
	 */
	public $element = 'buildingpermit';
	/**
	 * @var string Name of table without prefix where object is stored
	 */
	public $table_element = 'amhppermits_buildingpermit';
	/**
	 * @var int  Does buildingpermit support multicompany module ? 0=No test on entity, 1=Test with field entity, 2=Test with link by societe
	 */
	public $ismultientitymanaged = 0;
	/**
	 * @var int  Does buildingpermit support extrafields ? 0=No, 1=Yes
	 */
	public $isextrafieldmanaged = 1;
	/**
	 * @var string String with name of icon for buildingpermit. Must be the part after the 'object_' into object_buildingpermit.png
	 */
	public $picto = 'buildingpermit@amhppermits';

	public $TEMPLATE_BASE = DOL_DOCUMENT_ROOT . "/custom/amhppermits/customfields";

	/**
	 *  'type' if the field format.
	 *  'label' the translation key.
	 *  'enabled' is a condition when the field must be managed.
	 *  'visible' says if field is visible in list (Examples: 0=Not visible, 1=Visible on list and create/update/view forms, 2=Visible on list only. Using a negative value means field is not shown by default on list but can be selected for viewing)
	 *  'notnull' is set to 1 if not null in database. Set to -1 if we must set data to null if empty ('' or 0).
	 *  'index' if we want an index in database.
	 *  'foreignkey'=>'tablename.field' if the field is a foreign key (it is recommanded to name the field fk_...).
	 *  'position' is the sort order of field.
	 *  'searchall' is 1 if we want to search in this field when making a search from the quick search button.
	 *  'isameasure' must be set to 1 if you want to have a total on list for this field. Field type must be summable like integer or double(24,8).
	 *  'help' is a string visible as a tooltip on field
	 *  'comment' is not used. You can store here any text of your choice. It is not used by application.
	 *  'default' is a default value for creation (can still be replaced by the global setup of default values)
	 *  'showoncombobox' if field must be shown into the label of combobox
	 */

	// BEGIN MODULEBUILDER PROPERTIES
	/**
	 * @var array  Array with all fields and their property. Do not use it as a static var. It may be modified by constructor.
	 */
	public $fields=array(
		'rowid' => array('type'=>'integer', 'label'=>'TechnicalID', 'visible'=>-1, 'enabled'=>1, 'position'=>1, 'notnull'=>1, 'index'=>1, 'comment'=>"Id",),
		'ref' => array('type'=>'varchar(128)', 'label'=>'Ref', 'visible'=>1, 'enabled'=>1, 'position'=>10, 'notnull'=>1, 'index'=>1, 'searchall'=>1, 'comment'=>"Reference of object",),
		'entity' => array('type'=>'integer', 'label'=>'Entity', 'visible'=>0, 'enabled'=>1, 'position'=>20, 'notnull'=>1, 'index'=>1,),
		'label' => array('type'=>'varchar(255)', 'label'=>'Label', 'visible'=>2, 'enabled'=>1, 'position'=>30, 'notnull'=>-1, 'searchall'=>1, 'help'=>"Help text",),
		'fk_soc' => array('type'=>'integer:Societe:societe/class/societe.class.php', 'label'=>'ThirdParty', 'visible'=>1, 'enabled'=>1, 'position'=>50, 'notnull'=>-1, 'index'=>1, 'searchall'=>1, 'help'=>"LinkToThirdParty",),
		'owner' => array('type'=>'varchar(50)', 'label'=>'Owner of property', 'visible'=>1, 'enabled'=>1, 'position'=>55, 'notnull'=>-1, 'searchall'=>1, 'comment'=>"Name from TP",),
		'owner_to_sign' => array('type'=>'varchar(128)', 'label'=>'Owner to Sign', 'visible'=>1, 'enabled'=>1, 'position'=>60, 'notnull'=>-1, 'index'=>1, 'searchall'=>1),
		'date_creation' => array('type'=>'datetime', 'label'=>'DateCreation', 'visible'=>0, 'enabled'=>1, 'position'=>500, 'notnull'=>1,),
		'tms' => array('type'=>'timestamp', 'label'=>'DateModification', 'visible'=>0, 'enabled'=>1, 'position'=>501, 'notnull'=>1,),
		'fk_user_creat' => array('type'=>'integer', 'label'=>'UserAuthor', 'visible'=>0, 'enabled'=>1, 'position'=>510, 'notnull'=>1,),
		'fk_user_modif' => array('type'=>'integer', 'label'=>'UserModif', 'visible'=>0, 'enabled'=>1, 'position'=>511, 'notnull'=>-1,),
		'import_key' => array('type'=>'varchar(14)', 'label'=>'ImportId', 'visible'=>0, 'enabled'=>1, 'position'=>1000, 'notnull'=>-1,),
		'status' => array('type'=>'integer', 'label'=>'Status', 'visible'=>0, 'enabled'=>1, 'position'=>1003, 'notnull'=>1, 'index'=>1, 'arrayofkeyval'=>array('0'=>'Draft', '1'=>'Active', '-1'=>'Cancel')),
		'jobaddress' => array('type'=>'varchar(255)', 'label'=>'Job Address', 'visible'=>1, 'enabled'=>1, 'position'=>1006, 'notnull'=>-1, 'searchall'=>1, 'comment'=>"Address from Third Party",),
		'poid' => array('type'=>'integer:EaProductionOrders:custom/amhpestimates/class/eaproductionorders.class.php', 'label'=>'Estimate', 'visible'=>1, 'enabled'=>1, 'position'=>90, 'notnull'=>0, 'index'=>1, 'searchall'=>1, 'comment'=>"Associated Estimate",),
		'eid' => array('type'=>'integer:EaEstimate:custom/amhpestimatesv2/class/eaestimate.class.php', 'label'=>'EstimateV2', 'visible'=>1, 'enabled'=>1, 'position'=>91, 'notnull'=>0, 'index'=>1, 'searchall'=>1, 'comment'=>"Associated V2 Estimate",),
		'town' => array('type'=>'varchar(50)', 'label'=>'Owner Town', 'visible'=>1, 'enabled'=>1, 'position'=>1010, 'notnull'=>-1, 'searchall'=>1, 'comment'=>"Town from Third Party",),
		'buildingdeptcity' => array('type'=>'varchar(128)', 'label'=>'Building Department City', 'visible'=>1, 'enabled'=>1, 'position'=>1015, 'notnull'=>1, 'comment'=>"Link to Building Department",),
		'state' => array('type'=>'varchar(50)', 'label'=>'Owner State', 'visible'=>1, 'enabled'=>1, 'position'=>1020, 'notnull'=>-1, 'searchall'=>1, 'comment'=>"State from Third Party",),
		'zip' => array('type'=>'varchar(25)', 'label'=>'Owner Zip', 'visible'=>1, 'enabled'=>1, 'position'=>1030, 'notnull'=>-1, 'searchall'=>1, 'comment'=>"Zip from Third Party",),
		'barcode' => array('type'=>'varchar(255)', 'label'=>'Folio Number', 'visible'=>1, 'enabled'=>1, 'position'=>1040, 'notnull'=>-1, 'searchall'=>1, 'comment'=>"Barcode from Third Party",),
		'lot' => array('type'=>'varchar(25)', 'label'=>'Lot', 'visible'=>1, 'enabled'=>1, 'position'=>1050, 'notnull'=>-1, 'comment'=>"Lot from Third Party complementary attribute",),
		'block' => array('type'=>'varchar(25)', 'label'=>'Block', 'visible'=>1, 'enabled'=>1, 'position'=>1060, 'notnull'=>-1, 'comment'=>"Block from Third Party complimentary attribute",),
		'tract' => array('type'=>'varchar(25)', 'label'=>'Tract', 'visible'=>1, 'enabled'=>1, 'position'=>1062, 'notnull'=>-1,),
		'building_no' => array('type'=>'varchar(25)', 'label'=>'Bldg#', 'visible'=>1, 'enabled'=>1, 'position'=>1064, 'notnull'=>-1,),
		'unit' => array('type'=>'varchar(25)', 'label'=>'Unit', 'visible'=>1, 'enabled'=>1, 'position'=>1067, 'notnull'=>-1,),
		'subdivision' => array('type'=>'varchar(50)', 'label'=>'Subdivision', 'visible'=>1, 'enabled'=>1, 'position'=>1070, 'notnull'=>-1, 'comment'=>"Subdivision from Third Party complementary attribute",),
		'primarypermit' => array('type'=>'varchar(50)', 'label'=>'Primary Permit', 'visible'=>1, 'enabled'=>1, 'position'=>1072, 'notnull'=>-1, 'arrayofkeyval'=>array('Primary Permit'=>'Primary Permit', 'Sub-permit'=>'Sub-permit', 'Revision'=>'Revision')),
		'permitno' => array('type'=>'varchar(50)', 'label'=>'Primary Permit #', 'visible'=>1, 'enabled'=>1, 'position'=>1073, 'notnull'=>-1),
		'legal_attached' => array('type'=>'integer', 'label'=>'Lengthy Legal Attached', 'visible'=>1, 'enabled'=>1, 'position'=>1075, 'notnull'=>-1, 'arrayofkeyval'=>array('1'=>'No', '2'=>'Yes')),
		'pbpg' => array('type'=>'varchar(25)', 'label'=>'PBpg', 'visible'=>1, 'enabled'=>1, 'position'=>1080, 'notnull'=>-1, 'comment'=>"PBpg from Third Party complementary attribute",),
		'metesandbounds' => array('type'=>'varchar(100)', 'label'=>'Metes and bounds', 'visible'=>1, 'enabled'=>1, 'position'=>1090, 'notnull'=>-1, 'comment'=>"Metes and bounds from the Third Party complementary attribute",),
		'hasapproval' => array('type'=>'varchar(50)', 'label'=>'Homeowner\'s Association Approval', 'visible'=>1, 'enabled'=>1, 'position'=>1095, 'notnull'=>-1, 'arrayofkeyval'=>array('Has approval'=>'Has approval', 'Does not need approval'=>'Does not need approval')),
		'homeownerassoc' => array('type'=>'varchar(255)', 'label'=>'Homeowner\'s Association', 'visible'=>1, 'enabled'=>1, 'position'=>1096, 'notnull'=>-1),
		'owner_signature_date' => array('type'=>'date', 'label'=>'Owner Signature Date', 'visible'=>1, 'enabled'=>1, 'position'=>1097, 'notnull'=>-1,),
		'processnum' => array('type'=>'varchar(50)', 'label'=>'Application Process Number', 'visible'=>1, 'enabled'=>1, 'position'=>1098, 'notnull'=>-1,),
		'contractornum' => array('type'=>'varchar(25)', 'label'=>'Contractor No.', 'visible'=>1, 'enabled'=>1, 'position'=>1100, 'notnull'=>-1, 'comment'=>"Prod Id (FEIN) from Company/Org",),
		'qualifiernum' => array('type'=>'varchar(25)', 'label'=>'Qualifier No.', 'visible'=>1, 'enabled'=>1, 'position'=>1110, 'notnull'=>-1, 'searchall'=>1, 'comment'=>"Sales Tax ID from Company/Org",),
		'contractorname' => array('type'=>'varchar(100)', 'label'=>'Contractor Name', 'visible'=>1, 'enabled'=>1, 'position'=>1120, 'notnull'=>-1, 'comment'=>"Name from Company/Org",),
		'qualifiername' => array('type'=>'varchar(100)', 'label'=>'Qualifier Name', 'visible'=>1, 'enabled'=>1, 'position'=>1130, 'notnull'=>-1, 'comment'=>"Managers Name from Company/Org",),
		'qualifiertitle' => array('type'=>'varchar(100)', 'label'=>'Qualifier Title', 'visible'=>1, 'enabled'=>1, 'position'=>1135, 'notnull'=>-1, 'comment'=>"Managers Name from Company/Org",),
		'contractoraddress' => array('type'=>'varchar(255)', 'label'=>'Contractor Address', 'visible'=>1, 'enabled'=>1, 'position'=>1140, 'notnull'=>-1, 'comment'=>"Address from Company/Org",),
		'contractorcity' => array('type'=>'varchar(50)', 'label'=>'Contractor City', 'visible'=>-1, 'enabled'=>1, 'position'=>1150, 'notnull'=>-1, 'comment'=>"City from Company/Org",),
		'contractorstate' => array('type'=>'varchar(50)', 'label'=>'Contractor State', 'visible'=>-1, 'enabled'=>1, 'position'=>1160, 'notnull'=>-1, 'comment'=>"State from Company/Org",),
		'contractorzip' => array('type'=>'varchar(25)', 'label'=>'Contractor Zip', 'visible'=>-1, 'enabled'=>1, 'position'=>1170, 'notnull'=>-1, 'comment'=>"Zip from Company/Org",),
		'contractorphone' => array('type'=>'varchar(25)', 'label'=>'Contractor Phone', 'visible'=>-1, 'enabled'=>1, 'position'=>1172, 'notnull'=>-1, 'comment'=>"Phone from Company/Org",),
		'contractorfax' => array('type'=>'varchar(25)', 'label'=>'Contractor Fax', 'visible'=>-1, 'enabled'=>1, 'position'=>1173, 'notnull'=>-1, 'comment'=>"Fax from Company/Org",),
		'contractoremail' => array('type'=>'varchar(100)', 'label'=>'Contractor Email', 'visible'=>-1, 'enabled'=>1, 'position'=>1174, 'notnull'=>-1, 'comment'=>"Email from Company/Org",),
		'owner_builder' => array('type'=>'integer', 'label'=>'Owner-Builder', 'visible'=>1, 'enabled'=>1, 'position'=>1174, 'notnull'=>-1, 'arrayofkeyval'=>array('1'=>'No', '2'=>'Yes')),
		'currentuse' => array('type'=>'varchar(255)', 'label'=>'Current use of property', 'visible'=>-1, 'enabled'=>1, 'position'=>1180, 'notnull'=>-1, 'comment'=>"Legal Form from Third Party"),
		'proposeduse' => array('type'=>'varchar(255)', 'label'=>'Proposed use of property', 'visible'=>-1, 'enabled'=>1, 'position'=>1185, 'notnull'=>-1),
		'descriptionofwork' => array('type'=>'varchar(512)', 'label'=>'Description of Work', 'visible'=>1, 'enabled'=>1, 'position'=>1190, 'notnull'=>-1, 'comment'=>"Copied from Estimate",),
		'description_of_improvements' => array('type'=>'varchar(512)', 'label'=>'Description of Improvements', 'visible'=>1, 'enabled'=>1, 'position'=>1200, 'notnull'=>-1,),
		'improvementstype' => array('type'=>'varchar(500)', 'label'=>'Type of Improvements', 'visible'=>1, 'enabled'=>1, 'position'=>1202, 'notnull'=>-1, 'comment'=>"Custom code in forms",),
		'improvementstype_other' => array('type'=>'varchar(100)', 'label'=>'Type of Improvements Other', 'visible'=>1, 'enabled'=>1, 'position'=>1203, 'notnull'=>-1,),
		'permittype' => array('type'=>'varchar(500)', 'label'=>'Permit Type', 'visible'=>1, 'enabled'=>1, 'position'=>1204, 'notnull'=>-1, 'comment'=>"Custom code in forms",),
		'permittype_other' => array('type'=>'varchar(100)', 'label'=>'Permit Type Other', 'visible'=>1, 'enabled'=>1, 'position'=>1205, 'notnull'=>-1,),
		'permitfee' => array('type'=>'double(24,8)', 'label'=>'Permit fee', 'visible'=>-1, 'enabled'=>1, 'position'=>1207, 'notnull'=>-1, 'comment'=>"PERMIT from Estimate",),
		'dumpsterpermit' => array('type'=>'integer', 'label'=>'Dumpster permit included?', 'visible'=>1, 'enabled'=>1, 'position'=>1208, 'notnull'=>-1, 'arrayofkeyval'=>array('1'=>'No', '2'=>'Yes')),
		'buildingcategory' => array('type'=>'varchar(10)', 'label'=>'Building Category', 'visible'=>1, 'enabled'=>1, 'position'=>1209, 'notnull'=>-1, 'comment'=>"From Miami Dade Permits"),
		'classificationofwork' => array('type'=>'varchar(500)', 'label'=>'Classification of work', 'visible'=>1, 'enabled'=>1, 'position'=>1210, 'notnull'=>-1),
		'totalalum' => array('type'=>'double(24,8)', 'label'=>'Sq. Ft.', 'visible'=>-1, 'enabled'=>1, 'position'=>1211, 'notnull'=>-1, 'comment'=>"TOTALALUM from Estimate",),
		'totallinearft' => array('type'=>'double(24,8)', 'label'=>'Total Linear Ft.', 'visible'=>-1, 'enabled'=>1, 'position'=>1215, 'notnull'=>-1, 'comment'=>"TOTALLINEARFT from Estimate",),
		'units' => array('type'=>'integer', 'label'=>'Units', 'visible'=>-1, 'enabled'=>1, 'position'=>1220, 'notnull'=>-1, 'comment'=>"Units from Estimate",),
		'floors' => array('type'=>'integer', 'label'=>'Floors', 'visible'=>-1, 'enabled'=>1, 'position'=>1225, 'notnull'=>-1, 'comment'=>"Floors from Estimate",),
		'valueofwork' => array('type'=>'double(24,8)', 'label'=>'Value of work', 'visible'=>-1, 'enabled'=>1, 'position'=>1230, 'notnull'=>-1, 'comment'=>"SALESPRICE from Estimate",),
		'owner_title' => array('type'=>'varchar(50)', 'label'=>'Owner Title/Office', 'visible'=>-1, 'enabled'=>1, 'position'=>1242, 'notnull'=>-1, 'searchall'=>1, 'comment'=>"Name from TP",),
		'coowner' => array('type'=>'varchar(50)', 'label'=>'Co-owner', 'visible'=>-1, 'enabled'=>1, 'position'=>1244, 'notnull'=>-1, 'searchall'=>1, ),
		'coowner_title' => array('type'=>'varchar(50)', 'label'=>'Co-owner Title/Office', 'visible'=>-1, 'enabled'=>1, 'position'=>1246, 'notnull'=>-1, 'searchall'=>1, ),
		'tp_address' => array('type'=>'varchar(255)', 'label'=>'Third Party Address', 'visible'=>1, 'enabled'=>1, 'position'=>1250, 'notnull'=>-1, 'searchall'=>1, 'comment'=>"Address from TP",),
		'tp_city' => array('type'=>'varchar(25)', 'label'=>'Third Party City', 'visible'=>1, 'enabled'=>1, 'position'=>1260, 'notnull'=>-1, 'searchall'=>1, 'comment'=>"City from TP",),
		'tp_state' => array('type'=>'varchar(50)', 'label'=>'Third Party State', 'visible'=>1, 'enabled'=>1, 'position'=>1270, 'notnull'=>-1, 'searchall'=>1, 'comment'=>"State from TP",),
		'tp_zip' => array('type'=>'varchar(25)', 'label'=>'Third Party Zip', 'visible'=>1, 'enabled'=>1, 'position'=>1280, 'notnull'=>-1, 'searchall'=>1, 'comment'=>"Zip from TP",),
		'tp_phone' => array('type'=>'varchar(25)', 'label'=>'Third Party Phone', 'visible'=>1, 'enabled'=>1, 'position'=>1290, 'notnull'=>-1, 'comment'=>"Primary Phone from TP",),
		'tp_fax' => array('type'=>'varchar(25)', 'label'=>'Third Party Fax', 'visible'=>1, 'enabled'=>1, 'position'=>1291, 'notnull'=>-1, 'comment'=>"Fax from TP",),
		'tp_email' => array('type'=>'varchar(100)', 'label'=>'Third Party Email', 'visible'=>1, 'enabled'=>1, 'position'=>1292, 'notnull'=>-1, 'comment'=>"Email from TP",),
		'pickup_name' => array('type'=>'varchar(50)', 'label'=>'Pick Up Name', 'visible'=>-1, 'enabled'=>1, 'position'=>1300, 'notnull'=>-1, 'comment'=>"Name from Company/Org",),
		'pickup_address' => array('type'=>'varchar(255)', 'label'=>'Pick Up Address', 'visible'=>-1, 'enabled'=>1, 'position'=>1310, 'notnull'=>-1, 'comment'=>"Address from Company/Org",),
		'pickup_city' => array('type'=>'varchar(50)', 'label'=>'Pick Up City', 'visible'=>-1, 'enabled'=>1, 'position'=>1320, 'notnull'=>-1, 'comment'=>"City from Company/Org",),
		'pickup_state' => array('type'=>'varchar(50)', 'label'=>'Pick Up State', 'visible'=>-1, 'enabled'=>1, 'position'=>1330, 'notnull'=>-1, 'comment'=>"State from Company/Org",),
		'pickup_zip' => array('type'=>'varchar(25)', 'label'=>'Pick Up Zip', 'visible'=>-1, 'enabled'=>1, 'position'=>1340, 'notnull'=>-1, 'comment'=>"Zip from Company/Org",),
		'pickup_phone' => array('type'=>'varchar(25)', 'label'=>'Pick Up Phone', 'visible'=>-1, 'enabled'=>1, 'position'=>1350, 'notnull'=>-1, 'comment'=>"Primary Phone from Company/Org",),
		'archname' => array('type'=>'varchar(128)', 'label'=>'Architect Name', 'visible'=>1, 'enabled'=>1, 'position'=>1360, 'notnull'=>-1, 'searchall'=>1,),
		'archaddress' => array('type'=>'varchar(255)', 'label'=>'Architect Address', 'visible'=>1, 'enabled'=>1, 'position'=>1370, 'notnull'=>-1,),
		'archcity' => array('type'=>'varchar(50)', 'label'=>'Architect City', 'visible'=>1, 'enabled'=>1, 'position'=>1380, 'notnull'=>-1,),
		'archstate' => array('type'=>'varchar(50)', 'label'=>'Architect State', 'visible'=>1, 'enabled'=>1, 'position'=>1390, 'notnull'=>-1,),
		'archzip' => array('type'=>'varchar(25)', 'label'=>'Architect Zip', 'visible'=>1, 'enabled'=>1, 'position'=>1400, 'notnull'=>-1,),
		'archphone' => array('type'=>'varchar(25)', 'label'=>'Architect Phone', 'visible'=>1, 'enabled'=>1, 'position'=>1410, 'notnull'=>-1,),
		'archemail' => array('type'=>'varchar(100)', 'label'=>'Architect Email', 'visible'=>1, 'enabled'=>1, 'position'=>1411, 'notnull'=>-1,),
		'bondname' => array('type'=>'varchar(100)', 'label'=>'Bonding Company', 'visible'=>1, 'enabled'=>1, 'position'=>1412, 'notnull'=>-1,),
		'bondaddress' => array('type'=>'varchar(255)', 'label'=>'Bonding Address', 'visible'=>1, 'enabled'=>1, 'position'=>1413, 'notnull'=>-1,),
		'bondcity' => array('type'=>'varchar(50)', 'label'=>'Bonding City', 'visible'=>1, 'enabled'=>1, 'position'=>1414, 'notnull'=>-1,),
		'bondstate' => array('type'=>'varchar(50)', 'label'=>'Bonding State', 'visible'=>1, 'enabled'=>1, 'position'=>1416, 'notnull'=>-1,),
		'bondzip' => array('type'=>'varchar(25)', 'label'=>'Bonding Zip', 'visible'=>1, 'enabled'=>1, 'position'=>1417, 'notnull'=>-1,),
		'bondphone' => array('type'=>'varchar(25)', 'label'=>'Bonding Phone', 'visible'=>1, 'enabled'=>1, 'position'=>1418, 'notnull'=>-1,),
		'surety_bond' => array('type'=>'double(24,8)', 'label'=>'Bond amount', 'visible'=>1, 'enabled'=>1, 'position'=>1419, 'notnull'=>-1,),
		'lendername' => array('type'=>'varchar(128)', 'label'=>'Lender Name', 'visible'=>1, 'enabled'=>1, 'position'=>1420, 'notnull'=>-1,),
		'lenderaddress' => array('type'=>'varchar(255)', 'label'=>'Lender Address', 'visible'=>1, 'enabled'=>1, 'position'=>1421, 'notnull'=>-1,),
		'lendercity' => array('type'=>'varchar(50)', 'label'=>'Lender City', 'visible'=>1, 'enabled'=>1, 'position'=>1422, 'notnull'=>-1,),
		'lenderstate' => array('type'=>'varchar(50)', 'label'=>'Lender State', 'visible'=>1, 'enabled'=>1, 'position'=>1424, 'notnull'=>-1,),
		'lenderzip' => array('type'=>'varchar(25)', 'label'=>'Lender Zip', 'visible'=>1, 'enabled'=>1, 'position'=>1426, 'notnull'=>-1,),
		'lenderphone' => array('type'=>'varchar(25)', 'label'=>'Lender Phone', 'visible'=>1, 'enabled'=>1, 'position'=>1427, 'notnull'=>-1,),
		'lienorname' => array('type'=>'varchar(128)', 'label'=>'Lienor Name', 'visible'=>1, 'enabled'=>1, 'position'=>1428, 'notnull'=>-1,),
		'lienoraddress' => array('type'=>'varchar(255)', 'label'=>'Lienor Address', 'visible'=>1, 'enabled'=>1, 'position'=>1429, 'notnull'=>-1,),
		'lienorcity' => array('type'=>'varchar(50)', 'label'=>'Lienor City', 'visible'=>1, 'enabled'=>1, 'position'=>1430, 'notnull'=>-1,),
		'lienorstate' => array('type'=>'varchar(50)', 'label'=>'Lienor State', 'visible'=>1, 'enabled'=>1, 'position'=>1431, 'notnull'=>-1,),
		'lienorzip' => array('type'=>'varchar(25)', 'label'=>'Lienor Zip', 'visible'=>1, 'enabled'=>1, 'position'=>1432, 'notnull'=>-1,),
		'lienorphone' => array('type'=>'varchar(25)', 'label'=>'Lienor Phone', 'visible'=>1, 'enabled'=>1, 'position'=>1433, 'notnull'=>-1,),
		'lienamount' => array('type'=>'varchar(50)', 'label'=>'Lien Amount', 'visible'=>1, 'enabled'=>1, 'position'=>1434, 'notnull'=>-1,),
		'owner_name_address' => array('type'=>'varchar(128)', 'label'=>'Owner Contact', 'visible'=>1, 'enabled'=>1, 'position'=>1439, 'notnull'=>-1,),
		'owner_interest_in_property' => array('type'=>'varchar(128)', 'label'=>'Owner Interest in Property', 'visible'=>1, 'enabled'=>1, 'position'=>1440, 'notnull'=>-1,),
		'titleholdername' => array('type'=>'varchar(128)', 'label'=>'Titleholder Name', 'visible'=>1, 'enabled'=>1, 'position'=>1441, 'notnull'=>-1,),
		'titleholderaddress' => array('type'=>'varchar(128)', 'label'=>'Titleholder Address', 'visible'=>1, 'enabled'=>1, 'position'=>1442, 'notnull'=>-1,),
		'titleholdercity' => array('type'=>'varchar(50)', 'label'=>'Titleholder City', 'visible'=>1, 'enabled'=>1, 'position'=>1443, 'notnull'=>-1,),
		'titleholderstate' => array('type'=>'varchar(50)', 'label'=>'Titleholder State', 'visible'=>1, 'enabled'=>1, 'position'=>1444, 'notnull'=>-1,),
		'titleholderzip' => array('type'=>'varchar(25)', 'label'=>'Titleholder Zip', 'visible'=>1, 'enabled'=>1, 'position'=>1445, 'notnull'=>-1,),
		'titleholderphone' => array('type'=>'varchar(25)', 'label'=>'Titleholder Phone', 'visible'=>1, 'enabled'=>1, 'position'=>1446, 'notnull'=>-1,),
		'designatedname' => array('type'=>'varchar(128)', 'label'=>'Person Designated by Owner Name', 'visible'=>1, 'enabled'=>1, 'position'=>1460, 'notnull'=>-1,),
		'designatedaddress' => array('type'=>'varchar(128)', 'label'=>'Person Designated by Owner Address', 'visible'=>1, 'enabled'=>1, 'position'=>1462, 'notnull'=>-1,),
		'designatedcity' => array('type'=>'varchar(50)', 'label'=>'Person Designated by Owner City', 'visible'=>1, 'enabled'=>1, 'position'=>1464, 'notnull'=>-1,),
		'designatedstate' => array('type'=>'varchar(50)', 'label'=>'Person Designated by Owner State', 'visible'=>1, 'enabled'=>1, 'position'=>1466, 'notnull'=>-1,),
		'designatedzip' => array('type'=>'varchar(25)', 'label'=>'Person Designated by Owner Zip', 'visible'=>1, 'enabled'=>1, 'position'=>1468, 'notnull'=>-1,),
		'designatedphone' => array('type'=>'varchar(25)', 'label'=>'Person Designated by Owner Phone', 'visible'=>1, 'enabled'=>1, 'position'=>1470, 'notnull'=>-1,),
		'designatednameln' => array('type'=>'varchar(128)', 'label'=>'Lienor Notice Copied Name', 'visible'=>1, 'enabled'=>1, 'position'=>1472, 'notnull'=>-1,),
		'designatedphoneln' => array('type'=>'varchar(25)', 'label'=>'Lienor Notice Copied Phone', 'visible'=>1, 'enabled'=>1, 'position'=>1474, 'notnull'=>-1,),
		'notices_name_address_phone' => array('type'=>'varchar(128)', 'label'=>'Notices Contact', 'visible'=>1, 'enabled'=>1, 'position'=>1480, 'notnull'=>-1,),
		'lienor_name_address_phone' => array('type'=>'varchar(128)', 'label'=>'Lienor Notices Contact', 'visible'=>1, 'enabled'=>1, 'position'=>1490, 'notnull'=>-1,),
		'notice_of_commencement_date' => array('type'=>'date', 'label'=>'Notice of Commencement Date', 'visible'=>1, 'enabled'=>1, 'position'=>1510, 'notnull'=>-1,),
		'dade_or_book' => array('type'=>'varchar(25)', 'label'=>'Recorded Book', 'visible'=>1, 'enabled'=>1, 'position'=>1520, 'notnull'=>-1,),
		'dade_or_page' => array('type'=>'varchar(25)', 'label'=>'Recorded Page', 'visible'=>1, 'enabled'=>1, 'position'=>1530, 'notnull'=>-1,),
		'notice_of_commencement_term_date' => array('type'=>'date', 'label'=>'Notice of Commencement Termination Date', 'visible'=>1, 'enabled'=>1, 'position'=>1540, 'notnull'=>-1,),
		'notice_of_commencement_expr_date' => array('type'=>'date', 'label'=>'Notice of Commencement Expiration Date', 'visible'=>1, 'enabled'=>1, 'position'=>1545, 'notnull'=>-1,),
		'notice_of_term_applies_to' => array('type'=>'varchar(10)', 'label'=>'Notice of Termination Applies To', 'visible'=>1, 'enabled'=>1, 'position'=>1550, 'notnull'=>-1, 'arrayofkeyval'=>array('all'=>'All Real Property', 'portion'=>'Portion of Real Property'), 'default'=>'all'),
		'notice_of_term_portion' => array('type'=>'varchar(600)', 'label'=>'Notice of Termination Applicable Portion', 'visible'=>1, 'enabled'=>1, 'position'=>1560, 'notnull'=>-1,),
		'unpaid_labor_and_materials' => array('type'=>'varchar(600)', 'label'=>'Unpaid Labor and Materials', 'visible'=>1, 'enabled'=>1, 'position'=>1570, 'notnull'=>-1,),
		'unpaid_lienors' => array('type'=>'varchar(600)', 'label'=>'Unpaid Lienors', 'visible'=>1, 'enabled'=>1, 'position'=>1580, 'notnull'=>-1,),
		'owner_personally_known' => array('type'=>'varchar(50)', 'label'=>'Owner Personally Known', 'visible'=>1, 'enabled'=>1, 'position'=>1590, 'notnull'=>-1),
		'owner_produced_id' => array('type'=>'varchar(50)', 'label'=>'Owner Produced Id', 'visible'=>1, 'enabled'=>1, 'position'=>1600, 'notnull'=>-1,),
		'qualifier_personally_known' => array('type'=>'varchar(50)', 'label'=>'Qualifier Personally Known', 'visible'=>1, 'enabled'=>1, 'position'=>1610, 'notnull'=>-1),
		'qualifier_produced_id' => array('type'=>'varchar(50)', 'label'=>'Qualifier Produced Id', 'visible'=>1, 'enabled'=>1, 'position'=>1620, 'notnull'=>-1,),
		'notary_name' => array('type'=>'varchar(100)', 'label'=>'Notary Name', 'visible'=>1, 'enabled'=>1, 'position'=>1623, 'notnull'=>-1,),
		'expiration_date' => array('type'=>'date', 'label'=>'Permit Expiration Date', 'visible'=>1, 'enabled'=>1, 'position'=>1624, 'notnull'=>-1,),
		'comm_ack_individ_flag' => array('type'=>'varchar(50)', 'label'=>'Commencement Acknowledged by', 'visible'=>1, 'enabled'=>1, 'position'=>1626, 'notnull'=>-1),
		'comm_ack_as' => array('type'=>'varchar(100)', 'label'=>'Commencement Acknowledged As', 'visible'=>1, 'enabled'=>1, 'position'=>1629, 'notnull'=>-1,),
		'cnf' => array('type'=>'varchar(100)', 'label'=>'CFN', 'visible'=>1, 'enabled'=>1, 'position'=>1660, 'notnull'=>-1,),
		'term_attachments' => array('type'=>'checkbox', 'label'=>'Notice of Termination Attachments', 'visible'=>1, 'enabled'=>1, 'position'=>1670, 'notnull'=>-1),
		'notary_commission_expires' => array('type'=>'varchar(50)', 'label'=>'Notary Commision Expires', 'visible'=>1, 'enabled'=>1, 'position'=>1680, 'notnull'=>-1,),
		'commission_number' => array('type'=>'varchar(25)', 'label'=>'Notary Commision Number', 'visible'=>1, 'enabled'=>1, 'position'=>1685, 'notnull'=>-1,),
		'flood_zone' => array('type'=>'varchar(100)', 'label'=>'Flood Zn', 'visible'=>1, 'enabled'=>1, 'position'=>1690, 'notnull'=>-1,),
		'bfe' => array('type'=>'varchar(100)', 'label'=>'BFE', 'visible'=>1, 'enabled'=>1, 'position'=>1700, 'notnull'=>-1,),
		'floor_area' => array('type'=>'varchar(100)', 'label'=>'Floor Area', 'visible'=>1, 'enabled'=>1, 'position'=>1710, 'notnull'=>-1,),
		'construction_type' => array('type'=>'varchar(100)', 'label'=>'Construction Type', 'visible'=>1, 'enabled'=>1, 'position'=>1720, 'notnull'=>-1,),
		'occupancy_group' => array('type'=>'varchar(100)', 'label'=>'Occupancy Group', 'visible'=>1, 'enabled'=>1, 'position'=>1730, 'notnull'=>-1,),
		'attachment' => array('type'=>'integer', 'label'=>'Attachment', 'visible'=>1, 'enabled'=>1, 'position'=>1740, 'notnull'=>-1, 'arrayofkeyval'=>array('1'=>'No', '2'=>'Yes')),
	);

	public $rowid;
	public $ref;
	public $entity;
	public $label;
	public $fk_soc;
	public $owner_to_sign;
	public $date_creation;
	public $tms;
	public $fk_user_creat;
	public $fk_user_modif;
	public $import_key;
	public $status;
	public $jobaddress;
	public $poid;
	public $eid;
	public $town;
	public $buildingdeptcity;
	public $state;
	public $zip;
	public $barcode;
	public $lot;
	public $block;
	public $building_no;
	public $unit;
	public $subdivision;
	public $legal_attached;
	public $pbpg;
	public $metesandbounds;
	public $contractornum;
	public $qualifiernum;
	public $contractorname;
	public $qualifiername;
	public $qualifiertitle;
	public $contractoraddress;
	public $contractorcity;
	public $contractorstate;
	public $contractorzip;
	public $contractorphone;
	public $contractorfax;
	public $contractoremail;
	public $owner_builder;
	public $currentuse;
	public $proposeduse;
	public $descriptionofwork;
	public $description_of_improvements;
	public $improvementstype;
	public $improvementstype_other;
	public $permittype;
	public $permittype_other;
	public $primarypermit;
	public $permitno;
	public $permitfee;
	public $dumpsterpermit;
	public $buildingcategory;
	public $classificationofwork;
	public $totalalum;
	public $totallinearft;
	public $units;
	public $floors;
	public $valueofwork;
	public $owner;
	public $owner_title;
	public $coowner;
	public $coowner_title;
	public $tp_address;
	public $tp_city;
	public $tp_state;
	public $tp_zip;
	public $tp_phone;
	public $tp_fax;
	public $tp_email;
	public $pickup_name;
	public $pickup_address;
	public $pickup_city;
	public $pickup_state;
	public $pickup_zip;
	public $pickup_phone;
	public $archname;
	public $archaddress;
	public $archcity;
	public $archstate;
	public $archzip;
	public $archphone;
	public $archemail;
	public $bondname;
	public $bondaddress;
	public $bondcity;
	public $bondstate;
	public $bondzip;
	public $bondphone;
	public $surety_bond;
	public $lendername;
	public $lenderaddress;
	public $lendercity;
	public $lenderstate;
	public $lenderzip;
	public $lenderphone;
	public $lienorname;
	public $lienoraddress;
	public $lienorcity;
	public $lienorstate;
	public $lienorzip;
	public $lienorphone;
	public $lienamount;
	public $owner_name_address;
	public $owner_interest_in_property;
	public $titleholdername;
	public $titleholderaddress;
	public $titleholdercity;
	public $titleholderstate;
	public $titleholderzip;
	public $titleholderphone;
	public $designatedname;
	public $designatedaddress;
	public $designatedcity;
	public $designatedstate;
	public $designatedzip;
	public $designatedphone;
	public $designatednameln;
	public $designatedphoneln;
	public $notices_name_address_phone;
	public $lienor_name_address_phone;
	public $notice_of_commencement_date;
	public $dade_or_book;
	public $dade_or_page;
	public $notice_of_commencement_term_date;
	public $notice_of_commencement_expr_date;
	public $notice_of_term_applies_to;
	public $notice_of_term_portion;
	public $unpaid_labor_and_materials;
	public $unpaid_lienors;
	public $owner_personally_known;
	public $qualifier_produced_id;
	public $qualifier_personally_known;
	public $owner_produced_id;
	public $notary_name;
	public $expiration_date;
	public $comm_ack_individ_flag;
	public $comm_ack_as;
	public $cnf;
	public $term_attachments;
	public $notary_commission_expires;
	public $commission_number;
	public $flood_zone;
	public $bfe;
	public $floor_area;
	public $construction_type;
	public $occupancy_group;
	public $attachment;
	public $hasapproval;
	public $homeownerassoc;
	
// END MODULEBUILDER PROPERTIES

	// If this object has a subtable with lines

	/**
	 * @var int    Name of subtable line
	 */
	//public $table_element_line = 'buildingpermitdet';
	/**
	 * @var int    Field with ID of parent key if this field has a parent
	 */
	//public $fk_element = 'fk_buildingpermit';
	/**
	 * @var int    Name of subtable class that manage subtable lines
	 */
	//public $class_element_line = 'BuildingPermitline';
	/**
	 * @var array  Array of child tables (child tables to delete before deleting a record)
	 */
	//protected $childtables=array('buildingpermitdet');
	/**
	 * @var BuildingPermitLine[]     Array of subtable lines
	 */
	//public $lines = array();

	/**
	 * Constructor
	 *
	 * @param DoliDb $db Database handler
	 */
	public function __construct(DoliDB $db)
	{
		global $conf;

		$this->db = $db;

		if (empty($conf->global->MAIN_SHOW_TECHNICAL_ID)) $this->fields['rowid']['visible']=0;
		if (empty($conf->multicompany->enabled)) $this->fields['entity']['enabled']=0;

		$this->fields['improvementstype']['joinOnUpsert'] = true;
		$this->fields['permittype']['joinOnUpsert'] = true;
		$this->fields['buildingcategory']['joinOnUpsert'] = true;
		$this->fields['term_attachments']['joinOnUpsert'] = true;
		$this->fields['hasapproval']['joinOnUpsert'] = true;
		$this->fields['primarypermit']['joinOnUpsert'] = true;
		$this->fields['classificationofwork']['joinOnUpsert'] = true;
		$this->fields['notice_of_term_applies_to']['joinOnUpsert'] = true;

		$this->fields['improvementstype_other']['hideInput'] = true;
		$this->fields['permittype_other']['hideInput'] = true;
		$this->fields['buildingcategory']['showOutputRequiresTemplate'] = true;
		$this->fields['term_attachments']['showOutputRequiresTemplate'] = true;

		$this->fields['currentuse']['arrayofkeyval'] = $this->getDropDownValues("llx_ea_useofproperty");
		$this->fields['proposeduse']['arrayofkeyval'] = $this->getDropDownValues("llx_ea_useofproperty");
		$this->fields['owner_personally_known']['arrayofkeyval'] = $this->getDropDownValues("llx_ea_personally_known");
		$this->fields['qualifier_personally_known']['arrayofkeyval'] = $this->getDropDownValues("llx_ea_personally_known");
		$this->fields['comm_ack_individ_flag']['arrayofkeyval'] = $this->getDropDownValues("llx_ea_comm_ack");
		
	}

	public function getDropDownValues($table)
	{
		$sql = "SELECT name FROM ".$table." WHERE active=1 order by id";
		$resultsarray = array();
		
		$resql=$this->db->query($sql);
		if ($resql)
		{
			while ($obj = $this->db->fetch_object($resql))
			{
				$val = new stdClass();
				$name = $obj->name; 
				$resultsarray[$name] = $name;
			}
		}
		else
		{
			echo $this->db->lasterror();
		}

		$this->db->free($resql);
		return $resultsarray;
	}

	/**
	 * Create object into database
	 *
	 * @param  User $user      User that creates
	 * @param  bool $notrigger false=launch triggers after, true=disable triggers
	 * @return int             <0 if KO, Id of created object if OK
	 */
	public function create(User $user, $notrigger = false)
	{
		return $this->createCommon($user, $notrigger);
	}

	/**
	 * Clone and object into another one
	 *
	 * @param  	User 	$user      	User that creates
	 * @param  	int 	$fromid     Id of object to clone
	 * @return 	mixed 				New object created, <0 if KO
	 */
	public function createFromClone(User $user, $fromid)
	{
		global $hookmanager, $langs;
	    $error = 0;

	    dol_syslog(__METHOD__, LOG_DEBUG);

	    $object = new self($this->db);

	    $this->db->begin();

	    // Load source object
	    $object->fetchCommon($fromid);
	    // Reset some properties
	    unset($object->id);
	    unset($object->fk_user_creat);
	    unset($object->import_key);

	    // Clear fields
	    $object->ref = "copy_of_".$object->ref;
	    $object->title = $langs->trans("CopyOf")." ".$object->title;
	    // ...

	    // Create clone
		$object->context['createfromclone'] = 'createfromclone';
	    $result = $object->createCommon($user);
	    if ($result < 0) {
	        $error++;
	        $this->error = $object->error;
	        $this->errors = $object->errors;
	    }

	    // End
	    if (!$error) {
	        $this->db->commit();
	        return $object;
	    } else {
	        $this->db->rollback();
	        return -1;
	    }
	}

	/**
	 * Load object in memory from the database
	 *
	 * @param int    $id   Id object
	 * @param string $ref  Ref
	 * @return int         <0 if KO, 0 if not found, >0 if OK
	 */
	public function fetch($id, $ref = null)
	{
		$result = $this->fetchCommon($id, $ref);
		if ($result > 0 && ! empty($this->table_element_line)) $this->fetchLines();
		return $result;
	}

	/**
	 * Load object lines in memory from the database
	 *
	 * @return int         <0 if KO, 0 if not found, >0 if OK
	 */
	/*public function fetchLines()
	{
		$this->lines=array();

		// Load lines with object BuildingPermitLine

		return count($this->lines)?1:0;
	}*/

	/**
	 * Update object into database
	 *
	 * @param  User $user      User that modifies
	 * @param  bool $notrigger false=launch triggers after, true=disable triggers
	 * @return int             <0 if KO, >0 if OK
	 */
	public function update(User $user, $notrigger = false)
	{
		return $this->updateCommon($user, $notrigger);
	}

	/**
	 * Delete object in database
	 *
	 * @param User $user       User that deletes
	 * @param bool $notrigger  false=launch triggers after, true=disable triggers
	 * @return int             <0 if KO, >0 if OK
	 */
	public function delete(User $user, $notrigger = false)
	{
		return $this->deleteCommon($user, $notrigger);
	}

	/**
	 *  Return a link to the object card (with optionaly the picto)
	 *
	 *	@param	int		$withpicto					Include picto in link (0=No picto, 1=Include picto into link, 2=Only picto)
	 *	@param	string	$option						On what the link point to ('nolink', ...)
     *  @param	int  	$notooltip					1=Disable tooltip
     *  @param  string  $morecss            		Add more css on link
     *  @param  int     $save_lastsearch_value    	-1=Auto, 0=No save of lastsearch_values when clicking, 1=Save lastsearch_values whenclicking
	 *	@return	string								String with URL
	 */
	function getNomUrl($withpicto=0, $option='', $notooltip=0, $morecss='', $save_lastsearch_value=-1)
	{
		global $db, $conf, $langs;
        global $dolibarr_main_authentication, $dolibarr_main_demo;
        global $menumanager;

        if (! empty($conf->dol_no_mouse_hover)) $notooltip=1;   // Force disable tooltips

        $result = '';
        $companylink = '';

        $label = '<u>' . $langs->trans("BuildingPermit") . '</u>';
        $label.= '<br>';
        $label.= '<b>' . $langs->trans('Ref') . ':</b> ' . $this->ref;

        $url = dol_buildpath('/amhppermits/buildingpermit_card.php',1).'?id='.$this->id;

        if ($option != 'nolink')
        {
	        // Add param to save lastsearch_values or not
	        $add_save_lastsearch_values=($save_lastsearch_value == 1 ? 1 : 0);
	        if ($save_lastsearch_value == -1 && preg_match('/list\.php/',$_SERVER["PHP_SELF"])) $add_save_lastsearch_values=1;
	        if ($add_save_lastsearch_values) $url.='&save_lastsearch_values=1';
        }

        $linkclose='';
        if (empty($notooltip))
        {
            if (! empty($conf->global->MAIN_OPTIMIZEFORTEXTBROWSER))
            {
                $label=$langs->trans("ShowBuildingPermit");
                $linkclose.=' alt="'.dol_escape_htmltag($label, 1).'"';
            }
            $linkclose.=' title="'.dol_escape_htmltag($label, 1).'"';
            $linkclose.=' class="classfortooltip'.($morecss?' '.$morecss:'').'"';
        }
        else $linkclose = ($morecss?' class="'.$morecss.'"':'');

		$linkstart = '<a href="'.$url.'"';
		$linkstart.=$linkclose.'>';
		$linkend='</a>';

		$result .= $linkstart;
		if ($withpicto) $result.=img_object(($notooltip?'':$label), ($this->picto?$this->picto:'generic'), ($notooltip?(($withpicto != 2) ? 'class="paddingright"' : ''):'class="'.(($withpicto != 2) ? 'paddingright ' : '').'classfortooltip"'), 0, 0, $notooltip?0:1);
		if ($withpicto != 2) $result.= $this->ref;
		$result .= $linkend;
		//if ($withpicto != 2) $result.=(($addlabel && $this->label) ? $sep . dol_trunc($this->label, ($addlabel > 1 ? $addlabel : 0)) : '');

		return $result;
	}

	/**
	 *  Retourne le libelle du status d'un user (actif, inactif)
	 *
	 *  @param	int		$mode          0=libelle long, 1=libelle court, 2=Picto + Libelle court, 3=Picto, 4=Picto + Libelle long, 5=Libelle court + Picto
	 *  @return	string 			       Label of status
	 */
	function getLibStatut($mode=0)
	{
		return $this->LibStatut($this->status,$mode);
	}

	/**
	 *  Return the status
	 *
	 *  @param	int		$status        	Id status
	 *  @param  int		$mode          	0=long label, 1=short label, 2=Picto + short label, 3=Picto, 4=Picto + long label, 5=Short label + Picto, 6=Long label + Picto
	 *  @return string 			       	Label of status
	 */
	static function LibStatut($status,$mode=0)
	{
		global $langs;

		if ($mode == 0)
		{
			$prefix='';
			if ($status == 1) return $langs->trans('Enabled');
			if ($status == 0) return $langs->trans('Disabled');
		}
		if ($mode == 1)
		{
			if ($status == 1) return $langs->trans('Enabled');
			if ($status == 0) return $langs->trans('Disabled');
		}
		if ($mode == 2)
		{
			if ($status == 1) return img_picto($langs->trans('Enabled'),'statut4').' '.$langs->trans('Enabled');
			if ($status == 0) return img_picto($langs->trans('Disabled'),'statut5').' '.$langs->trans('Disabled');
		}
		if ($mode == 3)
		{
			if ($status == 1) return img_picto($langs->trans('Enabled'),'statut4');
			if ($status == 0) return img_picto($langs->trans('Disabled'),'statut5');
		}
		if ($mode == 4)
		{
			if ($status == 1) return img_picto($langs->trans('Enabled'),'statut4').' '.$langs->trans('Enabled');
			if ($status == 0) return img_picto($langs->trans('Disabled'),'statut5').' '.$langs->trans('Disabled');
		}
		if ($mode == 5)
		{
			if ($status == 1) return $langs->trans('Enabled').' '.img_picto($langs->trans('Enabled'),'statut4');
			if ($status == 0) return $langs->trans('Disabled').' '.img_picto($langs->trans('Disabled'),'statut5');
		}
		if ($mode == 6)
		{
			if ($status == 1) return $langs->trans('Enabled').' '.img_picto($langs->trans('Enabled'),'statut4');
			if ($status == 0) return $langs->trans('Disabled').' '.img_picto($langs->trans('Disabled'),'statut5');
		}
	}

	/**
	 *	Charge les informations d'ordre info dans l'objet commande
	 *
	 *	@param  int		$id       Id of order
	 *	@return	void
	 */
	function info($id)
	{
		$sql = 'SELECT rowid, date_creation as datec, tms as datem,';
		$sql.= ' fk_user_creat, fk_user_modif';
		$sql.= ' FROM '.MAIN_DB_PREFIX.$this->table_element.' as t';
		$sql.= ' WHERE t.rowid = '.$id;
		$result=$this->db->query($sql);
		if ($result)
		{
			if ($this->db->num_rows($result))
			{
				$obj = $this->db->fetch_object($result);
				$this->id = $obj->rowid;
				if ($obj->fk_user_author)
				{
					$cuser = new User($this->db);
					$cuser->fetch($obj->fk_user_author);
					$this->user_creation   = $cuser;
				}

				if ($obj->fk_user_valid)
				{
					$vuser = new User($this->db);
					$vuser->fetch($obj->fk_user_valid);
					$this->user_validation = $vuser;
				}

				if ($obj->fk_user_cloture)
				{
					$cluser = new User($this->db);
					$cluser->fetch($obj->fk_user_cloture);
					$this->user_cloture   = $cluser;
				}

				$this->date_creation     = $this->db->jdate($obj->datec);
				$this->date_modification = $this->db->jdate($obj->datem);
				$this->date_validation   = $this->db->jdate($obj->datev);
			}

			$this->db->free($result);

		}
		else
		{
			dol_print_error($this->db);
		}
	}

	/**
	 * Initialise object with example values
	 * Id must be 0 if object instance is a specimen
	 *
	 * @return void
	 */
	public function initAsSpecimen()
	{
		$this->initAsSpecimenCommon();
	}


	/**
	 * Action executed by scheduler
	 * CAN BE A CRON TASK
	 *
	 * @return	int			0 if OK, <>0 if KO (this function is used also by cron so only 0 is OK)
	 */
	public function doScheduledJob()
	{
		global $conf, $langs;

		$this->output = '';
		$this->error='';

		dol_syslog(__METHOD__, LOG_DEBUG);

		// ...

		return 0;
	}

	/**
	 * Generates action buttons for printing the permit
	 */
	public function showPrintPermitActions()
	{
		global $langs;

		$sql = 'SELECT t.rowid, t.name ';
		$sql .= ' FROM `llx_ea_permittemplates` t ';
		$sql .= ' left join llx_ea_permittemplates_builddepts pb on pb.template_id = t.rowid';
		$sql .= ' left join llx_ea_builddepts b on b.rowid = pb.builddept_id';
		$sql .= ' WHERE b.city_name = \''.$this->buildingdeptcity.'\'';
		$sql .= ' ORDER BY t.buttonorder';
		$result=$this->db->query($sql);
		if ($result)
		{
			$num = $this->db->num_rows($result);
			$i = 0;
			if ($num)
			{
				while ($i < $num)
				{
					$obj = $this->db->fetch_object($result);

					$url = dol_buildpath('/amhppermits/printing/print.php',1).'?tid='.$obj->rowid.'&pid='.$this->id;
					print '<a class="butAction"';
					print ' onclick="var url=\''.$url.'\'; if (event.ctrlKey) url+=\'&dest=I\'; if (event.altKey) url+=\'&names=true\'; window.open(url); return false;"';
					print '>Print ' . $obj->name . '</a>'."\n";
					$i++;
				}
			}
			$this->db->free($result);
		}
		else
		{
			dol_print_error($this->db);
		}
	}

	/**
	 * Overrides method in CommonObject
	 */
	public function createCommon(User $user, $notrigger = false)
	{
		foreach($this->fields as $fkey => $fparams)
		{
			if ($fparams['joinOnUpsert'] && is_array($_POST[$fkey]))
			{
				$join = "";
				foreach($_POST[$fkey] as $k => $v) 
				{
					if ($join != "")
						$join = $join . ",";
					$join = $join . $v;
				}
				$this->$fkey = $join;
			}
		}		
		return parent::createCommon($user, $notrigger);
	}

	/**
	 * Overrides method in CommonObject
	 */
	public function updateCommon(User $user, $notrigger = false)
	{
		foreach($this->fields as $fkey => $fparams)
		{
			if ($fparams['joinOnUpsert'] && is_array($_POST[$fkey]))
			{
				$join = "";
				if (is_array($_POST[$fkey])) {
					foreach($_POST[$fkey] as $k => $v) 
					{
						if ($join != "")
							$join = $join . ",";
						$join = $join . $v;
					}
					$this->$fkey = $join;
				}
			}
		}
		
		return parent::updateCommon($user, $notrigger);
	}

	/**
	 * Overrides method in CommonObject
	 */
	function showOutputField($val, $key, $value, $moreparam='', $keysuffix='', $keyprefix='', $showsize=0)
	{
		global $conf,$langs,$form,$object;

		//dol_syslog(get_class($this).'::showOutputField label='.$val['label'].' type='.$val['type'].' size='.$val['css'].' key='.$key.' value='.$value, LOG_DEBUG);
		if ($key == 'poid') 
		{
			// only if something to display (perf)
			if ($value)
			{
				$po = new EaProductionOrders($this->db);
				$po->fetch($value);
				return '<a href="'.DOL_URL_ROOT.'/custom/amhpestimates/card.php?poid='.$po->POID.'&mainmenu=amhpestimates"> '.$po->PONUMBER.'</a>';
			}

			return '';
		}

		if ($key == 'eid') 
		{
			// only if something to display (perf)
			if ($value)
			{
				$e = new EaEstimate($this->db);
				$e->fetch($value);
				return '<a href="'.DOL_URL_ROOT.'/custom/amhpestimatesv2/card.php/'.$e->id.'?mainmenu=amhpestimatesv2"> '.$e->estimatenum.'</a>';
			}

			return '';
		}

		// Check for building department city customization...
		$template = $this->getCustomFieldsTemplateFolder($object->buildingdeptcity);
		$template = $template . "/{$key}_view.php";
		if (file_exists($template)) 
		{
			include($template);
			return "";
		}
		
		// Check for general customization...
		$template = $this->TEMPLATE_BASE . "/all/{$key}_view.php";
		if (file_exists($template)) 
		{
			include($template);
			return "";
		}

		if ($this->fields[$key]['showOutputRequiresTemplate'])
		{
			print '<div class="amhp.hide"/>';
		}

		return parent::showOutputField($val, $key, $value, $moreparam, $keysuffix, $keyprefix, $showsize);
	}

	function getCustomFieldsTemplateFolder($buildingdeptcity)
	{
		if (!$this->customFields)
			$this->customFields = array();

		if ($this->customFields[$buildingdeptcity])
			return $this->customFields[$buildingdeptcity];

		$templateFolder = "none";

		$sql = 'SELECT customfieldsdir';
		$sql.= ' FROM llx_ea_builddepts as t';
		$sql.= ' WHERE t.city_name = "'.$buildingdeptcity.'"';
		$result=$this->db->query($sql);
		if ($result)
		{
			if ($this->db->num_rows($result))
			{
				$obj = $this->db->fetch_object($result);
				$templateFolder = $obj->customfieldsdir;
			}

			$this->db->free($result);

		}
		else
		{
			dol_print_error($this->db);
		}

		$this->customFields[$buildingdeptcity] = $this->TEMPLATE_BASE . "/$templateFolder";
		return $this->customFields[$buildingdeptcity];
	}

	/**
	 * Overrides method in CommonObject
	 */
	function showInputField($val, $key, $value, $moreparam='', $keysuffix='', $keyprefix='', $showsize=0, $nonewbutton=0)
	{
		global $conf,$langs,$form,$action,$object;

		if ($key == 'poid') 
		{
			if ($action == 'createFromPOID' || $action == 'create' || $value) {
				if ($action == 'edit')
					return $this->showOutputField($val, $key, $value, $moreparam, $keysuffix, $keyprefix);
				else
					return $this->selectForEstimate(new EaProductionOrders($db), 'poid', $value, 1, '', '', '', 'onchange="onChangePOID(this)"');
			}
			else
				return "----";
		}

		if ($key == 'eid') 
		{
			if ($action == 'createFromEID' || $action == 'create' || $value) {
				if ($action == 'edit')
					return $this->showOutputField($val, $key, $value, $moreparam, $keysuffix, $keyprefix);
				else
					return $this->selectForEstimateV2(new EaEstimate($db), 'eid', $value, 1, '', '', '', 'onchange="onChangeEID(this)"');
			}
			else
				return "----";
		}

		if ($key == 'buildingdeptcity') 
		{
			return $this->selectForBuildDeptCity($value);
		}

		if ($key == 'fk_soc')
		{
			return 
				'<input type="text" class="flat minwidth400 maxwidthonsmartphone" name="fk_soc_view" value="'.$value.'" readonly>
				 <input type="hidden" name="fk_soc" id="fk_soc" value="'.$value.'">
				 <input type="hidden" name="label" value="">
				';
		}

		$template = $this->getCustomFieldsTemplateFolder($object->buildingdeptcity);
		$template = $template . "/{$key}_edit.php";
		if (file_exists($template)) 
		{
			include($template);
			return "";
		}
		
		$template = $this->TEMPLATE_BASE . "/all/{$key}_edit.php";
		if (file_exists($template)) 
		{
			include($template);
			return "";
		}
		
		if ($this->fields[$key]['showInputRequiresTemplate'])
		{
			print '<div class="amhp.hide"/>';
			return "";
		}

		if ($this->fields[$key]['hideInput'])
		{
			print '<div class="amhp.hide"/>';
			return "";
		}

		return parent::showInputField($val, $key, $value, $moreparam, $keysuffix, $keyprefix, $showsize, $nonewbutton);
	}

	/**
	 * Output html form to select an estimate.
	 */
	function selectForEstimate($objecttmp, $htmlname, $preselectedvalue, $showempty='', $searchkey='', $placeholder='', $morecss='', $moreparams='', $forcecombo=0, $outputmode=0)
	{
		global $conf, $langs, $user;

		$fieldstoshow='t.PONUMBER,t.CUSTOMERNAME';

		$out='';
		$outarray=array();

		$num=0;

		// Search data
		$sql = "SELECT po.poid as POID, po.ponumber as PONUMBER, tp.nom as CUSTOMERNAME ";
		$sql.= " FROM ".MAIN_DB_PREFIX."ea_po as po";
		$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."societe as tp on (tp.rowid = po.customerId)";
		$sql.= " WHERE po.permitId is null ";
		if ($searchkey != '') $sql.=natural_search(explode(',',$fieldstoshow), $searchkey);

		// Build output string
		$resql=$this->db->query($sql);
		if ($resql)
		{
			if (! $forcecombo)
			{
				include_once DOL_DOCUMENT_ROOT . '/core/lib/ajax.lib.php';
				$out .= ajax_combobox($htmlname, null, (!empty($conf->global->$confkeyforautocompletemode) ? $conf->global->$confkeyforautocompletemode : 0));
			}

			// Construct $out and $outarray
			$out.= '<select id="'.$htmlname.'" class="flat'.($morecss?' '.$morecss:'').'"'.($moreparams?' '.$moreparams:'').' name="'.$htmlname.'">'."\n";

			// Warning: Do not use textifempty = ' ' or '&nbsp;' here, or search on key will search on ' key'. Seems it is no more true with selec2 v4
			$textifempty='&nbsp;';

			//if (! empty($conf->use_javascript_ajax) || $forcecombo) $textifempty='';
			if (! empty($conf->global->$confkeyforautocompletemode))
			{
				if ($showempty && ! is_numeric($showempty)) $textifempty=$langs->trans($showempty);
				else $textifempty.=$langs->trans("All");
			}
			if ($showempty) $out.= '<option value="-1">'.$textifempty.'</option>'."\n";

			$num = $this->db->num_rows($resql);
			$i = 0;
			if ($num)
			{
				while ($i < $num)
				{
					$obj = $this->db->fetch_object($resql);
					$label='';
					$tmparray=explode(',', $fieldstoshow);
					foreach($tmparray as $key => $val)
					{
						$val = preg_replace('/t\./','',$val);
						$label .= (($label && $obj->$val)?' - ':'').$obj->$val;
					}
					if (empty($outputmode))
					{
						if ($preselectedvalue > 0 && $preselectedvalue == $obj->POID)
						{
							$out.= '<option value="'.$obj->POID.'" selected>'.$label.'</option>';
						}
						else
						{
							$out.= '<option value="'.$obj->POID.'">'.$label.'</option>';
						}
					}
					else
					{
						array_push($outarray, array('key'=>$obj->POID, 'value'=>$label, 'label'=>$label));
					}

					$i++;
					if (($i % 10) == 0) $out.="\n";
				}
			}

			$out.= '</select>'."\n";
		}
		else
		{
			dol_print_error($this->db);
		}

		$this->result=array('nbofelement'=>$num);

		if ($outputmode) return $outarray;
		return $out;
	}

	/**
	 * Output html form to select an estimate.
	 */
	function selectForEstimateV2($objecttmp, $htmlname, $preselectedvalue, $showempty='', $searchkey='', $placeholder='', $morecss='', $moreparams='', $forcecombo=0, $outputmode=0)
	{
		global $conf, $langs, $user;

		$fieldstoshow='t.ENUMBER,t.CUSTOMERNAME';

		$out='';
		$outarray=array();

		$num=0;

		// Search data
		$sql = "SELECT e.id as id, e.estimatenum as ENUMBER, tp.nom as CUSTOMERNAME ";
		$sql.= " FROM ".MAIN_DB_PREFIX."ea_estimate as e";
		$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."societe as tp on (tp.rowid = e.customerid)";
		$sql.= " WHERE e.permitId is null ";
		if ($searchkey != '') $sql.=natural_search(explode(',',$fieldstoshow), $searchkey);

		// Build output string
		$resql=$this->db->query($sql);
		if ($resql)
		{
			if (! $forcecombo)
			{
				include_once DOL_DOCUMENT_ROOT . '/core/lib/ajax.lib.php';
				$out .= ajax_combobox($htmlname, null, (!empty($conf->global->$confkeyforautocompletemode) ? $conf->global->$confkeyforautocompletemode : 0));
			}

			// Construct $out and $outarray
			$out.= '<select id="'.$htmlname.'" class="flat'.($morecss?' '.$morecss:'').'"'.($moreparams?' '.$moreparams:'').' name="'.$htmlname.'">'."\n";

			// Warning: Do not use textifempty = ' ' or '&nbsp;' here, or search on key will search on ' key'. Seems it is no more true with selec2 v4
			$textifempty='&nbsp;';

			//if (! empty($conf->use_javascript_ajax) || $forcecombo) $textifempty='';
			if (! empty($conf->global->$confkeyforautocompletemode))
			{
				if ($showempty && ! is_numeric($showempty)) $textifempty=$langs->trans($showempty);
				else $textifempty.=$langs->trans("All");
			}
			if ($showempty) $out.= '<option value="-1">'.$textifempty.'</option>'."\n";

			$num = $this->db->num_rows($resql);
			$i = 0;
			if ($num)
			{
				while ($i < $num)
				{
					$obj = $this->db->fetch_object($resql);
					$label='';
					$tmparray=explode(',', $fieldstoshow);
					foreach($tmparray as $key => $val)
					{
						$val = preg_replace('/t\./','',$val);
						$label .= (($label && $obj->$val)?' - ':'').$obj->$val;
					}
					if (empty($outputmode))
					{
						if ($preselectedvalue > 0 && $preselectedvalue == $obj->id)
						{
							$out.= '<option value="'.$obj->id.'" selected>'.$label.'</option>';
						}
						else
						{
							$out.= '<option value="'.$obj->id.'">'.$label.'</option>';
						}
					}
					else
					{
						array_push($outarray, array('key'=>$obj->id, 'value'=>$label, 'label'=>$label));
					}

					$i++;
					if (($i % 10) == 0) $out.="\n";
				}
			}

			$out.= '</select>'."\n";
		}
		else
		{
			dol_print_error($this->db);
		}

		$this->result=array('nbofelement'=>$num);

		if ($outputmode) return $outarray;
		return $out;
	}
	
	function selectForBuildDeptCity($value)
	{
		return Form::selectarray('buildingdeptcity', $this->builddept_city_array(), strtoupper($value),1,0,1, '', 0, 0, 0, '', 'minwidth200', 1);
	}

	function builddept_city_array()
	{
		global $db;
		$cities = array();

		$sql = "SELECT DISTINCT s.town, s.city_name as city ";
		$sql.= " FROM ".MAIN_DB_PREFIX."ea_builddepts as s";
		$sql.= " WHERE s.city_name is not null";
		$sql.= " AND s.status = 1";
		$sql.= " ORDER by city";
		$resql=$db->query($sql);
		if ($resql)
		{
			while ($row = $db->fetch_object($resql))
			{
				if ($row->city != "")
				//	$cities[$row->city] = $row->town . " / " . $row->city;
					array_push($cities, strtoupper($row->city));

			}
			$db->free($resql);
		}

		return $cities;
	}

}

