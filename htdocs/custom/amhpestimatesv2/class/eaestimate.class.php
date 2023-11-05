<?php
// Put here all includes required by your class file
require_once(DOL_DOCUMENT_ROOT."/core/class/commonobject.class.php");

/**
 *	This class is a CRUD wrapper for accessing the llx_ea_estimate table
 */
class EaEstimate extends CommonObject
{
	var $db;							//!< To store db handler
	var $error;							//!< To return error code (or message)
	var $errors=array();				//!< To return several error codes (or messages)
	var $element='eaestimate';			//!< Id that identify managed objects
	var $table_element='ea_estimate';		//!< Name of table without prefix where object is stored
	var $specimen;

	var $id;
	var $estimatenum;
	var $quotedate;
	var $customerid;
	var $vendor;
	var $vendor_phone;
	var $folio;

	var $deposit_percent;
	var $deposit_percent_with_install;
	var $percent_final_inspection;
	var $warranty_years;
	var $pay_upon_completion;
	var $new_construction_owner_responsability;
	var $status;
	var $status_reason;
	var $approved_date;
	var $rejected_date;
	var $delivered_date;
	var $permitId;
		
	var $defcolor;
	var $defglasscolor;
	var $is_alteration;
	var $is_installation_included;
	var $add_sales_discount;
	var $add_inst_discount;
	var $permits;
	var $salestax;
	var $totalprice;
	var $notes;
	var $public_notes;
  
	// Local copies (not stored in estimate table)
	var $customername;
	var $contactname;
	var $contactphone;
	var $contactmobile;
	var $contactaddress;
	var $customeraddress;
	var $customerzip;
	var $customercity;
	var $customerstate;
	var $customerphone;
	var $customermobile;
	var $customeremail;
	var $folionumber;
	var $reltype;

	/**
	 *  Constructor
	 *
	 *  @param	DoliDb		$db	  Database handler
	 */
	function __construct($db)
	{
		$this->db = $db;
		return 1;
	}

