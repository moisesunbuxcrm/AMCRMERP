<?php
// Put here all includes required by your class file
require_once(DOL_DOCUMENT_ROOT."/core/class/commonobject.class.php");


/**
 *	This class is a CRUD wrapper for accessing the llx_ea_builddepts table
 */
class Eabuilddepts extends CommonObject
{
	var $db;							//!< To store db handler
	var $error;							//!< To return error code (or message)
	var $errors=array();				//!< To return several error codes (or messages)
	var $element='eabuilddepts';			//!< Id that identify managed objects
	var $table_element='ea_builddepts';		//!< Name of table without prefix where object is stored

    var $id;
    
	var $name;
	var $tms='';
	var $datec='';
	var $address;
	var $zip;
	var $town;
	var $state_id;
	var $state_code;
	var $state_name;
	var $country_id;
	var $country_code;
	var $phone;
	var $fax;
	var $url;
	var $email;
	var $note_private;
	var $note_public;
	var $fk_user_creat;
	var $fk_user_modif;
	var $default_lang;
	var $city_code;
	var $city_name;
	var $working_hours;
	var $county;
	var $prop_search_url;

    


    /**
     *  Constructor
     *
     *  @param	DoliDb		$db      Database handler
     */
    function __construct($db)
    {
        $this->db = $db;
        return 1;
    }


