<?php
include_once DOL_DOCUMENT_ROOT .'/core/modules/DolibarrModules.class.php';

/**
 *  Description and activation class for module AMHPESTIMATES
 */
class modAMHPESTIMATES extends DolibarrModules
{
	// @codingStandardsIgnoreEnd
	/**
	 * Constructor. Define names, constants, directories, boxes, permissions
	 *
	 * @param DoliDB $db Database handler
	 */
	public function __construct($db)
	{
        global $langs,$conf;

        $this->db = $db;

		// Id for module (must be unique).
		// Use here a free id (See in Home -> System information -> Dolibarr for list of used modules id).
		$this->numero = 502306;		// TODO Go on page https://wiki.dolibarr.org/index.php/List_of_modules_id to reserve id number for your module
		// Key text used to identify module (for permissions, menus, etc...)
		$this->rights_class = 'amhpestimates';

		// Family can be 'crm','financial','hr','projects','products','ecm','technic','interface','other'
		// It is used to group modules by family in module setup page
		$this->family = "amhp";
		// Module position in the family
		$this->module_position = 16;
		// Gives the possibility to the module, to provide his own family info and position of this family (Overwrite $this->family and $this->module_position. Avoid this)
		//$this->familyinfo = array('amhpestimates' => array('position' => '002', 'label' => $langs->trans("AMHPFamily")));

		// Module label (no space allowed), used if translation string 'AMHPESTIMATESName' not found
		$this->name = preg_replace('/^mod/i','',get_class($this));
		// Module description, used if translation string 'AMHPESTIMATESDesc' not found.
		$this->description = $langs->trans("AMHPESTIMATESDesc");
		// Used only if file README.md and README-LL.md not found.
		$this->descriptionlong = "A&M Estimates";

		$this->editor_name = 'Paul Dermody & German Acosta';
		$this->editor_url = 'https://www.elementalley.com';

		// Possible values for version are: 'development', 'experimental', 'dolibarr', 'dolibarr_deprecated' or a version string like 'x.y.z'
		$this->version = '1.0';
		// Key used in llx_const table to save module status enabled/disabled (where MYMODULE is value of property name of module in uppercase)
		$this->const_name = 'MAIN_MODULE_'.strtoupper($this->name);
		// Name of image file used for this module.
		// If file is in theme/yourtheme/img directory under name object_pictovalue.png, use this->picto='pictovalue'
		// If file is in module/img directory under name object_pictovalue.png, use this->picto='pictovalue@module'
		$this->picto='amhplogo@amhpestimates';

		// Defined all module parts (triggers, login, substitutions, menus, css, etc...)
		// for default path (eg: /amhp/core/xxxxx) (0=disable, 1=enable)
		// for specific path of parts (eg: /amhp/core/modules/barcode)
		// for specific css file (eg: /amhp/css/amhp.css.php)
		$this->module_parts = array(
		                        	'triggers' => 0,                                 	// Set this to 1 if module has its own trigger directory (core/triggers)
									'login' => 0,                                    	// Set this to 1 if module has its own login method directory (core/login)
									'substitutions' => 0,                            	// Set this to 1 if module has its own substitution function file (core/substitutions)
									'menus' => 0,                                    	// Set this to 1 if module has its own menus handler directory (core/menus)
									'theme' => 0,                                    	// Set this to 1 if module has its own theme directory (theme)
		                        	'tpl' => 0,                                      	// Set this to 1 if module overwrite template dir (core/tpl)
									'barcode' => 0,                                  	// Set this to 1 if module has its own barcode directory (core/modules/barcode)
									'models' => 1,                                   	// Set this to 1 if module has its own models directory (core/modules/xxx)
									'css' => array('/amhpestimates/css/amhpestimates.css.php'),	// Set this to relative path of css file if module has its own css file
	 								'js' => array(),                                    // Set this to relative path of js file if module must load a js on all pages
									'hooks' => array('thirdpartycard','estimatestptab','commcard','projectthirdparty','consumptionthirdparty','thirdpartybancard','thirdpartynotification','agendathirdparty') 	// Set here all hooks context managed by module. You can also set hook context 'all'
		                        );

		// Data directories to create when module is enabled.
		// Example: this->dirs = array("/amhp/temp","/amhp/subdir");
		$this->dirs = array();

		// Config pages. Put here list of php page, stored into amhpestimates/admin directory, to use to setup module.
		$this->config_page_url = array("setup.php@amhpestimates");

		// Dependencies
		$this->hidden = false;			// A condition to hide module
		$this->depends = array('modAMHP');		// List of module class names as string that must be enabled if this module is enabled
		$this->requiredby = array('modAMHPPermits');	// List of module ids to disable if this one is disabled
		$this->conflictwith = array();	// List of module class names as string this module is in conflict with
		$this->phpmin = array(5,3);					// Minimum version of PHP required by module
		$this->need_dolibarr_version = array(4,0);	// Minimum version of Dolibarr required by module
		$this->langfiles = array("amhpestimates@amhpestimates");
		$this->warnings_activation = array();                     // Warning to show when we activate module. array('always'='text') or array('FR'='textfr','ES'='textes'...)
		$this->warnings_activation_ext = array();                 // Warning to show when we activate an external module. array('always'='text') or array('FR'='textfr','ES'='textes'...)

		// Constants
		// List of particular constants to add when module is enabled (key, 'chaine', value, desc, visible, 'current' or 'allentities', deleteonunactive)
		// Example: $this->const=array(0=>array('MYMODULE_MYNEWCONST1','chaine','myvalue','This is a constant to add',1),
		//                             1=>array('MYMODULE_MYNEWCONST2','chaine','myvalue','This is another constant to add',0, 'current', 1)
		// );
		$this->const = array(
			array('AMHP_DEFAULT_PERMIT', 						'chaine', '300', 			'Default value for Permit in $', 1, 'allentities', 0),
			array('AMHP_DEFAULT_INSTTIME', 						'chaine', '8', 				'Default value for Installation time in hours', 1, 'allentities', 0),
			array('AMHP_DEFAULT_CHECK50', 						'chaine', 'Y', 				'Default value for Remove 50% (Y/N)', 1, 'allentities', 0),
			array('AMHP_DEFAULT_SIGNATUREREQ', 					'chaine', 'N', 				'Default value for Signature Required (Y/N)', 1, 'allentities', 0),
			array('AMHP_DEFAULT_YEARSWARRANTY', 				'chaine', '10', 			'Default number of years warranty for PO', 1, 'allentities', 0),
			array('AMHP_DEFAULT_CHECK10YEARSWARRANTY', 			'chaine', 'Y', 				'Default state for Years Warranty Checkbox (Y/N)', 1, 'allentities', 0),
			array('AMHP_DEFAULT_LIFETIMEWARRANTY', 				'chaine', 'N', 				'Default state for Lifetime Warranty Checkbox (Y/N)', 1, 'allentities', 0),
			array('AMHP_DEFAULT_CHECKFREEOPENINGCLOSING', 		'chaine', 'N', 				'Default value for Free Opening and Closing (Y/N)', 1, 'allentities', 0),
			array('AMHP_DEFAULT_CHECKNOPAYMENT', 				'chaine', 'Y', 				'Default value for No Payments (Y/N)', 1, 'allentities', 0),
			array('AMHP_DEFAULT_PO_TEMPLATE', 					'chaine', 'po_standard', 	'Template to use for Production Order PDFs', 1, 'allentities', 0),
			array('AMHP_DEFAULT_ESTHTVALUE', 					'chaine', '8', 				'Default value for ESTIMATE+HT', 1, 'allentities', 0),
			array('AMHP_DEFAULT_CHECK10YEARSFREEMAINTENANCE', 	'chaine', 'Y', 				'Default state for 10 Years maintenance Checkbox (Y/N)', 1, 'allentities', 0),
			array('AMHP_DEFAULT_HTVALUE', 						'chaine', '2', 				'Default value for PO + HT', 1, 'allentities', 0),
			array('AMHP_DEFAULT_MATERIAL', 						'chaine', 'ALUMINUM',		'Default value for MATERIAL', 1, 'allentities', 0),
			array('AMHP_DEFAULT_COLOR', 						'chaine', 'WHITE', 			'Default value for COLOR', 1, 'allentities', 0),
			array('AMHP_DEFAULT_SQFEETPRICE', 					'chaine', '14', 			'Default value for Square Feet Price for estimate in $', 1, 'allentities', 0),
			array('AMHP_DEFAULT_PROVIDER_ID', 					'chaine', '3', 				'Default value for PROVIDER id', 1, 'allentities', 0),
			array('AMHP_DEFAULT_LOCKIN', 						'chaine', 'OUT', 			'Default value for LOCKIN', 1, 'allentities', 0),
			array('AMHP_DEFAULT_LOCKSIZE', 						'chaine', '0', 				'Default value for LOCKSIZE', 1, 'allentities', 0),
		);

		// Array to add new pages in new tabs
		// Example: $this->tabs = array('objecttype:+tabname1:Title1:mylangfile@amhpestimates:$user->rights->amhpestimates->read:/amhpestimates/mynewtab1.php?id=__ID__',  					// To add a new tab identified by code tabname1
        //                              'objecttype:+tabname2:SUBSTITUTION_Title2:mylangfile@amhpestimates:$user->rights->othermodule->read:/amhpestimates/mynewtab2.php?id=__ID__',  	// To add another new tab identified by code tabname2. Label will be result of calling all substitution functions on 'Title2' key.
        //                              'objecttype:-tabname:NU:conditiontoremove');                                                     										// To remove an existing tab identified by code tabname
        // Can also be:	$this->tabs = array('data'=>'...', 'entity'=>0);
        //
		// where objecttype can be
		// 'categories_x'	  to add a tab in category view (replace 'x' by type of category (0=product, 1=supplier, 2=customer, 3=member)
		// 'contact'          to add a tab in contact view
		// 'contract'         to add a tab in contract view
		// 'group'            to add a tab in group view
		// 'intervention'     to add a tab in intervention view
		// 'invoice'          to add a tab in customer invoice view
		// 'invoice_supplier' to add a tab in supplier invoice view
		// 'member'           to add a tab in fundation member view
		// 'opensurveypoll'	  to add a tab in opensurvey poll view
		// 'order'            to add a tab in customer order view
		// 'order_supplier'   to add a tab in supplier order view
		// 'payment'		  to add a tab in payment view
		// 'payment_supplier' to add a tab in supplier payment view
		// 'product'          to add a tab in product view
		// 'propal'           to add a tab in propal view
		// 'project'          to add a tab in project view
		// 'stock'            to add a tab in stock view
		// 'thirdparty'       to add a tab in third party view
		// 'user'             to add a tab in user view
        $this->tabs = array('thirdparty:+estimates:Estimates:amhpestimates@amhpestimates:$user->rights->amhpestimates->estimates->read:/custom/amhpestimates/thirdparty/estimates.php?socid=__ID__');

		if (! isset($conf->amhpestimates) || ! isset($conf->amhpestimates->enabled))
        {
        	$conf->amhpestimates=new stdClass();
        	$conf->amhpestimates->enabled=0;
        }

        $this->dictionaries=array(
            'langs'=>'amhpestimates@amhpestimates',
            'tabname'=>array( // List of tables we want to see in the dictionary editor
				MAIN_DB_PREFIX."ea_angulartypes",
				MAIN_DB_PREFIX."ea_itemtypes",
				MAIN_DB_PREFIX."ea_lockins",
				MAIN_DB_PREFIX."ea_locksizes",
				MAIN_DB_PREFIX."ea_materials",
				MAIN_DB_PREFIX."ea_mounts",
				MAIN_DB_PREFIX."ea_producttypes",
				MAIN_DB_PREFIX."ea_stackingdata",
				MAIN_DB_PREFIX."ea_windowtypes"
				),
            'tablib'=>array( // Label of tables
				"AMHP Angular Types",
				"AMHP Item Types",
				"AMHP Lockins",
				"AMHP Lock Sizes",
				"AMHP Materials",
				"AMHP Mounts",
				"AMHP Product Types",
				"AMHP Stacking Data",
				"AMHP Window Types"
			),
            'tabsql'=>array( // Request to select fields
				'SELECT f.id, f.id as rowid, f.name, f.active FROM '.MAIN_DB_PREFIX.'ea_angulartypes as f',
				'SELECT f.id, f.id as rowid, f.name, f.active FROM '.MAIN_DB_PREFIX.'ea_itemtypes as f',
				'SELECT f.id, f.id as rowid, f.name, f.active FROM '.MAIN_DB_PREFIX.'ea_lockins as f',
				'SELECT f.id, f.id as rowid, f.size, f.active FROM '.MAIN_DB_PREFIX.'ea_locksizes as f',
				'SELECT f.id, f.id as rowid, f.name, f.active FROM '.MAIN_DB_PREFIX.'ea_materials as f',
				'SELECT f.id, f.id as rowid, f.name, f.active FROM '.MAIN_DB_PREFIX.'ea_mounts as f',
				'SELECT f.id, f.id as rowid, f.name, f.active FROM '.MAIN_DB_PREFIX.'ea_producttypes as f',
				'SELECT f.stackingid, f.stackingid as rowid, f.chartid, f.blades, f.mo, f.stack, f.track, f.active FROM '.MAIN_DB_PREFIX.'ea_stackingdata as f',
				'SELECT f.id, f.id as rowid, f.name, f.active FROM '.MAIN_DB_PREFIX.'ea_windowtypes as f'
			),	
            'tabsqlsort'=>array( // Sort order
				"id ASC",
				"id ASC",
				"id ASC",
				"id ASC",
				"id ASC",
				"id ASC",
				"id ASC",
				"chartid ASC, blades ASC",
				"id ASC"
			),																					
            'tabfield'=>array( // List of fields (result of select to show dictionary)
				"name",
				"name",
				"name",
				"size",
				"name",
				"name",
				"name",
				"chartid,blades,mo,stack,track",
				"name"
			),																					
            'tabfieldvalue'=>array( // List of fields (list of fields to edit a record)
				"name",
				"name",
				"name",
				"size",
				"name",
				"name",
				"name",
				"chartid,blades,mo,stack,track",
				"name"
			),																				
            'tabfieldinsert'=>array( // List of fields (list of fields for insert)
				"name",
				"name",
				"name",
				"size",
				"name",
				"name",
				"name",
				"chartid,blades,mo,stack,track",
				"name"
			),																			
            'tabrowid'=>array( // Name of columns with primary key (try to always name it 'rowid')
				"id",
				"id",
				"id",
				"id",
				"id",
				"id",
				"id",
				"stackingid",
				"id"
			),
            'tabcond'=>array( // Condition to show each dictionary
				$conf->amhpestimates->enabled,
				$conf->amhpestimates->enabled,
				$conf->amhpestimates->enabled,
				$conf->amhpestimates->enabled,
				$conf->amhpestimates->enabled,
				$conf->amhpestimates->enabled,
				$conf->amhpestimates->enabled,
				$conf->amhpestimates->enabled,
				$conf->amhpestimates->enabled
			)
        );


        // Boxes/Widgets
		// Add here list of php file(s) stored in amhpestimates/core/boxes that contains class to show a widget.
        $this->boxes = array(
			0=>array('file'=>'amhpestimates.php@amhpestimates','enabledbydefaulton'=>'Home'),
        	// 1=>array('file'=>'amhpwidget2.php@amhp','note'=>'Widget provided by AMHP'),
        	// 2=>array('file'=>'amhpwidget3.php@amhpestimates','note'=>'Widget provided by AMHP')
        );


		// Cronjobs (List of cron jobs entries to add when module is enabled)
		// $this->cronjobs = array(
			// 0=>array('label'=>'MyJob label', 'jobtype'=>'method', 'class'=>'/amhpestimates/class/amhpestimatesmyjob.class.php', 'objectname'=>'amhpestimatesMyJob', 'method'=>'myMethod', 'parameters'=>'', 'comment'=>'Comment', 'frequency'=>2, 'unitfrequency'=>3600, 'status'=>0, 'test'=>true)
		// );
		// Example: $this->cronjobs=array(0=>array('label'=>'My label', 'jobtype'=>'method', 'class'=>'/dir/class/file.class.php', 'objectname'=>'MyClass', 'method'=>'myMethod', 'parameters'=>'', 'comment'=>'Comment', 'frequency'=>2, 'unitfrequency'=>3600, 'status'=>0, 'test'=>true),
		//                                1=>array('label'=>'My label', 'jobtype'=>'command', 'command'=>'', 'parameters'=>'', 'comment'=>'Comment', 'frequency'=>1, 'unitfrequency'=>3600*24, 'status'=>0, 'test'=>true)
		// );


		// Permissions
		$this->rights = array();		// Permission array used by this module

		$r=0;
		$this->rights[$r][0] = $this->numero + $r;	// Permission id (must not be already used)
		$this->rights[$r][1] = $langs->trans("AMHPESTIMATESRightsReadEstimates");	// Permission label
		$this->rights[$r][3] = 1; 					// Permission by default for new user (0/1)
		$this->rights[$r][4] = 'estimates';				// In php code, permission will be checked by test if ($user->rights->amhpestimates->level1->level2)
		$this->rights[$r][5] = 'read';				    // In php code, permission will be checked by test if ($user->rights->amhpestimates->level1->level2)

		$r++;
		$this->rights[$r][0] = $this->numero + $r;	// Permission id (must not be already used)
		$this->rights[$r][1] = $langs->trans("AMHPESTIMATESRightsUseEstimates");	// Permission label
		$this->rights[$r][3] = 1; 					// Permission by default for new user (0/1)
		$this->rights[$r][4] = 'estimates';			// In php code, permission will be checked by test if ($user->rights->amhpestimates->level1->level2)
		$this->rights[$r][5] = 'create';			// In php code, permission will be checked by test if ($user->rights->amhpestimates->level1->level2)

		$r++;
		$this->rights[$r][0] = $this->numero + $r;	// Permission id (must not be already used)
		$this->rights[$r][1] = $langs->trans("AMHPESTIMATESRightsUpdateEstimates");	// Permission label
		$this->rights[$r][3] = 1; 					// Permission by default for new user (0/1)
		$this->rights[$r][4] = 'estimates';			// In php code, permission will be checked by test if ($user->rights->amhpestimates->level1->level2)
		$this->rights[$r][5] = 'update';			// In php code, permission will be checked by test if ($user->rights->amhpestimates->level1->level2)

		// Main menu entries
		$this->menu = array();			// List of menus to add
		$r=35;

		// Add here entries to declare new menus

		// Example to declare a new Top Menu entry and its Left menu entry:
		/* BEGIN MODULEBUILDER TOPMENU */
		$this->menu[]=array('fk_menu'=>'',			                // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
								'type'=>'top',			                // This is a Top menu entry
								'titre'=>$langs->trans("AMHPESTIMATESMenuName"),
								'mainmenu'=>'amhpestimates',
								'leftmenu'=>'',
								'url'=>'/custom/amhpestimates/list.php?mainmenu=amhpestimates',
								'langs'=>'amhpestimates@amhpestimates',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
								'position'=>++$r,
								'enabled'=>'$conf->amhpestimates->enabled',	// Define condition to show or hide menu entry. Use '$conf->amhpestimates->enabled' if entry must be visible if module is enabled.
								'perms'=>'$user->rights->amhpestimates->estimates->create',	// Use 'perms'=>'$user->rights->amhpestimates->level1->level2' if you want your menu with a permission rules
								'target'=>'',
								'user'=>0);				                // 0=Menu for internal users, 1=external users, 2=both
		/* END MODULEBUILDER TOPMENU */

		$this->menu[]=array('fk_menu'=>'fk_mainmenu=amhpestimates',
								'type'=>'left',
								'titre'=>$langs->trans("AMHPESTIMATESMenuName"),
								'mainmenu'=>'amhpestimates',
								'leftmenu'=>'amhpestimates',
								'url'=>'/custom/amhpestimates/list.php?mainmenu=amhpestimates',
								'langs'=>'amhpestimates@amhpestimates',
								'position'=>++$r,
								'perms'=>'1',
								'enabled'=>'$conf->amhpestimates->enabled',
								'target'=>'',
								'user'=>0);
		$this->menu[]=array('fk_menu'=>'fk_mainmenu=amhpestimates,fk_leftmenu=amhpestimates',
								'type'=>'left',
								'titre'=>$langs->trans("AMHPESTIMATESNewPOMenuName"),
								'mainmenu'=>'amhpestimates',
								'leftmenu'=>'',
								'url'=>'/custom/amhpestimates/card.php?mainmenu=amhpestimates',
								'langs'=>'amhpestimates@amhpestimates',
								'position'=>++$r,
								'perms'=>'1',
								'enabled'=>'$conf->amhpestimates->enabled',
								'target'=>'',
								'user'=>0);
		$this->menu[]=array('fk_menu'=>'fk_mainmenu=amhpestimates,fk_leftmenu=amhpestimates',
								'type'=>'left',
								'titre'=>$langs->trans("AMHPESTIMATESListOfPOsMenuName"),
								'mainmenu'=>'amhpestimates',
								'leftmenu'=>'',
								'url'=>'/custom/amhpestimates/list.php?mainmenu=amhpestimates',
								'langs'=>'amhpestimates@amhpestimates',
								'position'=>++$r,
								'perms'=>'1',
								'enabled'=>'$conf->amhpestimates->enabled',
								'target'=>'',
								'user'=>0);
		//$this->menu[]=array('fk_menu'=>'fk_mainmenu=amhpestimates,fk_leftmenu=amhpestimates',
		//						'type'=>'left',
		//						'titre'=>$langs->trans("AMHPESTIMATESNewPOMenuName"),
		//						'mainmenu'=>'amhpestimates',
		//						'leftmenu'=>'',
		//						'url'=>'/custom/amhpestimates/card.php?mainmenu=amhpestimates&action=new',
		//						'langs'=>'amhpestimates@amhpestimates',
		//						'position'=>++$r,
		//						'perms'=>'1',
		//						'enabled'=>'$conf->amhpestimates->enabled',
		//						'target'=>'',
		//						'user'=>0);
		// Example to declare a Left Menu entry into an existing Top menu entry:
		/* BEGIN MODULEBUILDER LEFTMENU MYOBJECT
		$this->menu[$r++]=array(	'fk_menu'=>'fk_mainmenu=amhpestimates',	    // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
								'type'=>'left',			                // This is a Left menu entry
								'titre'=>'List MyObject',
								'mainmenu'=>'amhpestimates',
								'leftmenu'=>'amhpestimates',
								'url'=>'/amhpestimates/myobject_list.php',
								'langs'=>'amhpestimates@amhpestimates',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
								'position'=>1000+$r,
								'enabled'=>'$conf->amhpestimates->enabled',  // Define condition to show or hide menu entry. Use '$conf->amhpestimates->enabled' if entry must be visible if module is enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
								'perms'=>'1',			                // Use 'perms'=>'$user->rights->amhpestimates->level1->level2' if you want your menu with a permission rules
								'target'=>'',
								'user'=>2);				                // 0=Menu for internal users, 1=external users, 2=both
		$this->menu[$r++]=array(	'fk_menu'=>'fk_mainmenu=amhpestimates,fk_leftmenu=amhpestimates',	    // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
								'type'=>'left',			                // This is a Left menu entry
								'titre'=>'New MyObject',
								'mainmenu'=>'amhpestimates',
								'leftmenu'=>'amhpestimates',
								'url'=>'/amhpestimates/myobject_page.php?action=create',
								'langs'=>'amhpestimates@amhpestimates',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
								'position'=>1000+$r,
								'enabled'=>'$conf->amhpestimates->enabled',  // Define condition to show or hide menu entry. Use '$conf->amhpestimates->enabled' if entry must be visible if module is enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
								'perms'=>'1',			                // Use 'perms'=>'$user->rights->amhpestimates->level1->level2' if you want your menu with a permission rules
								'target'=>'',
								'user'=>2);				                // 0=Menu for internal users, 1=external users, 2=both
		END MODULEBUILDER LEFTMENU MYOBJECT */
		
		// Exports
		$r=1;

		// Example:
		/* BEGIN MODULEBUILDER EXPORT MYOBJECT
		$this->export_code[$r]=$this->rights_class.'_'.$r;
		$this->export_label[$r]='amhpestimates';	                         // Translation key (used only if key ExportDataset_xxx_z not found)
        $this->export_enabled[$r]='1';                               // Condition to show export in list (ie: '$user->id==3'). Set to 1 to always show when module is enabled.
        $this->export_icon[$r]='generic:amhpestimates';					 // Put here code of icon then string for translation key of module name
		//$this->export_permission[$r]=array(array("amhpestimates","level1","level2"));
        $this->export_fields_array[$r]=array('t.rowid'=>"Id",'t.ref'=>'Ref','t.label'=>'Label','t.datec'=>"DateCreation",'t.tms'=>"DateUpdate");
		$this->export_TypeFields_array[$r]=array('t.rowid'=>'Numeric', 't.ref'=>'Text', 't.label'=>'Label', 't.datec'=>"Date", 't.tms'=>"Date");
		// $this->export_entities_array[$r]=array('t.rowid'=>"company",'s.nom'=>'company','s.address'=>'company','s.zip'=>'company','s.town'=>'company','s.fk_pays'=>'company','s.phone'=>'company','s.siren'=>'company','s.siret'=>'company','s.ape'=>'company','s.idprof4'=>'company','s.code_compta'=>'company','s.code_compta_fournisseur'=>'company','f.rowid'=>"invoice",'f.facnumber'=>"invoice",'f.datec'=>"invoice",'f.datef'=>"invoice",'f.total'=>"invoice",'f.total_ttc'=>"invoice",'f.tva'=>"invoice",'f.paye'=>"invoice",'f.fk_statut'=>'invoice','f.note'=>"invoice",'fd.rowid'=>'invoice_line','fd.description'=>"invoice_line",'fd.price'=>"invoice_line",'fd.total_ht'=>"invoice_line",'fd.total_tva'=>"invoice_line",'fd.total_ttc'=>"invoice_line",'fd.tva_tx'=>"invoice_line",'fd.qty'=>"invoice_line",'fd.date_start'=>"invoice_line",'fd.date_end'=>"invoice_line",'fd.fk_product'=>'product','p.ref'=>'product');
		// $this->export_dependencies_array[$r]=array('invoice_line'=>'fd.rowid','product'=>'fd.rowid');   // To add unique key if we ask a field of a child to avoid the DISTINCT to discard them
		// $this->export_sql_start[$r]='SELECT DISTINCT ';
		// $this->export_sql_end[$r]  =' FROM '.MAIN_DB_PREFIX.'myobject as t';
		// $this->export_sql_order[$r] .=' ORDER BY t.ref';
		// $r++;
		END MODULEBUILDER EXPORT MYOBJECT */

	}

