<?php
include_once DOL_DOCUMENT_ROOT .'/core/modules/DolibarrModules.class.php';

/**
 *  Description and activation class for module AMHPESTIMATESV2
 */
class modAMHPESTIMATESV2 extends DolibarrModules
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
		$this->numero = 502506;		// TODO Go on page https://wiki.dolibarr.org/index.php/List_of_modules_id to reserve id number for your module
		// Key text used to identify module (for permissions, menus, etc...)
		$this->rights_class = 'amhpestimatesv2';

		// Family can be 'crm','financial','hr','projects','products','ecm','technic','interface','other'
		// It is used to group modules by family in module setup page
		$this->family = "amhp";
		// Module position in the family
		$this->module_position = 16;
		// Gives the possibility to the module, to provide his own family info and position of this family (Overwrite $this->family and $this->module_position. Avoid this)
		//$this->familyinfo = array('amhpestimatesv2' => array('position' => '002', 'label' => $langs->trans("AMHPFamily")));

		// Module label (no space allowed), used if translation string 'AMHPESTIMATESV2Name' not found
		$this->name = preg_replace('/^mod/i','',get_class($this));
		// Module description, used if translation string 'AMHPESTIMATESV2Desc' not found.
		$this->description = $langs->trans("AMHPESTIMATESV2Desc");
		// Used only if file README.md and README-LL.md not found.
		$this->descriptionlong = "A&M Estimates";

		$this->editor_name = 'Paul Dermody';
		$this->editor_url = 'https://www.elementalley.com';

		// Possible values for version are: 'development', 'experimental', 'dolibarr', 'dolibarr_deprecated' or a version string like 'x.y.z'
		$this->version = '1.0';
		// Key used in llx_const table to save module status enabled/disabled (where MYMODULE is value of property name of module in uppercase)
		$this->const_name = 'MAIN_MODULE_'.strtoupper($this->name);
		// Name of image file used for this module.
		// If file is in theme/yourtheme/img directory under name object_pictovalue.png, use this->picto='pictovalue'
		// If file is in module/img directory under name object_pictovalue.png, use this->picto='pictovalue@module'
		$this->picto='amhplogo@amhpestimatesv2';

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
									'css' => array('/amhpestimatesv2/css/amhpestimatesv2.css.php'),	// Set this to relative path of css file if module has its own css file
	 								'js' => array(),                                    // Set this to relative path of js file if module must load a js on all pages
									'hooks' => array('thirdpartycard','estimatesv2tptab','commcard','projectthirdparty','consumptionthirdparty','thirdpartybancard','thirdpartynotification','agendathirdparty') 	// Set here all hooks context managed by module. You can also set hook context 'all'
		                        );

		// Data directories to create when module is enabled.
		// Example: this->dirs = array("/amhp/temp","/amhp/subdir");
		$this->dirs = array();

		// Dependencies
		$this->hidden = false;			// A condition to hide module
		$this->depends = array('modAMHP');		// List of module class names as string that must be enabled if this module is enabled
		$this->requiredby = array();	// List of module ids to disable if this one is disabled
		$this->conflictwith = array();	// List of module class names as string this module is in conflict with
		$this->phpmin = array(5,3);					// Minimum version of PHP required by module
		$this->need_dolibarr_version = array(4,0);	// Minimum version of Dolibarr required by module
		$this->langfiles = array("amhpestimatesv2@amhpestimatesv2");
		$this->warnings_activation = array();                     // Warning to show when we activate module. array('always'='text') or array('FR'='textfr','ES'='textes'...)
		$this->warnings_activation_ext = array();                 // Warning to show when we activate an external module. array('always'='text') or array('FR'='textfr','ES'='textes'...)

		// Constants
		// List of particular constants to add when module is enabled (key, 'chaine', value, desc, visible, 'current' or 'allentities', deleteonunactive)
		// Example: $this->const=array(0=>array('MYMODULE_MYNEWCONST1','chaine','myvalue','This is a constant to add',1),
		//                             1=>array('MYMODULE_MYNEWCONST2','chaine','myvalue','This is another constant to add',0, 'current', 1)
		// );
		$this->const = array(
		);

		// Array to add new pages in new tabs
		// Example: $this->tabs = array('objecttype:+tabname1:Title1:mylangfile@amhpestimatesv2:$user->rights->amhpestimatesv2->read:/amhpestimatesv2/mynewtab1.php?id=__ID__',  					// To add a new tab identified by code tabname1
        //                              'objecttype:+tabname2:SUBSTITUTION_Title2:mylangfile@amhpestimatesv2:$user->rights->othermodule->read:/amhpestimatesv2/mynewtab2.php?id=__ID__',  	// To add another new tab identified by code tabname2. Label will be result of calling all substitution functions on 'Title2' key.
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
        $this->tabs = array('thirdparty:+Estmates V2:estimatesv2:amhpestimatesv2@amhpestimatesv2:$user->rights->amhpestimatesv2->estimates->read:/custom/amhpestimatesv2/thirdparty/estimatesv2.php?socid=__ID__');

		if (! isset($conf->amhpestimatesv2) || ! isset($conf->amhpestimatesv2->enabled))
        {
        	$conf->amhpestimatesv2=new stdClass();
        	$conf->amhpestimatesv2->enabled=0;
        }

        $this->dictionaries=array();


        // Boxes/Widgets
		// Add here list of php file(s) stored in amhpestimatesv2/core/boxes that contains class to show a widget.
        $this->boxes = array(
			0=>array('file'=>'amhpestimatesv2.php@amhpestimatesv2','enabledbydefaulton'=>'Home'),
        	// 1=>array('file'=>'amhpwidget2.php@amhp','note'=>'Widget provided by AMHP'),
        	// 2=>array('file'=>'amhpwidget3.php@amhpestimatesv2','note'=>'Widget provided by AMHP')
        );


		// Cronjobs (List of cron jobs entries to add when module is enabled)
		// $this->cronjobs = array(
			// 0=>array('label'=>'MyJob label', 'jobtype'=>'method', 'class'=>'/amhpestimatesv2/class/amhpestimatesv2myjob.class.php', 'objectname'=>'amhpestimatesv2MyJob', 'method'=>'myMethod', 'parameters'=>'', 'comment'=>'Comment', 'frequency'=>2, 'unitfrequency'=>3600, 'status'=>0, 'test'=>true)
		// );
		// Example: $this->cronjobs=array(0=>array('label'=>'My label', 'jobtype'=>'method', 'class'=>'/dir/class/file.class.php', 'objectname'=>'MyClass', 'method'=>'myMethod', 'parameters'=>'', 'comment'=>'Comment', 'frequency'=>2, 'unitfrequency'=>3600, 'status'=>0, 'test'=>true),
		//                                1=>array('label'=>'My label', 'jobtype'=>'command', 'command'=>'', 'parameters'=>'', 'comment'=>'Comment', 'frequency'=>1, 'unitfrequency'=>3600*24, 'status'=>0, 'test'=>true)
		// );


		// Permissions
		$this->rights = array();		// Permission array used by this module

		$r=0;
		$this->rights[$r][0] = $this->numero + $r;	// Permission id (must not be already used)
		$this->rights[$r][1] = $langs->trans("AMHPestimatesv2RightsReadestimatesv2");	// Permission label
		$this->rights[$r][3] = 1; 					// Permission by default for new user (0/1)
		$this->rights[$r][4] = 'estimates';				// In php code, permission will be checked by test if ($user->rights->amhpestimatesv2->estimates->level2)
		$this->rights[$r][5] = 'read';				    // In php code, permission will be checked by test if ($user->rights->amhpestimatesv2->estimates->level2)

		$r++;
		$this->rights[$r][0] = $this->numero + $r;	// Permission id (must not be already used)
		$this->rights[$r][1] = $langs->trans("AMHPestimatesv2RightsUseestimatesv2");	// Permission label
		$this->rights[$r][3] = 1; 					// Permission by default for new user (0/1)
		$this->rights[$r][4] = 'estimates';			// In php code, permission will be checked by test if ($user->rights->amhpestimatesv2->estimates->level2)
		$this->rights[$r][5] = 'create';			// In php code, permission will be checked by test if ($user->rights->amhpestimatesv2->estimates->level2)

		$r++;
		$this->rights[$r][0] = $this->numero + $r;	// Permission id (must not be already used)
		$this->rights[$r][1] = $langs->trans("AMHPestimatesv2RightsUpdateestimatesv2");	// Permission label
		$this->rights[$r][3] = 1; 					// Permission by default for new user (0/1)
		$this->rights[$r][4] = 'estimates';			// In php code, permission will be checked by test if ($user->rights->amhpestimatesv2->estimates->level2)
		$this->rights[$r][5] = 'update';			// In php code, permission will be checked by test if ($user->rights->amhpestimatesv2->estimates->level2)

		// Main menu entries
		$this->menu = array();			// List of menus to add
		$r=35;

		// Add here entries to declare new menus

		// Example to declare a new Top Menu entry and its Left menu entry:
		/* BEGIN MODULEBUILDER TOPMENU */
		$this->menu[]=array('fk_menu'=>'',			                // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
								'type'=>'top',			                // This is a Top menu entry
								'titre'=>$langs->trans("AMHPESTIMATESV2MenuName"),
								'mainmenu'=>'amhpestimatesv2',
								'leftmenu'=>'',
								'url'=>'/custom/amhpestimatesv2/list.php?mainmenu=amhpestimatesv2',
								'langs'=>'amhpestimatesv2@amhpestimatesv2',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
								'position'=>++$r,
								'enabled'=>'$conf->amhpestimatesv2->enabled',	// Define condition to show or hide menu entry. Use '$conf->amhpestimatesv2->enabled' if entry must be visible if module is enabled.
								'perms'=>'$user->rights->amhpestimatesv2->estimates->create',	// Use 'perms'=>'$user->rights->amhpestimatesv2->level1->level2' if you want your menu with a permission rules
								'target'=>'',
								'user'=>0);				                // 0=Menu for internal users, 1=external users, 2=both
		/* END MODULEBUILDER TOPMENU */

		$this->menu[]=array('fk_menu'=>'fk_mainmenu=amhpestimatesv2',
								'type'=>'left',
								'titre'=>$langs->trans("AMHPESTIMATESV2MenuName"),
								'mainmenu'=>'amhpestimatesv2',
								'leftmenu'=>'amhpestimatesv2',
								'url'=>'/custom/amhpestimatesv2/list.php?mainmenu=amhpestimatesv2',
								'langs'=>'amhpestimatesv2@amhpestimatesv2',
								'position'=>++$r,
								'perms'=>'1',
								'enabled'=>'$conf->amhpestimatesv2->enabled',
								'target'=>'',
								'user'=>0);
		$this->menu[]=array('fk_menu'=>'fk_mainmenu=amhpestimatesv2,fk_leftmenu=amhpestimatesv2',
								'type'=>'left',
								'titre'=>$langs->trans("AMHPESTIMATESV2NewPOMenuName"),
								'mainmenu'=>'amhpestimatesv2',
								'leftmenu'=>'',
								'url'=>'/custom/amhpestimatesv2/card.php?mainmenu=amhpestimatesv2',
								'langs'=>'amhpestimatesv2@amhpestimatesv2',
								'position'=>++$r,
								'perms'=>'1',
								'enabled'=>'$conf->amhpestimatesv2->enabled',
								'target'=>'',
								'user'=>0);
		$this->menu[]=array('fk_menu'=>'fk_mainmenu=amhpestimatesv2,fk_leftmenu=amhpestimatesv2',
								'type'=>'left',
								'titre'=>$langs->trans("AMHPESTIMATESV2ListOfPOsMenuName"),
								'mainmenu'=>'amhpestimatesv2',
								'leftmenu'=>'',
								'url'=>'/custom/amhpestimatesv2/list.php?mainmenu=amhpestimatesv2',
								'langs'=>'amhpestimatesv2@amhpestimatesv2',
								'position'=>++$r,
								'perms'=>'1',
								'enabled'=>'$conf->amhpestimatesv2->enabled',
								'target'=>'',
								'user'=>0);
		$this->menu[]=array('fk_menu'=>'fk_mainmenu=amhpestimatesv2,fk_leftmenu=amhpestimatesv2',
								'type'=>'left',
								'titre'=>$langs->trans("AMHPESTIMATESV2DownloadProducts"),
								'mainmenu'=>'amhpestimatesv2',
								'leftmenu'=>'',
								'url'=>'/custom/amhpestimatesv2/db/getProductsCSV.php',
								'langs'=>'amhpestimatesv2@amhpestimatesv2',
								'position'=>++$r,
								'perms'=>'1',
								'enabled'=>'$conf->amhpestimatesv2->enabled',
								'target'=>'',
								'user'=>0);
		//$this->menu[]=array('fk_menu'=>'fk_mainmenu=amhpestimatesv2,fk_leftmenu=amhpestimatesv2',
		//						'type'=>'left',
		//						'titre'=>$langs->trans("AMHPESTIMATESV2NewPOMenuName"),
		//						'mainmenu'=>'amhpestimatesv2',
		//						'leftmenu'=>'',
		//						'url'=>'/custom/amhpestimatesv2/card.php?mainmenu=amhpestimatesv2&action=new',
		//						'langs'=>'amhpestimatesv2@amhpestimatesv2',
		//						'position'=>++$r,
		//						'perms'=>'1',
		//						'enabled'=>'$conf->amhpestimatesv2->enabled',
		//						'target'=>'',
		//						'user'=>0);
		// Example to declare a Left Menu entry into an existing Top menu entry:
		/* BEGIN MODULEBUILDER LEFTMENU MYOBJECT
		$this->menu[$r++]=array(	'fk_menu'=>'fk_mainmenu=amhpestimatesv2',	    // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
								'type'=>'left',			                // This is a Left menu entry
								'titre'=>'List MyObject',
								'mainmenu'=>'amhpestimatesv2',
								'leftmenu'=>'amhpestimatesv2',
								'url'=>'/amhpestimatesv2/myobject_list.php',
								'langs'=>'amhpestimatesv2@amhpestimatesv2',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
								'position'=>1000+$r,
								'enabled'=>'$conf->amhpestimatesv2->enabled',  // Define condition to show or hide menu entry. Use '$conf->amhpestimatesv2->enabled' if entry must be visible if module is enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
								'perms'=>'1',			                // Use 'perms'=>'$user->rights->amhpestimatesv2->level1->level2' if you want your menu with a permission rules
								'target'=>'',
								'user'=>2);				                // 0=Menu for internal users, 1=external users, 2=both
		$this->menu[$r++]=array(	'fk_menu'=>'fk_mainmenu=amhpestimatesv2,fk_leftmenu=amhpestimatesv2',	    // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
								'type'=>'left',			                // This is a Left menu entry
								'titre'=>'New MyObject',
								'mainmenu'=>'amhpestimatesv2',
								'leftmenu'=>'amhpestimatesv2',
								'url'=>'/amhpestimatesv2/myobject_page.php?action=create',
								'langs'=>'amhpestimatesv2@amhpestimatesv2',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
								'position'=>1000+$r,
								'enabled'=>'$conf->amhpestimatesv2->enabled',  // Define condition to show or hide menu entry. Use '$conf->amhpestimatesv2->enabled' if entry must be visible if module is enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
								'perms'=>'1',			                // Use 'perms'=>'$user->rights->amhpestimatesv2->level1->level2' if you want your menu with a permission rules
								'target'=>'',
								'user'=>2);				                // 0=Menu for internal users, 1=external users, 2=both
		END MODULEBUILDER LEFTMENU MYOBJECT */
		
		// Exports
		$r=1;

		// Example:
		/* BEGIN MODULEBUILDER EXPORT MYOBJECT
		$this->export_code[$r]=$this->rights_class.'_'.$r;
		$this->export_label[$r]='amhpestimatesv2';	                         // Translation key (used only if key ExportDataset_xxx_z not found)
        $this->export_enabled[$r]='1';                               // Condition to show export in list (ie: '$user->id==3'). Set to 1 to always show when module is enabled.
        $this->export_icon[$r]='generic:amhpestimatesv2';					 // Put here code of icon then string for translation key of module name
		//$this->export_permission[$r]=array(array("amhpestimatesv2","level1","level2"));
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
		$this->_load_tables('/amhpestimatesv2/sql/');
		$this->create_stored_procedures('/amhpestimatesv2/sql_sp/');

		// Create extrafields
		include_once DOL_DOCUMENT_ROOT.'/core/class/extrafields.class.php';
		$extrafields = new ExtraFields($this->db);
		//$result1=$extrafields->addExtraField('myattr1', "New Attr 1 label", 'boolean', 1, 3, 'thirdparty');
		//$result2=$extrafields->addExtraField('myattr2', "New Attr 2 label", 'string', 1, 10, 'project');

		dol_mkdir(DOL_DATA_ROOT.'/amhpestimatesv2');
		
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

		dol_syslog("AMHPESTIMATESV2::run_sql run sql file ".$sqlfile, LOG_DEBUG);

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