    /**
     *  Create object into database
     *
     *  @param	User	$user        User that creates
     *  @param  int		$notrigger   0=launch triggers after, 1=disable triggers
     *  @return int      		   	 <0 if KO, Id of created object if OK
     */
    function create($user, $notrigger=0)
    {
    	global $conf, $langs;
		$error=0;

		// Clean parameters
        
		if (isset($this->name)) $this->name=trim($this->name);
		if (isset($this->address)) $this->address=trim($this->address);
		if (isset($this->zip)) $this->zip=trim($this->zip);
		if (isset($this->town)) $this->town=trim($this->town);
		if (isset($this->state_id)) $this->state_id=trim($this->state_id);
		if (isset($this->country_id)) $this->country_id=trim($this->country_id);
		if (isset($this->country_code)) $this->country_code=trim($this->country_code);
		if (isset($this->phone)) $this->phone=trim($this->phone);
		if (isset($this->fax)) $this->fax=trim($this->fax);
		if (isset($this->url)) $this->url=trim($this->url);
		if (isset($this->email)) $this->email=trim($this->email);
		if (isset($this->note_private)) $this->note_private=trim($this->note_private);
		if (isset($this->note_public)) $this->note_public=trim($this->note_public);
		if (isset($this->fk_user_creat)) $this->fk_user_creat=trim($this->fk_user_creat);
		if (isset($this->fk_user_modif)) $this->fk_user_modif=trim($this->fk_user_modif);
		if (isset($this->default_lang)) $this->default_lang=trim($this->default_lang);
		if (isset($this->city_code)) $this->city_code=trim($this->city_code);
		if (isset($this->city_name)) $this->city_name=trim($this->city_name);
		if (isset($this->working_hours)) $this->working_hours=trim($this->working_hours);
		if (isset($this->county)) $this->county=trim($this->county);
		if (isset($this->prop_search_url)) $this->prop_search_url=trim($this->prop_search_url);

        

		// Check parameters
		// Put here code to add control on parameters values

        // Insert request
		$sql = "INSERT INTO ".MAIN_DB_PREFIX."ea_builddepts(";
		
		$sql.= "nom,";
		$sql.= "datec,";
		$sql.= "address,";
		$sql.= "zip,";
		$sql.= "town,";
		$sql.= "fk_departement,";
		$sql.= "fk_pays,";
		$sql.= "phone,";
		$sql.= "fax,";
		$sql.= "url,";
		$sql.= "email,";
		$sql.= "note_private,";
		$sql.= "note_public,";
		$sql.= "fk_user_creat,";
		$sql.= "fk_user_modif,";
		$sql.= "default_lang,";
		$sql.= "city_code,";
		$sql.= "city_name,";
		$sql.= "working_hours,";
		$sql.= "county,";
		$sql.= "prop_search_url";

		
        $sql.= ") VALUES (";
        
		$sql.= " ".(! isset($this->name)?'NULL':"'".$this->db->escape($this->name)."'").",";
		$sql.= " ".(! isset($this->datec) || dol_strlen($this->datec)==0?'NULL':$this->db->idate($this->datec)).",";
		$sql.= " ".(! isset($this->address)?'NULL':"'".$this->db->escape($this->address)."'").",";
		$sql.= " ".(! isset($this->zip)?'NULL':"'".$this->db->escape($this->zip)."'").",";
		$sql.= " ".(! isset($this->town)?'NULL':"'".$this->db->escape($this->town)."'").",";
		$sql.= " ".(! isset($this->state_id)?'NULL':"'".$this->state_id."'").",";
		$sql.= " ".(! isset($this->country_id)?'NULL':"'".$this->country_id."'").",";
		$sql.= " ".(! isset($this->phone)?'NULL':"'".$this->db->escape($this->phone)."'").",";
		$sql.= " ".(! isset($this->fax)?'NULL':"'".$this->db->escape($this->fax)."'").",";
		$sql.= " ".(! isset($this->url)?'NULL':"'".$this->db->escape($this->url)."'").",";
		$sql.= " ".(! isset($this->email)?'NULL':"'".$this->db->escape($this->email)."'").",";
		$sql.= " ".(! isset($this->note_private)?'NULL':"'".$this->db->escape($this->note_private)."'").",";
		$sql.= " ".(! isset($this->note_public)?'NULL':"'".$this->db->escape($this->note_public)."'").",";
		$sql.= " ".(! isset($this->fk_user_creat)?'NULL':"'".$this->fk_user_creat."'").",";
		$sql.= " ".(! isset($this->fk_user_modif)?'NULL':"'".$this->fk_user_modif."'").",";
		$sql.= " ".(! isset($this->default_lang)?'NULL':"'".$this->db->escape($this->default_lang)."'").",";
		$sql.= " ".(! isset($this->city_code)?'NULL':"'".$this->city_code."'").",";
		$sql.= " ".(! isset($this->city_name)?'NULL':"'".$this->db->escape($this->city_name)."'").",";
		$sql.= " ".(! isset($this->working_hours)?'NULL':"'".$this->db->escape($this->working_hours)."'").",";
		$sql.= " ".(! isset($this->county)?'NULL':"'".$this->db->escape($this->county)."'").",";
		$sql.= " ".(! isset($this->prop_search_url)?'NULL':"'".$this->db->escape($this->prop_search_url)."'")."";

        
		$sql.= ")";

		$this->db->begin();

	   	dol_syslog(get_class($this)."::create sql=".$sql, LOG_DEBUG);
        $resql=$this->db->query($sql);
    	if (! $resql) { $error++; $this->errors[]="Error ".$this->db->lasterror(); }

		if (! $error)
        {
            $this->id = $this->db->last_insert_id(MAIN_DB_PREFIX."ea_builddepts");
			$this->ref = $this->id;
			if (! $notrigger)
			{
	            // Uncomment this and change MYOBJECT to your own tag if you
	            // want this action calls a trigger.

	            //// Call triggers
	            //include_once DOL_DOCUMENT_ROOT . '/core/class/interfaces.class.php';
	            //$interface=new Interfaces($this->db);
	            //$result=$interface->run_triggers('MYOBJECT_CREATE',$this,$user,$langs,$conf);
	            //if ($result < 0) { $error++; $this->errors=$interface->errors; }
	            //// End call triggers
			}
        }

        // Commit or rollback
        if ($error)
		{
			foreach($this->errors as $errmsg)
			{
	            dol_syslog(get_class($this)."::create ".$errmsg, LOG_ERR);
	            $this->error.=($this->error?', '.$errmsg:$errmsg);
			}
			$this->db->rollback();
			return -1*$error;
		}
		else
		{
			$this->db->commit();
            return $this->id;
		}
    }