	/**
	 *  Create object into database
	 *
	 *  @return int	  		   	 <0 if KO, Id of created object if OK
	 */
	function create($user)
	{
		global $conf, $langs;
		$error=0;

		// Clean parameters
		if (isset($this->estimatenum)) $this->estimatenum=trim($this->estimatenum);
		if (isset($this->quotedate)) $this->quotedate=trim($this->quotedate);
		if (isset($this->customerid)) $this->customerid=trim($this->customerid);
		if (isset($this->vendor)) $this->vendor=trim($this->vendor);
		if (isset($this->vendor_phone)) $this->vendor_phone=trim($this->vendor_phone);
		if (isset($this->folio)) $this->folio=trim($this->folio);

		if (isset($this->deposit_percent)) $this->deposit_percent=trim($this->deposit_percent);
		if (isset($this->deposit_percent_with_install)) $this->deposit_percent_with_install=trim($this->deposit_percent_with_install);
		if (isset($this->percent_final_inspection)) $this->percent_final_inspection=trim($this->percent_final_inspection);
		if (isset($this->warranty_years)) $this->warranty_years=trim($this->warranty_years);
		if (isset($this->pay_upon_completion)) $this->pay_upon_completion=trim($this->pay_upon_completion);
		if (isset($this->new_construction_owner_responsability)) $this->new_construction_owner_responsability=trim($this->new_construction_owner_responsability);
		if (isset($this->status)) $this->status=trim($this->status);
		if (isset($this->status_reason)) $this->status_reason=trim($this->status_reason);
		if (isset($this->approved_date)) $this->approved_date=trim($this->approved_date);
		if (isset($this->rejected_date)) $this->rejected_date=trim($this->rejected_date);
		if (isset($this->delivered_date)) $this->delivered_date=trim($this->delivered_date);
		if (isset($this->permitId)) $this->permitId=trim($this->permitId);

		if (isset($this->defcolor)) $this->defcolor=trim($this->defcolor);
		if (isset($this->defglasscolor)) $this->defglasscolor=trim($this->defglasscolor);
		if (isset($this->is_alteration)) $this->is_alteration=trim($this->is_alteration);
		if (isset($this->is_installation_included)) $this->is_installation_included=trim($this->is_installation_included);
		if (isset($this->add_sales_discount)) $this->add_sales_discount=trim($this->add_sales_discount);
		if (isset($this->add_inst_discount)) $this->add_inst_discount=trim($this->add_inst_discount);
		if (isset($this->permits)) $this->permits=trim($this->permits);
		if (isset($this->salestax)) $this->salestax=trim($this->salestax);
		if (isset($this->totalprice)) $this->totalprice=trim($this->totalprice);
		if (isset($this->notes)) $this->notes=trim($this->notes);
		if (isset($this->public_notes)) $this->public_notes=trim($this->public_notes);
			
		// Check parameters
		if (! isset($this->estimatenum) || $this->estimatenum == "")
			$this->estimatenum = "create_estimate_numberv2('".getInitialsFor($user)."')";
		else
			$this->estimatenum = "'".$this->db->escape($this->estimatenum)."'";

		if (!$error)
		{
			// Insert request
			$sql = "INSERT INTO ".MAIN_DB_PREFIX."ea_estimate (";
			
			$sql.= "estimatenum,";
			$sql.= "quotedate,";
			$sql.= "customerid,";
			$sql.= "vendor,";
			$sql.= "vendor_phone,";
			$sql.= "folio,";

			$sql.= "deposit_percent,";
			$sql.= "deposit_percent_with_install,";
			$sql.= "percent_final_inspection,";
			$sql.= "warranty_years,";
			$sql.= "pay_upon_completion,";
			$sql.= "new_construction_owner_responsability,";
			$sql.= "status,";
			$sql.= "status_reason,";
			$sql.= "approved_date,";
			$sql.= "rejected_date,";
			$sql.= "delivered_date,";
			$sql.= "permitId,";
			
			$sql.= "defcolor,";
			$sql.= "defglasscolor,";
			$sql.= "is_alteration,";
			$sql.= "is_installation_included,";
			$sql.= "add_sales_discount,";
			$sql.= "add_inst_discount,";
			$sql.= "permits,";
			$sql.= "salestax,";
			$sql.= "totalprice,";
			$sql.= "notes,";
			$sql.= "public_notes";
		
			$sql.= ") VALUES (";

			$sql.= " ".$this->estimatenum.",";
			$sql.= " ".(! isset($this->quotedate)?'NULL':"'".$this->db->escape($this->quotedate)."'").",";
			$sql.= " ".(! isset($this->customerid)?'NULL':"'".$this->db->escape($this->customerid)."'").",";
			$sql.= " ".(! isset($this->vendor)?'NULL':"'".$this->db->escape($this->vendor)."'").",";
			$sql.= " ".(! isset($this->vendor_phone)?'NULL':"'".$this->db->escape($this->vendor_phone)."'").",";
			$sql.= " ".(! isset($this->folio)?'NULL':"'".$this->db->escape($this->folio)."'").",";

			$sql.= " ".(! isset($this->deposit_percent)?'NULL':"'".$this->db->escape($this->deposit_percent)."'").",";
			$sql.= " ".(! isset($this->deposit_percent_with_install)?'NULL':"'".$this->db->escape($this->deposit_percent_with_install)."'").",";
			$sql.= " ".(! isset($this->percent_final_inspection)?'NULL':"'".$this->db->escape($this->percent_final_inspection)."'").",";
			$sql.= " ".(! isset($this->warranty_years)?'NULL':"'".$this->db->escape($this->warranty_years)."'").",";
			$sql.= " ".($this->pay_upon_completion==true?"1":"0").",";
			$sql.= " ".($this->new_construction_owner_responsability==true?"1":"0").",";
			$sql.= " ".(! isset($this->status)?'NULL':"'".$this->db->escape($this->status)."'").",";
			$sql.= " ".(! isset($this->status_reason)?'NULL':"'".$this->db->escape($this->status_reason)."'").",";
			$sql.= " ".(! isset($this->approved_date) || empty($this->approved_date)?'NULL':"'".$this->db->escape($this->approved_date)."'").",";
			$sql.= " ".(! isset($this->rejected_date) || empty($this->rejected_date)?'NULL':"'".$this->db->escape($this->rejected_date)."'").",";
			$sql.= " ".(! isset($this->delivered_date) || empty($this->delivered_date)?'NULL':"'".$this->db->escape($this->delivered_date)."'").",";			
			$sql.= " ".(! isset($this->permitId) || $this->permitId == "0" || $this->permitId == "" ?'NULL':"'".$this->db->escape($this->permitId)."'").",";

			$sql.= " ".(! isset($this->defcolor)?'NULL':"'".$this->db->escape($this->defcolor)."'").",";
			$sql.= " ".(! isset($this->defglasscolor)?'NULL':"'".$this->db->escape($this->defglasscolor)."'").",";
			$sql.= " ".($this->is_alteration==true?"1":"0").",";
			$sql.= " ".($this->is_installation_included==true?"1":"0").",";
			$sql.=" ".(! isset($this->add_sales_discount)?'NULL':"'".$this->db->escape($this->add_sales_discount)."'").",";
			$sql.=" ".(! isset($this->add_inst_discount)?'NULL':"'".$this->db->escape($this->add_inst_discount)."'").",";
			$sql.=" ".(! isset($this->permits)?'NULL':"'".$this->db->escape($this->permits)."'").",";
			$sql.=" ".(! isset($this->salestax)?'NULL':"'".$this->db->escape($this->salestax)."'").",";
			$sql.=" ".(! isset($this->totalprice)?'NULL':"'".$this->db->escape($this->totalprice)."'").",";
			$sql.=" ".(! isset($this->notes)?'NULL':"'".$this->db->escape($this->notes)."',");
			$sql.=" ".(! isset($this->public_notes)?'NULL':"'".$this->db->escape($this->public_notes)."'");

			$sql.= ")";

			$this->db->begin();

			dol_syslog(get_class($this)."::create sql=".$sql, LOG_DEBUG);
			$resql=$this->db->query($sql);
			if (! $resql) { $error++; $this->errors[]="Error ".$this->db->lasterror(); }

			if (! $error)
			{
				$this->id = $this->db->last_insert_id(MAIN_DB_PREFIX."ea_estimate");
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
			return -1;
		}
		else
		{
			$this->db->commit();
			return 1;
		}
	}

	/**
	 *  Load object in memory from the database
	 *
	 *  @param	int		$id	Id object
	 *  @return int		  	<0 if KO, >0 if OK
	 */
	function fetch($id)
	{
		global $langs;
		$sql = "CALL get_estimate(".$id.")";

		dol_syslog(get_class($this)."::fetch sql=".$sql, LOG_DEBUG);
		$resql=$this->db->query($sql);
		if ($resql)
		{
			if ($this->db->num_rows($resql))
			{
				$obj = $this->db->fetch_object($resql);
				$this->ref = $obj->id;
								
				$this->id = $obj->id; 
				$this->estimatenum = $obj->estimatenum; 
				$this->quotedate = $obj->quotedate; 
				$this->vendor = $obj->vendor; 
				$this->vendor_phone = $obj->vendor_phone; 
				$this->customername = $obj->customername; 
				$this->contactname = $obj->contactname;
				$this->contactphone = $obj->contactphone;
				$this->contactmobile = $obj->contactmobile;
				$this->customeraddress = $obj->customeraddress;
				$this->customerzip = $obj->customerzip;
				$this->customercity = $obj->customercity; 
				$this->customerstate = $obj->customerstate; 
				$this->customerphone = $obj->customerphone; 
				$this->customermobile = $obj->customermobile; 
				$this->customeremail = $obj->customeremail; 
				$this->folionumber = $obj->folionumber; 
				$this->reltype = $obj->reltype; 

				$this->deposit_percent = $obj->deposit_percent;
				$this->deposit_percent_with_install = $obj->deposit_percent_with_install;
				$this->percent_final_inspection = $obj->percent_final_inspection;
				$this->warranty_years = $obj->warranty_years;
				$this->pay_upon_completion = $obj->pay_upon_completion==1;
				$this->new_construction_owner_responsability = $obj->new_construction_owner_responsability==1;
				$this->status = $obj->status;
				$this->status_reason = $obj->status_reason;
				$this->approved_date = $obj->approved_date;
				$this->rejected_date = $obj->rejected_date;
				$this->delivered_date = $obj->delivered_date;				
				$this->permitId = $obj->permitId;				

				$this->defcolor = $obj->defcolor; 
				$this->defglasscolor = $obj->defglasscolor; 
				$this->is_alteration = $obj->is_alteration==1; 
				$this->is_installation_included = $obj->is_installation_included==1; 
				$this->customerid = $obj->customerid;
				$this->add_sales_discount = $obj->add_sales_discount;
				$this->add_inst_discount = $obj->add_inst_discount;
				$this->permits = $obj->permits;
				$this->salestax = $obj->salestax;
				$this->totalprice = $obj->totalprice;
				$this->notes = $obj->notes;
				$this->public_notes = $obj->public_notes;
			}
			$this->db->free($resql);
			$this->db->db->next_result(); // Stored procedure returns an extra result set :(
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
	 *  Duplicate an estimate and all it's items
	 *
	 *  @param	int		$id	Id object
	 *  @param	int		$initials	Initials to use to create the estimatenum for the copied estimate
	 *  @return int		The id of the new estimate
	 */
	function duplicate($id, $initials)
	{
		global $langs;
		$newid = 0;
		$sql = "CALL copy_estimate(".$id.",'".$initials."')";

		dol_syslog(get_class($this)."::fetch sql=".$sql, LOG_DEBUG);
		$resql=$this->db->query($sql);
		if ($resql)
		{
			if ($this->db->num_rows($resql))
			{
				$obj = $this->db->fetch_object($resql);
				$newid = $obj->newid;
			}
			$this->db->free($resql);
			$this->db->db->next_result(); // Stored procedure returns an extra result set :(
			return $newid;
		}
		else
		{
	  		$this->error="Error ".$this->db->lasterror();
			dol_syslog(get_class($this)."::fetch ".$this->error, LOG_ERR);
			return -1;
		}
	}

	/**
	 *  Update object into database
	 *
	 *  @return int	 		   	 <0 if KO, >0 if OK
	 */
	function update()
	{
		global $conf, $langs;
		$error=0;

		// Clean parameters
		if (isset($this->estimatenum)) $this->estimatenum=trim($this->estimatenum);
		if (isset($this->quotedate)) $this->quotedate=trim($this->quotedate);
		if (isset($this->customerid)) $this->customerid=trim($this->customerid);
		if (isset($this->vendor)) $this->vendor=trim($this->vendor);
		if (isset($this->vendor_phone)) $this->vendor_phone=trim($this->vendor_phone);
		if (isset($this->folio)) $this->folio=trim($this->folio);

		if (isset($this->deposit_percent)) $this->deposit_percent=trim($this->deposit_percent);
		if (isset($this->deposit_percent_with_install)) $this->deposit_percent_with_install=trim($this->deposit_percent_with_install);
		if (isset($this->percent_final_inspection)) $this->percent_final_inspection=trim($this->percent_final_inspection);
		if (isset($this->warranty_years)) $this->warranty_years=trim($this->warranty_years);
		$this->pay_upon_completion=isset($this->pay_upon_completion)?$this->pay_upon_completion:false;
		$this->new_construction_owner_responsability=isset($this->new_construction_owner_responsability)?$this->new_construction_owner_responsability:false;
		if (isset($this->status)) $this->status=trim($this->status);
		if (isset($this->status_reason)) $this->status_reason=trim($this->status_reason);
		if (isset($this->approved_date)) $this->approved_date=trim($this->approved_date);
		if (isset($this->rejected_date)) $this->rejected_date=trim($this->rejected_date);
		if (isset($this->delivered_date)) $this->delivered_date=trim($this->delivered_date);
		if (isset($this->permitId)) $this->permitId=trim($this->permitId);

		if (isset($this->defcolor)) $this->defcolor=trim($this->defcolor);
		if (isset($this->defglasscolor)) $this->defglasscolor=trim($this->defglasscolor);
		$this->is_alteration=isset($this->is_alteration)?$this->is_alteration:false;
		$this->is_installation_included=isset($this->is_installation_included)?$this->is_installation_included:false;
		if (isset($this->add_sales_discount)) $this->add_sales_discount=trim($this->add_sales_discount);
		if (isset($this->add_inst_discount)) $this->add_inst_discount=trim($this->add_inst_discount);
		if (isset($this->permits)) $this->permits=trim($this->permits);
		if (isset($this->salestax)) $this->salestax=trim($this->salestax);
		if (isset($this->totalprice)) $this->totalprice=trim($this->totalprice);
		if (isset($this->notes)) $this->notes=trim($this->notes);
		if (isset($this->public_notes)) $this->public_notes=trim($this->public_notes);

		// Check parameters
		// Put here code to add a control on parameters values

		// Update request
		$sql = "UPDATE ".MAIN_DB_PREFIX."ea_estimate SET";
		
		$sql.= " estimatenum=".(isset($this->estimatenum)?"'".$this->db->escape($this->estimatenum)."'":"null").",";
		$sql.= " quotedate=".(strlen($this->quotedate)>0?"'".$this->db->escape($this->quotedate)."'":"null").",";
		$sql.= " customerid=".(isset($this->customerid)?"'".$this->db->escape($this->customerid)."'":"null").",";
		$sql.= " vendor=".(isset($this->vendor)?"'".$this->db->escape($this->vendor)."'":"null").",";
		$sql.= " vendor_phone=".(isset($this->vendor_phone)?"'".$this->db->escape($this->vendor_phone)."'":"null").",";
		$sql.= " folio=".(isset($this->folio)?"'".$this->db->escape($this->folio)."'":"null").",";

		$sql.= " deposit_percent=".(isset($this->deposit_percent)?"'".$this->db->escape($this->deposit_percent)."'":"null").",";
		$sql.= " deposit_percent_with_install=".(isset($this->deposit_percent_with_install)?"'".$this->db->escape($this->deposit_percent_with_install)."'":"null").",";
		$sql.= " percent_final_inspection=".(isset($this->percent_final_inspection)?"'".$this->db->escape($this->percent_final_inspection)."'":"null").",";
		$sql.= " warranty_years=".(isset($this->warranty_years)?"'".$this->db->escape($this->warranty_years)."'":"null").",";
		$sql.= " pay_upon_completion=".($this->pay_upon_completion==true?"1":"0").",";
		$sql.= " new_construction_owner_responsability=".($this->new_construction_owner_responsability==true?"1":"0").",";
		$sql.= " status=".(isset($this->status)?"'".$this->db->escape($this->status)."'":"null").",";
		$sql.= " status_reason=".(isset($this->status_reason)?"'".$this->db->escape($this->status_reason)."'":"null").",";
		$sql.= " approved_date=".(strlen($this->approved_date)>0?"'".$this->db->escape($this->approved_date)."'":"null").",";
		$sql.= " rejected_date=".(strlen($this->rejected_date)>0?"'".$this->db->escape($this->rejected_date)."'":"null").",";
		$sql.= " delivered_date=".(strlen($this->delivered_date)>0?"'".$this->db->escape($this->delivered_date)."'":"null").",";
		$sql.= " permitId=".(isset($this->permitId) && $this->permitId!=""?"'".$this->db->escape($this->permitId)."'":"null").",";

		$sql.= " defcolor=".(isset($this->defcolor)?"'".$this->db->escape($this->defcolor)."'":"null").",";
		$sql.= " defglasscolor=".(isset($this->defglasscolor)?"'".$this->db->escape($this->defglasscolor)."'":"null").",";
		$sql.= " is_alteration=".($this->is_alteration==true?"1":"0").",";
		$sql.= " is_installation_included=".($this->is_installation_included==true?"1":"0").",";
		$sql.= " add_sales_discount=".(isset($this->add_sales_discount)?"'".$this->db->escape($this->add_sales_discount)."'":"null").",";
		$sql.= " add_inst_discount=".(isset($this->add_inst_discount)?"'".$this->db->escape($this->add_inst_discount)."'":"null").",";
		$sql.= " permits=".(isset($this->permits)?"'".$this->db->escape($this->permits)."'":"null").",";
		$sql.= " salestax=".(isset($this->salestax)?"'".$this->db->escape($this->salestax)."'":"null").",";
		$sql.= " totalprice=".(isset($this->totalprice)?"'".$this->db->escape($this->totalprice)."'":"null").",";
		$sql.= " notes=".(isset($this->notes)?"'".$this->db->escape($this->notes)."'":"null").",";
		$sql.= " public_notes=".(isset($this->public_notes)?"'".$this->db->escape($this->public_notes)."'":"null");

		$sql.= " WHERE id=".$this->id;

		$this->db->begin();

		dol_syslog(get_class($this)."::update sql=".$sql, LOG_DEBUG);
		$resql = $this->db->query($sql);
		if (! $resql) { $error++; $this->errors[]="Error ".$this->db->lasterror(); }

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
	 *  Delete object in database. The $user parameter is unused but required by Dolibarr.
	 *
	 *  @return	int					 <0 if KO, >0 if OK
	 */
	function delete($user)
	{
		global $conf, $langs;
		$error=0;

		$this->db->begin();

		if (! $error)
		{
			// Delete Impact Products
			$sql = "DELETE FROM ".MAIN_DB_PREFIX."ea_est_impact WHERE estimateitemid in (select id from ".MAIN_DB_PREFIX."ea_estimate_item where estimateid = ".$this->id.")";
			dol_syslog(get_class($this)."::delete sql=".$sql);
			$resql = $this->db->query($sql);
			if (! $resql) { 
				$error++; 
				$this->errors[]="Error ".$this->db->lasterror(); 
			}

			if (! $error)
			{
				// Delete Hardware
				$sql = "DELETE FROM ".MAIN_DB_PREFIX."ea_est_hardware WHERE estimateitemid in (select id from ".MAIN_DB_PREFIX."ea_estimate_item where estimateid = ".$this->id.")";
				dol_syslog(get_class($this)."::delete sql=".$sql);
				$resql = $this->db->query($sql);
				if (! $resql) { 
					$error++; 
					$this->errors[]="Error ".$this->db->lasterror(); 
				}
			}

			if (! $error)
			{
				// Delete Material
				$sql = "DELETE FROM ".MAIN_DB_PREFIX."ea_est_material WHERE estimateitemid in (select id from ".MAIN_DB_PREFIX."ea_estimate_item where estimateid = ".$this->id.")";
				dol_syslog(get_class($this)."::delete sql=".$sql);
				$resql = $this->db->query($sql);
				if (! $resql) { 
					$error++; 
					$this->errors[]="Error ".$this->db->lasterror(); 
				}
			}

			if (! $error)
			{
				// Delete Design
				$sql = "DELETE FROM ".MAIN_DB_PREFIX."ea_est_design WHERE estimateitemid in (select id from ".MAIN_DB_PREFIX."ea_estimate_item where estimateid = ".$this->id.")";
				dol_syslog(get_class($this)."::delete sql=".$sql);
				$resql = $this->db->query($sql);
				if (! $resql) { 
					$error++; 
					$this->errors[]="Error ".$this->db->lasterror(); 
				}
			}

			if (! $error)
			{
				// Delete items
				$sql = "DELETE FROM ".MAIN_DB_PREFIX."ea_estimate_item where estimateid = ".$this->id;
				dol_syslog(get_class($this)."::delete sql=".$sql);
				$resql = $this->db->query($sql);
				if (! $resql) { 
					$error++; 
					$this->errors[]="Error ".$this->db->lasterror(); 
				}
			}

			if ($error)
			{
				$error++; 
				$this->error .= ($this->error?', ':'');//.$itemObject->error;
			}
			else
			{
				$sql = "DELETE FROM ".MAIN_DB_PREFIX."ea_estimate WHERE id=".$this->id;
				dol_syslog(get_class($this)."::delete sql=".$sql);
				$resql = $this->db->query($sql);
				if (! $resql) { 
					$error++; 
					$this->errors[]="Error ".$this->db->lasterror(); 
				}
			}
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
	 *	@param	int		$fromid	 Id of object to clone
	 * 	@return	int		New id of clone
	 */
	function createFromClone($fromid)
	{
		global $langs;

		$error=0;

		$object=new EaEstimate($this->db);

		$this->db->begin();

		// Load source object
		$object->fetch($fromid);
		$object->id=0;
		$object->ref = $object->id;

		// Create clone
		$result=$object->create();

		// Other options
		if ($result < 0)
		{
			$this->error=$object->error;
			$error++;
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
	 */
	function initAsSpecimen()
	{
		$this->specimen = 1;
		$result = -1;
		$sql = "SELECT";
		$sql.= " max(e.id) as id";		
		$sql.= " FROM ".MAIN_DB_PREFIX."ea_estimate as e";

		$return_arr = array();
		$resql=$this->db->query($sql);
		if ($resql)
		{
			while ($obj = $this->db->fetch_object($resql))
			{
				$result = $this->fetch($obj->id);
				$this->fetchItems();
				break;
			}
		}
		else
		{
			$this->ref = '';
		
			$this->id='1';
			$this->estimatenum='1PD';
			$this->quotedate=date("m/d/Y");
			$this->vendor='pd';
			$this->vendor_phone='123 456 7890';
			$this->customerid='1';
			$this->folio='';

			$this->deposit_percent='0';
			$this->deposit_percent_with_install='0';
			$this->percent_final_inspection='0';
			$this->warranty_years='0';
			$this->pay_upon_completion=false;
			$this->new_construction_owner_responsability=false;
			$this->status='In Progress';
			$this->status_reason='';
			$this->approved_date=NULL;
			$this->rejected_date=NULL;
			$this->delivered_date=NULL;
			$this->permitId=NULL;

			$this->defcolor='WHITE';
			$this->defglasscolor='WHITE';
			$this->is_alteration=false;
			$this->is_installation_included=false;
			$this->add_sales_discount='';
			$this->add_inst_discount='';
			$this->permits='';
			$this->salestax='';
			$this->totalprice=null;
			$this->notes=null;
			$this->public_notes=null;
			$this->customername='';
			$this->contactname='';
			$this->contactphone='';
			$this->contactmobile='';
			$this->contactaddress='';
			$this->customeraddress='';
			$this->customerzip='';
			$this->customercity='';
			$this->customerstate='';
			$this->customerphone='';
			$this->customermobile='';
			$this->customeremail='';
			$this->folionumber='';
			$this->reltype='';
		}

		$this->db->free($resql);

	}

	function fetchItems()
	{
		//$itemObject=new EaProductionOrderItems($this->db);
		//$this->items = $itemObject->fetchByPOID($this->POID);
	}

	/**
	 *	Return label of status (activity, closed)
	 */
	function getLibStatut($mode=0)
	{
		return ""; // Status not used for building departments
	}

	/**
	 *  Load all given estimates
	 */
	function fetchAllById($ids)
	{
		$result = -1;

		$return_arr = array();
		foreach ($ids as $id)
		{
			$e = new EaEstimate($this->db);
			$result = $e->fetch($id);
			if ($result<0)
			{
				$this->error="Error ".$this->db->lasterror();
				break;
			}
			else
				array_push($return_arr,$e);
		}

		return $return_arr;
	}
}
?>