	/**
	 *		Function called when module is enabled.
	 *		The init function add constants, boxes, permissions and menus (defined in constructor) into Dolibarr database.
	 *		It also creates data directories
	 *
     *      @param      string	$options    Options when enabling module ('', 'noboxes')
	 *      @return     int             	1 if OK, 0 if KO
	 */
	public function init($options='')
	{
		$this->_load_tables('/amhpestimates/sql/');
		$this->create_stored_procedures('/amhpestimates/sql_sp/');

		// Create extrafields
		include_once DOL_DOCUMENT_ROOT.'/core/class/extrafields.class.php';
		$extrafields = new ExtraFields($this->db);
		//$result1=$extrafields->addExtraField('myattr1', "New Attr 1 label", 'boolean', 1, 3, 'thirdparty');
		//$result2=$extrafields->addExtraField('myattr2', "New Attr 2 label", 'string', 1, 10, 'project');

		dol_mkdir(DOL_DATA_ROOT.'/amhpestimates');
		
		$sql = array(
			"DELETE FROM ".MAIN_DB_PREFIX."document_model WHERE nom = 'po_standard' AND type = 'amhppo' AND entity = 1",
			"INSERT INTO ".MAIN_DB_PREFIX."document_model (nom, type, entity) VALUES('po_standard','amhppo',1)",
		);

		return $this->_init($sql, $options);
	}