    /**
     *  Load object in memory from the database
     *
     *  @param	int		$id    Id object
     *  @return int          	<0 if KO, >0 if OK
     */
    function fetch($id)
    {
    	global $langs;
        $sql = "SELECT";
		$sql.= " t.rowid,";
		
		$sql.= " t.nom,";
		$sql.= " t.tms,";
		$sql.= " t.datec,";
		$sql.= " t.address,";
		$sql.= " t.zip,";
		$sql.= " t.town,";
		$sql.= " t.fk_departement as state_id, d.code_departement as state_code, d.nom as state_name,";
		$sql.= " t.fk_pays as country_id, c.code as country_code,";
		$sql.= " t.phone,";
		$sql.= " t.fax,";
		$sql.= " t.url,";
		$sql.= " t.email,";
		$sql.= " t.note_private,";
		$sql.= " t.note_public,";
		$sql.= " t.fk_user_creat,";
		$sql.= " t.fk_user_modif,";
		$sql.= " t.default_lang,";
		$sql.= " t.city_code,";
		$sql.= " t.city_name,";
		$sql.= " t.working_hours,";
		$sql.= " t.county,";
		$sql.= " t.prop_search_url";

		
        $sql.= " FROM ".MAIN_DB_PREFIX."ea_builddepts as t";
        $sql.= " LEFT JOIN ".MAIN_DB_PREFIX."c_country as c ON t.fk_pays = c.rowid";
        $sql.= " LEFT JOIN ".MAIN_DB_PREFIX."c_departements as d ON t.fk_departement = d.rowid";
        $sql.= " WHERE t.rowid = ".$id;

    	dol_syslog(get_class($this)."::fetch sql=".$sql, LOG_DEBUG);
        $resql=$this->db->query($sql);
        if ($resql)
        {
            if ($this->db->num_rows($resql))
            {
                $obj = $this->db->fetch_object($resql);

                $this->id    = $obj->rowid;
				$this->ref = $this->id;
                
				$this->name = $obj->nom;
				$this->tms = $this->db->jdate($obj->tms);
				$this->datec = $this->db->jdate($obj->datec);
				$this->address = $obj->address;
				$this->zip = $obj->zip;
				$this->town = $obj->town;
				$this->state_id = $obj->state_id;
				$this->state_code = $obj->state_code;
				$this->state_name = $obj->state_name;
				$this->country_id = $obj->country_id;
				$this->country_code = $obj->country_code;
				$this->phone = $obj->phone;
				$this->fax = $obj->fax;
				$this->url = $obj->url;
				$this->email = $obj->email;
				$this->note_private = $obj->note_private;
				$this->note_public = $obj->note_public;
				$this->fk_user_creat = $obj->fk_user_creat;
				$this->fk_user_modif = $obj->fk_user_modif;
				$this->default_lang = $obj->default_lang;
				$this->city_code = $obj->city_code;
				$this->city_name = $obj->city_name;
				$this->working_hours = $obj->working_hours;
				$this->county = $obj->county;
				$this->prop_search_url = $obj->prop_search_url;

                
            }
            $this->db->free($resql);

            return 1;
        }
        else
        {
      	    $this->error="Error ".$this->db->lasterror();
            dol_syslog(get_class($this)."::fetch ".$this->error, LOG_ERR);
            return -1;
        }
    }


    /**
     *  Load object in memory from the database
     */
    function fetchByCity($city)
    {
		$result = -1;
        $sql = "SELECT";
		$sql.= " t.rowid";		
        $sql.= " FROM ".MAIN_DB_PREFIX."ea_builddepts as t";
        $sql.= " WHERE t.town = '".$city."'";

        $resql=$this->db->query($sql);
        if ($resql)
        {
            if ($this->db->num_rows($resql))
            {
                $obj = $this->db->fetch_object($resql);

                $result = $this->fetch($obj->rowid);
				if ($result<0)
					$this->error="Error ".$this->db->lasterror();
            }
        }
        else
        {
      	    $this->error="Error ".$this->db->lasterror();
            dol_syslog(get_class($this)."::fetch ".$this->error, LOG_ERR);
            $result = -1;
        }
		$this->db->free($resql);

		return $result;
    }

    /**
     *  Load object in memory from the database
     */
    function fetchByCityName($cityname)
    {
		$result = -1;
        $sql = "SELECT";
		$sql.= " t.rowid";		
        $sql.= " FROM ".MAIN_DB_PREFIX."ea_builddepts as t";
        $sql.= " WHERE t.city_name = '".$cityname."'";

        $resql=$this->db->query($sql);
        if ($resql)
        {
            if ($this->db->num_rows($resql))
            {
                $obj = $this->db->fetch_object($resql);

                $result = $this->fetch($obj->rowid);
				if ($result<0)
					$this->error="Error ".$this->db->lasterror();
            }
        }
        else
        {
      	    $this->error="Error ".$this->db->lasterror();
            dol_syslog(get_class($this)."::fetch ".$this->error, LOG_ERR);
            $result = -1;
        }
		$this->db->free($resql);

		return $result;
    }


    /**
     *  Update object into database
     *
     *  @param	User	$user        User that modifies
     *  @param  int		$notrigger	 0=launch triggers after, 1=disable triggers
     *  @return int     		   	 <0 if KO, >0 if OK
     */
    function update($user=0, $notrigger=0)
    {
    	global $conf, $langs;
		$error=0;

		// Clean parameters
        
		if (isset($this->name)) $this->name=trim($this->name);
		if (isset($this->address)) $this->address=trim($this->address);
		if (isset($this->zip)) $this->zip=trim($this->zip);
		if (isset($this->town)) $this->town=trim($this->town);
		if (isset($this->state_id)) $this->state_id=trim($this->state_id);
		if (isset($this->country_id)) $this->country_id=trim($this->country_id);
		if (isset($this->country_code)) $this->country_code=trim($this->country_code);
		if (isset($this->phone)) $this->phone=trim($this->phone);
		if (isset($this->fax)) $this->fax=trim($this->fax);
		if (isset($this->url)) $this->url=trim($this->url);
		if (isset($this->email)) $this->email=trim($this->email);
		if (isset($this->note_private)) $this->note_private=trim($this->note_private);
		if (isset($this->note_public)) $this->note_public=trim($this->note_public);
		if (isset($this->fk_user_creat)) $this->fk_user_creat=trim($this->fk_user_creat);
		if (isset($this->fk_user_modif)) $this->fk_user_modif=trim($this->fk_user_modif);
		if (isset($this->default_lang)) $this->default_lang=trim($this->default_lang);
		if (isset($this->city_code)) $this->city_code=trim($this->city_code);
		if (isset($this->city_name)) $this->city_name=trim($this->city_name);
		if (isset($this->working_hours)) $this->working_hours=trim($this->working_hours);
		if (isset($this->county)) $this->county=trim($this->county);
		if (isset($this->prop_search_url)) $this->prop_search_url=trim($this->prop_search_url);

        

		// Check parameters
		// Put here code to add a control on parameters values

        // Update request
        $sql = "UPDATE ".MAIN_DB_PREFIX."ea_builddepts SET";
        
		$sql.= " nom=".(isset($this->name)?"'".$this->db->escape($this->name)."'":"null").",";
		$sql.= " tms=".(dol_strlen($this->tms)!=0 ? "'".$this->db->idate($this->tms)."'" : 'null').",";
		$sql.= " datec=".(dol_strlen($this->datec)!=0 ? "'".$this->db->idate($this->datec)."'" : 'null').",";
		$sql.= " address=".(isset($this->address)?"'".$this->db->escape($this->address)."'":"null").",";
		$sql.= " zip=".(isset($this->zip)?"'".$this->db->escape($this->zip)."'":"null").",";
		$sql.= " town=".(isset($this->town)?"'".$this->db->escape($this->town)."'":"null").",";
		$sql.= " fk_departement=".(isset($this->state_id)?$this->state_id:"null").",";
		$sql.= " fk_pays=".(isset($this->country_id)?$this->country_id:"null").",";
		$sql.= " phone=".(isset($this->phone)?"'".$this->db->escape($this->phone)."'":"null").",";
		$sql.= " fax=".(isset($this->fax)?"'".$this->db->escape($this->fax)."'":"null").",";
		$sql.= " url=".(isset($this->url)?"'".$this->db->escape($this->url)."'":"null").",";
		$sql.= " email=".(isset($this->email)?"'".$this->db->escape($this->email)."'":"null").",";
		$sql.= " note_private=".(isset($this->note_private)?"'".$this->db->escape($this->note_private)."'":"null").",";
		$sql.= " note_public=".(isset($this->note_public)?"'".$this->db->escape($this->note_public)."'":"null").",";
		$sql.= " fk_user_creat=".(isset($this->fk_user_creat)?$this->fk_user_creat:"null").",";
		$sql.= " fk_user_modif=".(isset($this->fk_user_modif)?$this->fk_user_modif:"null").",";
		$sql.= " default_lang=".(isset($this->default_lang)?"'".$this->db->escape($this->default_lang)."'":"null").",";
		$sql.= " city_code=".(isset($this->city_code)?"'".$this->city_code."'":"null").",";
		$sql.= " city_name=".(isset($this->city_name)?"'".$this->db->escape($this->city_name)."'":"null").",";
		$sql.= " working_hours=".(isset($this->working_hours)?"'".$this->db->escape($this->working_hours)."'":"null").",";
		$sql.= " county=".(isset($this->county)?"'".$this->db->escape($this->county)."'":"null").",";
		$sql.= " prop_search_url=".(isset($this->prop_search_url)?"'".$this->db->escape($this->prop_search_url)."'":"null")."";

        
        $sql.= " WHERE rowid=".$this->id;

		print $sql;
		$this->db->begin();

		dol_syslog(get_class($this)."::update sql=".$sql, LOG_DEBUG);
        $resql = $this->db->query($sql);
    	if (! $resql) { $error++; $this->errors[]="Error ".$this->db->lasterror(); }

		if (! $error)
		{
			if (! $notrigger)
			{
	            // Uncomment this and change MYOBJECT to your own tag if you
	            // want this action calls a trigger.

	            //// Call triggers
	            //include_once DOL_DOCUMENT_ROOT . '/core/class/interfaces.class.php';
	            //$interface=new Interfaces($this->db);
	            //$result=$interface->run_triggers('MYOBJECT_MODIFY',$this,$user,$langs,$conf);
	            //if ($result < 0) { $error++; $this->errors=$interface->errors; }
	            //// End call triggers
	    	}
		}

        // Commit or rollback
		if ($error)
		{
			foreach($this->errors as $errmsg)
			{
	            dol_syslog(get_class($this)."::update ".$errmsg, LOG_ERR);
	            $this->error.=($this->error?', '.$errmsg:$errmsg);
			}
			$this->db->rollback();
			return -1*$error;
		}
		else
		{
			$this->db->commit();
			return 1;
		}
    }