	/**
	 * Function called when module is disabled.
	 * Remove from database constants, boxes and permissions from Dolibarr database.
	 * Data directories are not deleted
	 *
	 * @param      string	$options    Options when enabling module ('', 'noboxes')
	 * @return     int             	1 if OK, 0 if KO
	 */
	public function remove($options = '')
	{
		$sql = array();

		return $this->_remove($sql, $options);
	}

	/**
	 * Create stored procedures and functions.
	 * @param   string  $reldir Relative directory where to scan files
	 * @return  int             <=0 if KO, >0 if OK
	 */
	function create_stored_procedures($reldir)
	{
		global $conf;

		$error=0;
		$dirfound=0;

		if (empty($reldir)) return 1;

		$ok = 1;
		foreach($conf->file->dol_document_root as $dirroot)
		{
			if ($ok)
			{
				$dir = $dirroot.$reldir;
				$ok = 0;

				$handle=@opendir($dir);         // Dir may not exists
				if (is_resource($handle))
				{
					$dirfound++;

					// Run *.sql files
					$files = array();
					while (($file = readdir($handle))!==false)
					{
						$files[] = $file;
					}
					sort($files);
					foreach ($files as $file)
					{
						if (preg_match('/\.sql$/i',$file))
						{
							$result=$this->run_sql($dir.$file,1,'',1);
							if ($result <= 0) $error++;
						}
					}

					closedir($handle);
				}

				if ($error == 0)
				{
					$ok = 1;
				}
			}
		}

		if (! $dirfound) dol_syslog("A module ask to load sql files into ".$reldir." but this directory was not found.", LOG_WARNING);
		return $ok;
	}

		
	/**
	 *	Launch a sql file. 
	*
	*	@param		string	$sqlfile		Full path to sql file
	* 	@return		int						<=0 if KO, >0 if OK
	*/
	function run_sql($sqlfile)
	{
		global $conf, $langs, $user;

		dol_syslog("AMHPESTIMATES::run_sql run sql file ".$sqlfile, LOG_DEBUG);

		$ok=0;
		$error=0;
		$i=0;
		$content = file_get_contents($sqlfile);
		$statements = explode("----", $content);

		foreach($statements as $i => $sql)
		{
			$result=$this->db->query($sql);
			if ($result)
			{
				dol_syslog('Admin.lib::run_sql result='.$result, LOG_DEBUG);
			}
			else
			{
				dol_syslog('Admin.lib::run_sql sql='.$sql, LOG_DEBUG);
				dol_syslog('Admin.lib::run_sql Error '.$this->db->errno()." ".$this->db->error(), LOG_ERR);
				$error++;
				break;
			}
		}

		if ($error == 0)
			$ok = 1;
		else
			$ok = 0;

		return $ok;
	}


}