 	/**
	 *  Delete object in database
	 *
     *	@param  User	$user        User that deletes
     *  @param  int		$notrigger	 0=launch triggers after, 1=disable triggers
	 *  @return	int					 <0 if KO, >0 if OK
	 */
	function delete($user, $notrigger=0)
	{
		global $conf, $langs;
		$error=0;

		$this->db->begin();

		if (! $error)
		{
			if (! $notrigger)
			{
				// Uncomment this and change MYOBJECT to your own tag if you
		        // want this action calls a trigger.

		        //// Call triggers
		        //include_once DOL_DOCUMENT_ROOT . '/core/class/interfaces.class.php';
		        //$interface=new Interfaces($this->db);
		        //$result=$interface->run_triggers('MYOBJECT_DELETE',$this,$user,$langs,$conf);
		        //if ($result < 0) { $error++; $this->errors=$interface->errors; }
		        //// End call triggers
			}
		}

		if (! $error)
		{
    		$sql = "DELETE FROM ".MAIN_DB_PREFIX."ea_builddepts";
    		$sql.= " WHERE rowid=".$this->id;

    		dol_syslog(get_class($this)."::delete sql=".$sql);
    		$resql = $this->db->query($sql);
        	if (! $resql) { $error++; $this->errors[]="Error ".$this->db->lasterror(); }
		}

        // Commit or rollback
		if ($error)
		{
			foreach($this->errors as $errmsg)
			{
	            dol_syslog(get_class($this)."::delete ".$errmsg, LOG_ERR);
	            $this->error.=($this->error?', '.$errmsg:$errmsg);
			}
			$this->db->rollback();
			return -1*$error;
		}
		else
		{
			$this->db->commit();
			return 1;
		}
	}



	/**
	 *	Load an object from its id and create a new one in database
	 *
	 *	@param	int		$fromid     Id of object to clone
	 * 	@return	int					New id of clone
	 */
	function createFromClone($fromid)
	{
		global $user,$langs;

		$error=0;

		$object=new Eabuilddepts($this->db);

		$this->db->begin();

		// Load source object
		$object->fetch($fromid);
		$object->id=0;
		$this->ref = $this->id;

		// Clear fields
		// ...

		// Create clone
		$result=$object->create($user);

		// Other options
		if ($result < 0)
		{
			$this->error=$object->error;
			$error++;
		}

		if (! $error)
		{


		}

		// End
		if (! $error)
		{
			$this->db->commit();
			return $object->id;
		}
		else
		{
			$this->db->rollback();
			return -1;
		}
	}


	/**
	 *	Initialise object with example values
	 *	Id must be 0 if object instance is a specimen
	 *
	 *	@return	void
	 */
	function initAsSpecimen()
	{
		$this->id=0;
		$this->ref = $this->id;
		
		$this->name='';
		$this->tms='';
		$this->datec='';
		$this->address='';
		$this->zip='';
		$this->town='';
		$this->state_id='';
		$this->state_code='';
		$this->state_name='';
		$this->country_id='';
		$this->country_code='';
		$this->phone='';
		$this->fax='';
		$this->url='';
		$this->email='';
		$this->note_private='';
		$this->note_public='';
		$this->fk_user_creat='';
		$this->fk_user_modif='';
		$this->default_lang='';
		$this->city_code='';
		$this->city_name='';
		$this->working_hours='';
		$this->county='';
		$this->prop_search_url='';

		
	}

    /**
     *    	Return a link on thirdparty (with picto)
     *
     *		@param	int		$maxlen			          Max length of name
     *      @param	int  	$notooltip		          1=Disable tooltip
     *      @param  int     $save_lastsearch_value    -1=Auto, 0=No save of lastsearch_values when clicking, 1=Save lastsearch_values whenclicking
     *		@return	string					          String with URL
     */
    function getNomUrl($maxlen=0, $notooltip=0, $save_lastsearch_value=-1)
    {
        global $conf, $langs;

        if (! empty($conf->dol_no_mouse_hover)) $notooltip=1;   // Force disable tooltips

        $name=$this->name?$this->name:$this->nom;

        $result=''; $label='';
        $linkstart=''; $linkend='';

		$label.= '<div class="centpercent">';
	    $label.= '<u>' . $langs->trans("ShowCustomer") . '</u>';
        $linkstart = '<a href="'.DOL_URL_ROOT.'/custom/amhp/builddepts/card.php?socid='.$this->id;

        if (! empty($this->name))
        {
            $label.= '<br><b>' . $langs->trans('Name') . ':</b> '. $this->name;
        }

        $label.= '</div>';

        // Add param to save lastsearch_values or not
        $add_save_lastsearch_values=($save_lastsearch_value == 1 ? 1 : 0);
        if ($save_lastsearch_value == -1 && preg_match('/list\.php/',$_SERVER["PHP_SELF"])) $add_save_lastsearch_values=1;
        if ($add_save_lastsearch_values) $linkstart.='&save_lastsearch_values=1';
        $linkstart.='"';

        $linkclose='';
        if (empty($notooltip))
        {
            if (! empty($conf->global->MAIN_OPTIMIZEFORTEXTBROWSER))
            {
                $label=$langs->trans("ShowCompany");
                $linkclose.=' alt="'.dol_escape_htmltag($label, 1).'"';
            }
            $linkclose.= ' title="'.dol_escape_htmltag($label, 1).'"';
            $linkclose.=' class="classfortooltip"';
        }
        $linkstart.=$linkclose.'>';
        $linkend='</a>';

        $result.=$linkstart.($maxlen?dol_trunc($name,$maxlen):$name).$linkend;
		
        return $result;
    }

    /**
     *    	Return a link on city name (with picto)
     *
     *		@param	int		$maxlen			          Max length of name
     *      @param	int  	$notooltip		          1=Disable tooltip
     *      @param  int     $save_lastsearch_value    -1=Auto, 0=No save of lastsearch_values when clicking, 1=Save lastsearch_values whenclicking
     *		@return	string					          String with URL
     */
    function getCityNameUrl($maxlen=0, $notooltip=0, $save_lastsearch_value=-1)
    {
        global $conf, $langs;

        if (! empty($conf->dol_no_mouse_hover)) $notooltip=1;   // Force disable tooltips

        $name=$this->city_name;

        $result=''; $label='';
        $linkstart=''; $linkend='';

		$label.= '<div class="centpercent">';
        $linkstart = '<a href="'.DOL_URL_ROOT.'/custom/amhp/builddepts/card.php?socid='.$this->id;

        if (! empty($this->city_name))
        {
            $label.= '<b>' . $langs->trans('AMHPCityName') . ':</b> '. $this->city_name;
        }

        $label.= '</div>';

        // Add param to save lastsearch_values or not
        $add_save_lastsearch_values=($save_lastsearch_value == 1 ? 1 : 0);
        if ($save_lastsearch_value == -1 && preg_match('/list\.php/',$_SERVER["PHP_SELF"])) $add_save_lastsearch_values=1;
        if ($add_save_lastsearch_values) $linkstart.='&save_lastsearch_values=1';
        $linkstart.='"';

        $linkclose='';
        if (empty($notooltip))
        {
            $linkclose.= ' title="'.dol_escape_htmltag($label, 1).'"';
            $linkclose.=' class="classfortooltip"';
        }
        $linkstart.=$linkclose.'>';
        $linkend='</a>';

        $result.=$linkstart.($maxlen?dol_trunc($name,$maxlen):$name).$linkend;
		
        return $result;
    }

	/**
	 * Return array of tabs to used on pages for third parties cards.
	 */
	function prepare_head()
	{
		global $db, $langs, $conf, $user;
		$h = 0;
		$head = array();

		$head[$h][0] = DOL_URL_ROOT.'/custom/amhp/builddepts/card.php?socid='.$this->id;
		$head[$h][1] = $langs->trans("Card");
		$head[$h][2] = 'card';
		$h++;
		return $head;
	}
	
    /**
     *    	Get a tool tip div for this Building Department
     */
    function getToolTip()
    {
		global $conf, $langs;

		$result=''; $label='';
		$linkstart=''; $linkend='';

        $label = $this->getInfoDiv();
		$linkstart = '<a href="'.DOL_URL_ROOT.'/custom/amhp/builddepts/card.php?socid='.$this->id.'"';

        $linkclose='';
		$linkclose.= ' title="'.dol_escape_htmltag($label, 1).'"';
		$linkclose.=' class="classfortooltip"';
        $linkstart.=$linkclose.'>';
        $linkend='</a>';

        global $user;
        if (! $user->rights->amhp->builddepts->read)
        {
            $linkstart='';
            $linkend='';
        }

        $result.=$linkstart.($maxlen?dol_trunc($name,$maxlen):$name).$linkend;

        return $result;
    }

    /**
     *        Get a div with info for this Building Department
     */
    function getInfoDiv()
    {
        global $conf, $langs;

        $label='';

        $label.= '<div class="centpercent">';

        if (! empty($this->name))
        {
            $label.= '<b>' . $langs->trans('Name') . ':</b> '. $this->name;
        }
        if (! empty($this->city_code))
            $label.= '<br><b>' . $langs->trans('CityCode') . ':</b> '. $this->city_code;
        if (! empty($this->city_name))
            $label.= '<br><b>' . $langs->trans('CityName') . ':</b> '. $this->city_name;
        if (! empty($this->url))
            $label.= '<br><b>' . $langs->trans('Web') . ':</b> '. "<a target='_blank' href='".$this->url."'>".$this->url."</a>";
        if (! empty($this->prop_search_url))
            $label.= '<br><b>' . $langs->trans('PropertySearchUrl') . ':</b> '. "<a target='_blank' href='".$this->prop_search_url."'>".$this->prop_search_url."</a>";

        $label.= '</div>';

        return $label;
    }

	/**
	 *    Return label of status (activity, closed)
	 */
	function getLibStatut($mode=0)
	{
		return ""; // Status not used for building departments
	}
}
?>
