<?php
// Put here all includes required by your class file
require_once(DOL_DOCUMENT_ROOT."/core/class/commonobject.class.php");
require_once(DOL_DOCUMENT_ROOT.'/custom/amhpestimates/class/eaproductionorderitems.class.php');


/**
 *	This class is a CRUD wrapper for accessing the llx_ea_po table
 */
class EaProductionOrders extends CommonObject
{
	var $db;							//!< To store db handler
	var $error;							//!< To return error code (or message)
	var $errors=array();				//!< To return several error codes (or messages)
	var $element='eaproductionorders';			//!< Id that identify managed objects
	var $table_element='ea_po';		//!< Name of table without prefix where object is stored
	var $specimen;

	var $POID;
	var $PONUMBER;
	var $PODATE;
	var $QUOTEDATE;
	var $Salesman;
	var $CUSTOMERNAME;
	var $CONTACTNAME;
	var $CONTACTPHONE1;
	var $CONTACTPHONE2;
	var $CUSTOMERADDRESS;
	var $ZIPCODE;
	var $CITY;
	var $STATE;
	var $PHONENUMBER1;
	var $PHONENUMBER2;
	var $FAXNUMBER;
	var $EMail;
	var $COLOR;
	var $HTVALUE;
	var $DESCRIPTIONOFWORK;
	var $OBSERVATION;
	var $TOTALTRACK;
	var $TAPCONS;
	var $TOTALLONG;
	var $FASTENERS;
	var $TOTALALUMINST;
	var $TOTALLINEARFT;
	var $OBSINST;
	var $SQINSTPRICE;
	var $INSTSALESPRICE;
	var $ESTHTVALUE;
	var $ESTOBSERVATION;
	var $INSTTIME;
	var $PERMIT;
	var $CUSTVALUE;
	var $CUSTOMIZE;
	var $SALES_TAX;
	var $SALESTAXAMOUNT;
	var $TOTALALUM;
	var $SALESPRICE;
	var $SQFEETPRICE;
	var $OTHERFEES;
	var $Check50;
	var $CheckAssIns;
	var $OrderCompleted;
	var $Check10YearsWarranty;
	var $Check10YearsFreeMaintenance;
	var $CheckFreeOpeningClosing;
	var $CheckNoPayment;
	var $YearsWarranty;
	var $LifeTimeWarranty;
	var $SignatureReq;
	var $Discount;
	var $customerId;
	var $invoiceId;
	var $invoiceLocked;
	var $permitId;
	var $items;

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
	function create()
	{
		global $conf, $langs;
		$error=0;

		// Clean parameters
		if (isset($this->PONUMBER)) $this->PONUMBER=trim($this->PONUMBER);
		if (isset($this->PODATE)) $this->PODATE=trim($this->PODATE);
		if (isset($this->QUOTEDATE)) $this->QUOTEDATE=trim($this->QUOTEDATE);
		if (isset($this->Salesman)) $this->Salesman=trim($this->Salesman);
		if (isset($this->COLOR)) $this->COLOR=trim($this->COLOR);
		if (isset($this->HTVALUE)) $this->HTVALUE=trim($this->HTVALUE);
		if (isset($this->DESCRIPTIONOFWORK)) $this->DESCRIPTIONOFWORK=trim($this->DESCRIPTIONOFWORK);
		if (isset($this->OBSERVATION)) $this->OBSERVATION=trim($this->OBSERVATION);
		if (isset($this->TOTALTRACK)) $this->TOTALTRACK=trim($this->TOTALTRACK);
		if (isset($this->TAPCONS)) $this->TAPCONS=trim($this->TAPCONS);
		if (isset($this->TOTALLONG)) $this->TOTALLONG=trim($this->TOTALLONG);
		if (isset($this->FASTENERS)) $this->FASTENERS=trim($this->FASTENERS);
		if (isset($this->TOTALALUMINST)) $this->TOTALALUMINST=trim($this->TOTALALUMINST);
		if (isset($this->TOTALLINEARFT)) $this->TOTALLINEARFT=trim($this->TOTALLINEARFT);
		if (isset($this->OBSINST)) $this->OBSINST=trim($this->OBSINST);
		if (isset($this->SQINSTPRICE)) $this->SQINSTPRICE=trim($this->SQINSTPRICE);
		if (isset($this->INSTSALESPRICE)) $this->INSTSALESPRICE=trim($this->INSTSALESPRICE);
		if (isset($this->ESTHTVALUE)) $this->ESTHTVALUE=trim($this->ESTHTVALUE);
		if (isset($this->ESTOBSERVATION)) $this->ESTOBSERVATION=trim($this->ESTOBSERVATION);
		if (isset($this->INSTTIME)) $this->INSTTIME=trim($this->INSTTIME);
		if (isset($this->PERMIT)) $this->PERMIT=trim($this->PERMIT);
		if (isset($this->CUSTVALUE)) $this->CUSTVALUE=trim($this->CUSTVALUE);
		if (isset($this->CUSTOMIZE)) $this->CUSTOMIZE=trim($this->CUSTOMIZE);
		if (isset($this->SALES_TAX)) $this->SALES_TAX=trim($this->SALES_TAX);
		if (isset($this->SALESTAXAMOUNT)) $this->SALESTAXAMOUNT=trim($this->SALESTAXAMOUNT);
		if (isset($this->TOTALALUM)) $this->TOTALALUM=trim($this->TOTALALUM);
		if (isset($this->SALESPRICE)) $this->SALESPRICE=trim($this->SALESPRICE);
		if (isset($this->SQFEETPROCE)) $this->SQFEETPROCE=trim($this->SQFEETPROCE);
		//if (isset($this->Check50)) $this->Check50=trim($this->Check50);
		//if (isset($this->CheckAssIns)) $this->CheckAssIns=trim($this->CheckAssIns);
		//if (isset($this->OrderCompleted)) $this->OrderCompleted=trim($this->OrderCompleted);
		//if (isset($this->Check10YearsWarranty)) $this->Check10YearsWarranty=trim($this->Check10YearsWarranty);
		//if (isset($this->Check10YearsFreeMaintenance)) $this->Check10YearsFreeMaintenance=trim($this->Check10YearsFreeMaintenance);
		//if (isset($this->CheckFreeOpeningClosing)) $this->CheckFreeOpeningClosing=trim($this->CheckFreeOpeningClosing);
		//if (isset($this->CheckNoPayment)) $this->CheckNoPayment=trim($this->CheckNoPayment);
		if (isset($this->YearsWarranty)) $this->YearsWarranty=trim($this->YearsWarranty);
		//if (isset($this->LifeTimeWarranty)) $this->LifeTimeWarranty=trim($this->LifeTimeWarranty);
		//if (isset($this->SignatureReq)) $this->SignatureReq=trim($this->SignatureReq);
		if (isset($this->Discount)) $this->Discount=trim($this->Discount);
		if (isset($this->customerId)) $this->customerId=trim($this->customerId);
		if (isset($this->invoiceId)) $this->invoiceId=trim($this->invoiceId);
		if (isset($this->permitId)) $this->permitId=trim($this->permitId);
		
		// Check parameters
		if (! $this->PONUMBER) { $error++; $this->errors[]="Missing PONUMBER"; }

		if (!$error)
		{
			// Insert request
			$sql = "INSERT INTO ".MAIN_DB_PREFIX."ea_po (";
			
			$sql.= "PONUMBER,";
			$sql.= "PODATE,";
			$sql.= "QUOTEDATE,";
			$sql.= "Salesman,";
			$sql.= "COLOR,";
			$sql.= "HTVALUE,";
			$sql.= "DESCRIPTIONOFWORK,";
			$sql.= "OBSERVATION,";
			$sql.= "TOTALTRACK,";
			$sql.= "TAPCONS,";
			$sql.= "TOTALLONG,";
			$sql.= "FASTENERS,";
			$sql.= "TOTALALUMINST,";
			$sql.= "TOTALLINEARFT,";
			$sql.= "OBSINST,";
			$sql.= "SQINSTPRICE,";
			$sql.= "INSTSALESPRICE,";
			$sql.= "ESTHTVALUE,";
			$sql.= "ESTOBSERVATION,";
			$sql.= "INSTTIME,";
			$sql.= "PERMIT,";
			$sql.= "CUSTVALUE,";
			$sql.= "CUSTOMIZE,";
			$sql.= "SALES_TAX,";
			$sql.= "SALESTAXAMOUNT,";
			$sql.= "TOTALALUM,";
			$sql.= "SALESPRICE,";
			$sql.= "SQFEETPRICE,";
			$sql.= "OTHERFEES,";
			$sql.= "Check50,";
			$sql.= "CheckAssIns,";
			$sql.= "OrderCompleted,";
			$sql.= "Check10YearsWarranty,";
			$sql.= "Check10YearsFreeMaintenance,";
			$sql.= "CheckFreeOpeningClosing,";
			$sql.= "CheckNoPayment,";
			$sql.= "YearsWarranty,";
			$sql.= "LifeTimeWarranty,";
			$sql.= "SignatureReq,";
			$sql.= "Discount,";
			$sql.= "customerId,";
			$sql.= "invoiceId,";
			$sql.= "permitId";

			$sql.= ") VALUES (";

			$sql.= " ".(! isset($this->PONUMBER)?'NULL':"'".$this->db->escape($this->PONUMBER)."'").",";
			$sql.= " ".(! isset($this->PODATE)?'NULL':"'".$this->db->escape($this->PODATE)."'").",";
			$sql.= " ".(! isset($this->QUOTEDATE)?'NULL':"'".$this->db->escape($this->QUOTEDATE)."'").",";
			$sql.= " ".(! isset($this->Salesman)?'NULL':"'".$this->db->escape($this->Salesman)."'").",";
			$sql.= " ".(! isset($this->COLOR)?'NULL':"'".$this->db->escape($this->COLOR)."'").",";
			$sql.= " ".(! isset($this->HTVALUE)?'NULL':"'".$this->db->escape($this->HTVALUE)."'").",";
			$sql.= " ".(! isset($this->DESCRIPTIONOFWORK)?'NULL':"'".$this->db->escape($this->DESCRIPTIONOFWORK)."'").",";
			$sql.= " ".(! isset($this->OBSERVATION)?'NULL':"'".$this->db->escape($this->OBSERVATION)."'").",";
			$sql.= " ".(! isset($this->TOTALTRACK)?'NULL':"'".$this->db->escape($this->TOTALTRACK)."'").",";
			$sql.= " ".(! isset($this->TAPCONS)?'NULL':"'".$this->db->escape($this->TAPCONS)."'").",";
			$sql.= " ".(! isset($this->TOTALLONG)?'NULL':"'".$this->db->escape($this->TOTALLONG)."'").",";
			$sql.= " ".(! isset($this->FASTENERS)?'NULL':"'".$this->db->escape($this->FASTENERS)."'").",";
			$sql.= " ".(! isset($this->TOTALALUMINST)?'NULL':"'".$this->db->escape($this->TOTALALUMINST)."'").",";
			$sql.= " ".(! isset($this->TOTALLINEARFT)?'NULL':"'".$this->db->escape($this->TOTALLINEARFT)."'").",";
			$sql.= " ".(! isset($this->OBSINST)?'NULL':"'".$this->db->escape($this->OBSINST)."'").",";
			$sql.= " ".(! isset($this->SQINSTPRICE)?'NULL':"'".$this->db->escape($this->SQINSTPRICE)."'").",";
			$sql.= " ".(! isset($this->INSTSALESPRICE)?'NULL':"'".$this->db->escape($this->INSTSALESPRICE)."'").",";
			$sql.= " ".(! isset($this->ESTHTVALUE)?'NULL':"'".$this->db->escape($this->ESTHTVALUE)."'").",";
			$sql.= " ".(! isset($this->ESTOBSERVATION)?'NULL':"'".$this->db->escape($this->ESTOBSERVATION)."'").",";
			$sql.= " ".(! isset($this->INSTTIME)?'NULL':"'".$this->db->escape($this->INSTTIME)."'").",";
			$sql.= " ".(! isset($this->PERMIT)?'NULL':"'".$this->db->escape($this->PERMIT)."'").",";
			$sql.= " ".(! isset($this->CUSTVALUE)?'NULL':"'".$this->db->escape($this->CUSTVALUE)."'").",";
			$sql.= " ".(! isset($this->CUSTOMIZE)?'NULL':"'".$this->db->escape($this->CUSTOMIZE)."'").",";
			$sql.= " ".(! isset($this->SALES_TAX)?'NULL':"'".$this->db->escape($this->SALES_TAX)."'").",";
			$sql.= " ".(! isset($this->SALESTAXAMOUNT)?'NULL':"'".$this->db->escape($this->SALESTAXAMOUNT)."'").",";
			$sql.= " ".(! isset($this->TOTALALUM)?'NULL':"'".$this->db->escape($this->TOTALALUM)."'").",";
			$sql.= " ".(! isset($this->SALESPRICE)?'NULL':"'".$this->db->escape($this->SALESPRICE)."'").",";
			$sql.= " ".(! isset($this->SQFEETPRICE)?'NULL':"'".$this->db->escape($this->SQFEETPRICE)."'").",";
			$sql.= " ".(! isset($this->OTHERFEES)?'NULL':"'".$this->db->escape($this->OTHERFEES)."'").",";
			$sql.= " ".($this->Check50==true?"1":"0").",";
			$sql.= " ".($this->CheckAssIns==true?"1":"0").",";
			$sql.= " ".($this->OrderCompleted==true?"1":"0").",";
			$sql.= " ".($this->Check10YearsWarranty==true?"1":"0").",";
			$sql.= " ".($this->Check10YearsFreeMaintenance==true?"1":"0").",";
			$sql.= " ".($this->CheckFreeOpeningClosing==true?"1":"0").",";
			$sql.= " ".($this->CheckNoPayment==true?"1":"0").",";
			$sql.= " ".(! isset($this->YearsWarranty)?'NULL':"'".$this->db->escape($this->YearsWarranty)."'").",";
			$sql.= " ".($this->LifeTimeWarranty==true?"1":"0").",";
			$sql.= " ".($this->SignatureReq==true?"1":"0").",";
			$sql.= " ".(! isset($this->Discount)?'NULL':"'".$this->db->escape($this->Discount)."'").",";
			$sql.= " ".(! isset($this->customerId)?'NULL':"'".$this->db->escape($this->customerId)."'").",";
			$sql.= " ".(! isset($this->invoiceId)?'NULL':"'".$this->db->escape($this->invoiceId)."'").",";
			$sql.= " ".(! isset($this->permitId)?'NULL':"'".$this->db->escape($this->permitId)."'");

			$sql.= ")";

			$this->db->begin();

			dol_syslog(get_class($this)."::create sql=".$sql, LOG_DEBUG);
			$resql=$this->db->query($sql);
			if (! $resql) { $error++; $this->errors[]="Error ".$this->db->lasterror(); }

			if (! $error)
			{
				$this->POID = $this->db->last_insert_id(MAIN_DB_PREFIX."ea_po");
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
		$sql = "CALL getPO(".$id.")";

		dol_syslog(get_class($this)."::fetch sql=".$sql, LOG_DEBUG);
		$resql=$this->db->query($sql);
		if ($resql)
		{
			if ($this->db->num_rows($resql))
			{
				$obj = $this->db->fetch_object($resql);
				$this->ref = $obj->POID;
								
				$this->POID = $obj->POID;
				$this->PONUMBER = $obj->PONUMBER;
				$this->PODATE = $obj->PODATE;
				$this->QUOTEDATE = $obj->QUOTEDATE;
				$this->Salesman = $obj->Salesman;
				$this->CUSTOMERNAME = $obj->CUSTOMERNAME;
				$this->CONTACTNAME = $obj->CONTACTNAME;
				$this->CONTACTPHONE1 = $obj->CONTACTPHONE1;
				$this->CONTACTPHONE2 = $obj->CONTACTPHONE2;
				$this->CUSTOMERADDRESS = $obj->CUSTOMERADDRESS;
				$this->ZIPCODE = $obj->ZIPCODE;
				$this->CITY = $obj->CITY;
				$this->STATE = $obj->STATE;
				$this->PHONENUMBER1 = $obj->PHONENUMBER1;
				$this->PHONENUMBER2 = $obj->PHONENUMBER2;
				$this->FAXNUMBER = $obj->FAXNUMBER;
				$this->EMail = $obj->EMail;
				$this->COLOR = $obj->COLOR ? $obj->COLOR : "NONE";
				$this->HTVALUE = $obj->HTVALUE;
				$this->DESCRIPTIONOFWORK = $obj->DESCRIPTIONOFWORK;
				$this->OBSERVATION = $obj->OBSERVATION;
				$this->TOTALTRACK = $obj->TOTALTRACK;
				$this->TAPCONS = $obj->TAPCONS;
				$this->TOTALLONG = $obj->TOTALLONG;
				$this->FASTENERS = $obj->FASTENERS;
				$this->TOTALALUMINST = $obj->TOTALALUMINST;
				$this->TOTALLINEARFT = $obj->TOTALLINEARFT;
				$this->OBSINST = $obj->OBSINST;
				$this->SQINSTPRICE = $obj->SQINSTPRICE;
				$this->INSTSALESPRICE = $obj->INSTSALESPRICE;
				$this->ESTHTVALUE = $obj->ESTHTVALUE;
				$this->ESTOBSERVATION = $obj->ESTOBSERVATION;
				$this->INSTTIME = $obj->INSTTIME;
				$this->PERMIT = $obj->PERMIT;
				$this->CUSTVALUE = $obj->CUSTVALUE;
				$this->CUSTOMIZE = $obj->CUSTOMIZE;
				$this->SALES_TAX = $obj->SALES_TAX;
				$this->SALESTAXAMOUNT = $obj->SALESTAXAMOUNT;
				$this->TOTALALUM = $obj->TOTALALUM;
				$this->SALESPRICE = $obj->SALESPRICE;
				$this->SQFEETPRICE = $obj->SQFEETPRICE;
				$this->OTHERFEES = $obj->OTHERFEES;
				$this->Check50 = $obj->Check50==1;
				$this->CheckAssIns = $obj->CheckAssIns==1;
				$this->OrderCompleted = $obj->OrderCompleted==1;
				$this->Check10YearsWarranty = $obj->Check10YearsWarranty==1;
				$this->Check10YearsFreeMaintenance = $obj->Check10YearsFreeMaintenance==1;
				$this->CheckFreeOpeningClosing = $obj->CheckFreeOpeningClosing==1;
				$this->CheckNoPayment = $obj->CheckNoPayment==1;
				$this->YearsWarranty = $obj->YearsWarranty;
				$this->LifeTimeWarranty = $obj->LifeTimeWarranty==1;
				$this->SignatureReq = $obj->SignatureReq==1;
				$this->Discount = $obj->Discount;
				$this->customerId = $obj->customerId;
				$this->invoiceId = $obj->invoiceId;
				$this->invoiceLocked = $obj->invoiceLocked == 1;
				$this->permitId = $obj->permitId;
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
	 *  Duplicate a Production Order and all it's items
	 *
	 *  @param	int		$id	Id object
	 *  @param	int		$initials	Initials to use to create the PONUMBER for the copied PO
	 *  @return int		The POID of the new production order
	 */
	function duplicate($id, $initials)
	{
		global $langs;
		$newPOID = 0;
		$sql = "CALL copyPO(".$id.",'".$initials."')";

		dol_syslog(get_class($this)."::fetch sql=".$sql, LOG_DEBUG);
		$resql=$this->db->query($sql);
		if ($resql)
		{
			if ($this->db->num_rows($resql))
			{
				$obj = $this->db->fetch_object($resql);
				$newPOID = $obj->newPOID;
			}
			$this->db->free($resql);
			$this->db->db->next_result(); // Stored procedure returns an extra result set :(
			return $newPOID;
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
				
		if (isset($this->PONUMBER)) $this->PONUMBER=trim($this->PONUMBER);
		if (isset($this->PODATE)) $this->PODATE=trim($this->PODATE);
		if (isset($this->QUOTEDATE)) $this->QUOTEDATE=trim($this->QUOTEDATE);
		if (isset($this->Salesman)) $this->Salesman=trim($this->Salesman);
		if (isset($this->COLOR)) $this->COLOR=trim($this->COLOR);
		if (isset($this->HTVALUE)) $this->HTVALUE=trim($this->HTVALUE);
		if (isset($this->DESCRIPTIONOFWORK)) $this->DESCRIPTIONOFWORK=trim($this->DESCRIPTIONOFWORK);
		if (isset($this->OBSERVATION)) $this->OBSERVATION=trim($this->OBSERVATION);
		if (isset($this->TOTALTRACK)) $this->TOTALTRACK=trim($this->TOTALTRACK);
		if (isset($this->TAPCONS)) $this->TAPCONS=trim($this->TAPCONS);
		if (isset($this->TOTALLONG)) $this->TOTALLONG=trim($this->TOTALLONG);
		if (isset($this->FASTENERS)) $this->FASTENERS=trim($this->FASTENERS);
		if (isset($this->TOTALALUMINST)) $this->TOTALALUMINST=trim($this->TOTALALUMINST);
		if (isset($this->TOTALLINEARFT)) $this->TOTALLINEARFT=trim($this->TOTALLINEARFT);
		if (isset($this->OBSINST)) $this->OBSINST=trim($this->OBSINST);
		if (isset($this->SQINSTPRICE)) $this->SQINSTPRICE=trim($this->SQINSTPRICE);
		if (isset($this->INSTSALESPRICE)) $this->INSTSALESPRICE=trim($this->INSTSALESPRICE);
		if (isset($this->ESTHTVALUE)) $this->ESTHTVALUE=trim($this->ESTHTVALUE);
		if (isset($this->ESTOBSERVATION)) $this->ESTOBSERVATION=trim($this->ESTOBSERVATION);
		if (isset($this->INSTTIME)) $this->INSTTIME=trim($this->INSTTIME);
		if (isset($this->PERMIT)) $this->PERMIT=trim($this->PERMIT);
		if (isset($this->CUSTVALUE)) $this->CUSTVALUE=trim($this->CUSTVALUE);
		if (isset($this->CUSTOMIZE)) $this->CUSTOMIZE=trim($this->CUSTOMIZE);
		if (isset($this->SALES_TAX)) $this->SALES_TAX=trim($this->SALES_TAX);
		if (isset($this->SALESTAXAMOUNT)) $this->SALESTAXAMOUNT=trim($this->SALESTAXAMOUNT);
		if (isset($this->TOTALALUM)) $this->TOTALALUM=trim($this->TOTALALUM);
		if (isset($this->SALESPRICE)) $this->SALESPRICE=trim($this->SALESPRICE);
		if (isset($this->SQFEETPRICE)) $this->SQFEETPRICE=trim($this->SQFEETPRICE);
		if (isset($this->OTHERFEES)) $this->OTHERFEES=trim($this->OTHERFEES);
		$this->Check50=isset($this->Check50)?$this->Check50:false;
		$this->CheckAssIns=isset($this->CheckAssIns)?$this->CheckAssIns:false;
		$this->OrderCompleted=isset($this->OrderCompleted)?$this->OrderCompleted:false;
		$this->Check10YearsWarranty=isset($this->Check10YearsWarranty)?$this->Check10YearsWarranty:false;
		$this->Check10YearsFreeMaintenance=isset($this->Check10YearsFreeMaintenance)?$this->Check10YearsFreeMaintenance:false;
		$this->CheckFreeOpeningClosing=isset($this->CheckFreeOpeningClosing)?$this->CheckFreeOpeningClosing:false;
		$this->CheckNoPayment=isset($this->CheckNoPayment)?$this->CheckNoPayment:false;
		if (isset($this->YearsWarranty)) $this->YearsWarranty=trim($this->YearsWarranty);
		$this->LifeTimeWarranty=isset($this->LifeTimeWarranty)?$this->LifeTimeWarranty:false;
		$this->SignatureReq=isset($this->SignatureReq)?$this->SignatureReq:false;
		if (isset($this->Discount)) $this->Discount=trim($this->Discount);
		if (isset($this->customerId)) $this->customerId=trim($this->customerId);
		if (isset($this->invoiceId)) $this->invoiceId=trim($this->invoiceId);
		if (isset($this->permitId)) $this->permitId=trim($this->permitId);

		// Check parameters
		// Put here code to add a control on parameters values

		// Update request
		$sql = "UPDATE ".MAIN_DB_PREFIX."ea_po SET";
		
		$sql.= " PONUMBER=".(isset($this->PONUMBER)?"'".$this->db->escape($this->PONUMBER)."'":"null").",";
		$sql.= " PODATE=".(isset($this->PODATE)?"'".$this->db->escape($this->PODATE)."'":"null").",";
		$sql.= " QUOTEDATE=".(isset($this->QUOTEDATE)?"'".$this->db->escape($this->QUOTEDATE)."'":"null").",";
		$sql.= " Salesman=".(isset($this->Salesman)?"'".$this->db->escape($this->Salesman)."'":"null").",";
		$sql.= " COLOR=".(isset($this->COLOR)?"'".$this->db->escape($this->COLOR)."'":"null").",";
		$sql.= " HTVALUE=".(isset($this->HTVALUE)?"'".$this->db->escape($this->HTVALUE)."'":"null").",";
		$sql.= " DESCRIPTIONOFWORK=".(isset($this->DESCRIPTIONOFWORK)?"'".$this->db->escape($this->DESCRIPTIONOFWORK)."'":"null").",";
		$sql.= " OBSERVATION=".(isset($this->OBSERVATION)?"'".$this->db->escape($this->OBSERVATION)."'":"null").",";
		$sql.= " TOTALTRACK=".(isset($this->TOTALTRACK)?"'".$this->db->escape($this->TOTALTRACK)."'":"null").",";
		$sql.= " TAPCONS=".(isset($this->TAPCONS)?"'".$this->db->escape($this->TAPCONS)."'":"null").",";
		$sql.= " TOTALLONG=".(isset($this->TOTALLONG)?"'".$this->db->escape($this->TOTALLONG)."'":"null").",";
		$sql.= " FASTENERS=".(isset($this->FASTENERS)?"'".$this->db->escape($this->FASTENERS)."'":"null").",";
		$sql.= " TOTALALUMINST=".(isset($this->TOTALALUMINST)?"'".$this->db->escape($this->TOTALALUMINST)."'":"null").",";
		$sql.= " TOTALLINEARFT=".(isset($this->TOTALLINEARFT)?"'".$this->db->escape($this->TOTALLINEARFT)."'":"null").",";
		$sql.= " OBSINST=".(isset($this->OBSINST)?"'".$this->db->escape($this->OBSINST)."'":"null").",";
		$sql.= " SQINSTPRICE=".(isset($this->SQINSTPRICE)?"'".$this->db->escape($this->SQINSTPRICE)."'":"null").",";
		$sql.= " INSTSALESPRICE=".(isset($this->INSTSALESPRICE)?"'".$this->db->escape($this->INSTSALESPRICE)."'":"null").",";
		$sql.= " ESTHTVALUE=".(isset($this->ESTHTVALUE)?"'".$this->db->escape($this->ESTHTVALUE)."'":"null").",";
		$sql.= " ESTOBSERVATION=".(isset($this->ESTOBSERVATION)?"'".$this->db->escape($this->ESTOBSERVATION)."'":"null").",";
		$sql.= " INSTTIME=".(isset($this->INSTTIME)?"'".$this->db->escape($this->INSTTIME)."'":"null").",";
		$sql.= " PERMIT=".(isset($this->PERMIT)?"'".$this->db->escape($this->PERMIT)."'":"null").",";
		$sql.= " CUSTVALUE=".(isset($this->CUSTVALUE)?"'".$this->db->escape($this->CUSTVALUE)."'":"null").",";
		$sql.= " CUSTOMIZE=".(isset($this->CUSTOMIZE)?"'".$this->db->escape($this->CUSTOMIZE)."'":"null").",";
		$sql.= " SALES_TAX=".(isset($this->SALES_TAX)?"'".$this->db->escape($this->SALES_TAX)."'":"null").",";
		$sql.= " SALESTAXAMOUNT=".(isset($this->SALESTAXAMOUNT)?"'".$this->db->escape($this->SALESTAXAMOUNT)."'":"null").",";
		$sql.= " TOTALALUM=".(isset($this->TOTALALUM)?"'".$this->db->escape($this->TOTALALUM)."'":"null").",";
		$sql.= " SALESPRICE=".(isset($this->SALESPRICE)?"'".$this->db->escape($this->SALESPRICE)."'":"null").",";
		$sql.= " SQFEETPRICE=".(isset($this->SQFEETPRICE)?"'".$this->db->escape($this->SQFEETPRICE)."'":"null").",";
		$sql.= " OTHERFEES=".(isset($this->OTHERFEES)?"'".$this->db->escape($this->OTHERFEES)."'":"null").",";
		$sql.= " Check50=".($this->Check50==true?"1":"0").",";
		$sql.= " CheckAssIns=".($this->CheckAssIns==true?"1":"0").",";
		$sql.= " OrderCompleted=".($this->OrderCompleted==true?"1":"0").",";
		$sql.= " Check10YearsWarranty=".($this->Check10YearsWarranty==true?"1":"0").",";
		$sql.= " Check10YearsFreeMaintenance=".($this->Check10YearsFreeMaintenance==true?"1":"0").",";
		$sql.= " CheckFreeOpeningClosing=".($this->CheckFreeOpeningClosing==true?"1":"0").",";
		$sql.= " CheckNoPayment=".($this->CheckNoPayment==true?"1":"0").",";
		$sql.= " YearsWarranty=".(isset($this->YearsWarranty)?"'".$this->db->escape($this->YearsWarranty)."'":"null").",";
		$sql.= " LifeTimeWarranty=".($this->LifeTimeWarranty==true?"1":"0").",";
		$sql.= " SignatureReq=".($this->SignatureReq==true?"1":"0").",";
		$sql.= " Discount=".(isset($this->Discount)?"'".$this->db->escape($this->Discount)."'":"null").",";
		$sql.= " customerId=".(isset($this->customerId)?$this->db->escape($this->customerId):"null").",";
		$sql.= " invoiceId=".(isset($this->invoiceId)?"'".$this->db->escape($this->invoiceId)."'":"null").",";
		$sql.= " permitId=".(isset($this->permitId)?"'".$this->db->escape($this->permitId)."'":"null");
		
		$sql.= " WHERE POID=".$this->POID;

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
			$itemObject = new EaProductionOrderItems($this->db);
			$result = $itemObject->deleteByPOID($this->POID);

			if (!$result)
			{
				$error++; 
				$this->error .= ($this->error?', ':'').$itemObject->error;
			}
			else
			{
				$sql = "DELETE FROM ".MAIN_DB_PREFIX."ea_po";
				$sql.= " WHERE POID=".$this->POID;

				dol_syslog(get_class($this)."::delete sql=".$sql);
				$resql = $this->db->query($sql);
				if (! $resql) { $error++; $this->errors[]="Error ".$this->db->lasterror(); }
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

		$object=new Eabuilddepts($this->db);

		$this->db->begin();

		// Load source object
		$object->fetch($fromid);
		$object->POID=0;
		$object->ref = $object->POID;

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
			return $object->POID;
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
		$sql.= " max(po.POID) as POID";		
		$sql.= " FROM ".MAIN_DB_PREFIX."ea_po as po";

		$return_arr = array();
		$resql=$this->db->query($sql);
		if ($resql)
		{
			while ($obj = $this->db->fetch_object($resql))
			{
				$result = $this->fetch($obj->POID);
				$this->fetchItems();
				break;
			}
		}
		else
		{
			$this->ref = '';
		
			$this->POID='1';
			$this->PONUMBER='1PD';
			$this->PODATE=date("m/d/Y");
			$this->QUOTEDATE=date("m/d/Y");
			$this->Salesman='pd';
			$this->CUSTOMERNAME='';
			$this->CONTACTNAME='';
			$this->CONTACTPHONE1='';
			$this->CONTACTPHONE2='';
			$this->CUSTOMERADDRESS='';
			$this->ZIPCODE='';
			$this->CITY='';
			$this->STATE='';
			$this->PHONENUMBER1='';
			$this->PHONENUMBER2='';
			$this->FAXNUMBER='';
			$this->EMail='';
			$this->COLOR='BEIGE';
			$this->HTVALUE='8';
			$this->DESCRIPTIONOFWORK='An description';
			$this->OBSERVATION='An observation';
			$this->TOTALTRACK='';
			$this->TAPCONS='';
			$this->TOTALLONG='';
			$this->FASTENERS='';
			$this->TOTALALUMINST='';
			$this->TOTALLINEARFT='';
			$this->OBSINST='';
			$this->SQINSTPRICE='';
			$this->INSTSALESPRICE='';
			$this->ESTHTVALUE='';
			$this->ESTOBSERVATION='';
			$this->INSTTIME='';
			$this->PERMIT='';
			$this->CUSTVALUE='';
			$this->CUSTOMIZE='';
			$this->SALES_TAX='';
			$this->SALESTAXAMOUNT='';
			$this->TOTALALUM='';
			$this->SALESPRICE='';
			$this->SQFEETPRICE='';
			$this->OTHERFEES='';
			$this->Check50=false;
			$this->CheckAssIns=false;
			$this->OrderCompleted=false;
			$this->Check10YearsWarranty=false;
			$this->Check10YearsFreeMaintenance=false;
			$this->CheckFreeOpeningClosing=false;
			$this->CheckNoPayment=false;
			$this->YearsWarranty='';
			$this->LifeTimeWarranty=false;
			$this->SignatureReq=false;
			$this->Discount='';
			$this->customerId='1';
			$this->invoiceId='';
			$this->invoiceLocked=false;
			$this->permitId='';
		}

		$this->db->free($resql);

	}

	function fetchItems()
	{
		$itemObject=new EaProductionOrderItems($this->db);
		$this->items = $itemObject->fetchByPOID($this->POID);
}

	/**
	 *	Return label of status (activity, closed)
	 */
	function getLibStatut($mode=0)
	{
		return ""; // Status not used for building departments
	}


	/**
	 *  Load a page of results from the database
	 */
	function fetchPage($page, $pageSize=5, $custId=0)
	{
		if ($page < 0)
			$page = 0;
		$firstRowNum = $page*$pageSize;

		$result = -1;
		$sql = "SELECT";
		$sql.= " po.POID";		
		$sql.= " FROM ".MAIN_DB_PREFIX."ea_po as po";
		$sql.= $custId ? " WHERE po.customerId=".$custId : "";
		$sql.= " ORDER BY po.POID ";
		$sql.= " LIMIT ".$firstRowNum.", ".$pageSize;

		$return_arr = array();
		$resql=$this->db->query($sql);
		if ($resql)
		{
			while ($obj = $this->db->fetch_object($resql))
			{
				$po = new EaProductionOrders($this->db);
				$result = $po->fetch($obj->POID);
				if ($result<0)
				{
					$this->error="Error ".$this->db->lasterror();
					break;
				}
				else
					array_push($return_arr,$po);
			}
		}
		else
		{
	  		$this->error="Error ".$this->db->lasterror();
			dol_syslog(get_class($this)."::fetch ".$this->error, LOG_ERR);
			$result = -1;
		}
		$this->db->free($resql);

		return $return_arr;
	}

	/**
	 *  Load all given POs
	 */
	function fetchAllById($ids)
	{
		$result = -1;

		$return_arr = array();
		foreach ($ids as $id)
		{
			$po = new EaProductionOrders($this->db);
			$result = $po->fetch($id);
			if ($result<0)
			{
				$this->error="Error ".$this->db->lasterror();
				break;
			}
			else
				array_push($return_arr,$po);
		}

		return $return_arr;
	}

	/**
	 *  Load the object that immediately follows the given object from the database
	 */
	function fetchNext($id)
	{
		$result = -1;
		$sql = "SELECT";
		$sql.= " min(po.POID) as POID";		
		$sql.= " FROM ".MAIN_DB_PREFIX."ea_po as po";
		$sql.= " WHERE po.POID > ".$id;

		$return_arr = array();
		$resql=$this->db->query($sql);
		if ($resql)
		{
			while ($obj = $this->db->fetch_object($resql))
			{
				$po = new EaProductionOrders($this->db);
				$result = $po->fetch($obj->POID);
				if ($result<0)
				{
					$this->error="Error ".$this->db->lasterror();
					break;
				}
				else
					array_push($return_arr,$po);
			}
		}
		else
		{
			$po = new EaProductionOrders($this->db);
			$result = $po->fetch($id);
			if ($result<0)
			{
				$this->error="Error ".$this->db->lasterror();
				//break;
			}
			else
				array_push($return_arr,$po);
		}
		$this->db->free($resql);

		return $return_arr;
	}


	/**
	 *  Load the object that immediately precedes the given object from the database
	 */
	function fetchPrev($id)
	{
		$result = -1;
		$sql = "SELECT";
		$sql.= " max(po.POID) as POID";		
		$sql.= " FROM ".MAIN_DB_PREFIX."ea_po as po";
		$sql.= " WHERE po.POID < ".$id;

		$return_arr = array();
		$resql=$this->db->query($sql);
		if ($resql)
		{
			while ($obj = $this->db->fetch_object($resql))
			{
				$po = new EaProductionOrders($this->db);
				$result = $po->fetch($obj->POID);
				if ($result<0)
				{
					$this->error="Error ".$this->db->lasterror();
					break;
				}
				else
					array_push($return_arr,$po);
			}
		}
		else
		{
			$po = new EaProductionOrders($this->db);
			$result = $po->fetch($id);
			if ($result<0)
			{
				$this->error="Error ".$this->db->lasterror();
				//break;
			}
			else
				array_push($return_arr,$po);
		}
		$this->db->free($resql);

		return $return_arr;
	}

	/**
	 *  Load the page that contains the given POID
	 *  First determine page number by counting PO's that come before it
	 */
	function fetchPageWithPOID($id, $pageSize=5, $custId=0)
	{
		$result = -1;
		$sql = "SELECT";
		$sql.= " count(*) as cnt";		
		$sql.= " FROM ".MAIN_DB_PREFIX."ea_po as po";
		$sql.= " WHERE ";
		$sql.= $custId ? " po.customerId=".$custId." AND " : "";
		$sql.= " po.POID <= ".$id;
		$sql.= " ORDER BY po.POID";

		$resql=$this->db->query($sql);
		$cnt = 0;
		if ($resql)
		{
			$obj = $this->db->fetch_object($resql);
			$cnt = $obj->cnt;
		}
		$this->db->free($resql);
		return $this->fetchPage( (int) (($cnt-1) / $pageSize), $pageSize, $custId);
	}

	/**
	 *  Load the page that contains the POID after the given POID
	 *  First determine page number by counting PO's that come before it
	 */
	function fetchPageAfterPOID($id, $pageSize=5, $custId=0)
	{
		$result = -1;
		$sql = "SELECT";
		$sql.= " count(*) as cnt";		
		$sql.= " FROM ".MAIN_DB_PREFIX."ea_po as po";
		$sql.= " WHERE ";
		$sql.= $custId ? " po.customerId=".$custId." AND " : "";
		$sql.= " po.POID <= ".$id;
		$sql.= " ORDER BY po.POID ";

		$resql=$this->db->query($sql);
		$cnt = 0;
		if ($resql)
		{
			$obj = $this->db->fetch_object($resql);
			$cnt = $obj->cnt;
		}
		$this->db->free($resql);
		return $this->fetchPage( (int) ($cnt / $pageSize), $pageSize, $custId);
	}

	/**
	 *  Load the page that contains the POID before the given POID
	 *  First determine page number by counting PO's that come before it
	 */
	function fetchPageBeforePOID($id, $pageSize=5, $custId=0)
	{
		$result = -1;
		$sql = "SELECT";
		$sql.= " count(*) as cnt";		
		$sql.= " FROM ".MAIN_DB_PREFIX."ea_po as po";
		$sql.= " WHERE ";
		$sql.= $custId ? " po.customerId=".$custId." AND " : "";
		$sql.= " po.POID < ".$id;
		$sql.= " ORDER BY po.POID";

		$resql=$this->db->query($sql);
		$cnt = 0;
		if ($resql)
		{
			$obj = $this->db->fetch_object($resql);
			$cnt = $obj->cnt;
		}
		$this->db->free($resql);

		return $this->fetchPage( (int) (($cnt-1) / $pageSize), $pageSize, $custId);
	}

	function fetchColorsById($ids)
	{
		$result = -1;
		$sql = <<<EOD
			SELECT 
				DISTINCT ifnull(pi.COLOR ,'NONE') as COLOR
			FROM 
				llx_ea_po p 
				JOIN llx_ea_po_item pi ON p.POID = pi.POID 
			WHERE 
				p.POID in (${ids}) 
			ORDER BY 
				COLOR
EOD;

		$return_arr = array();
		$resql=$this->db->query($sql);
		if ($resql)
		{
			while ($obj = $this->db->fetch_object($resql))
			{
				array_push($return_arr,$obj->COLOR);
			}
		}
		$this->db->free($resql);

		return $return_arr;
	}


	function fetchPONumbersById($ids)
	{
		$result = -1;
		$sql = <<<EOD
			SELECT 
				p.PONUMBER
			FROM 
				llx_ea_po p 
			WHERE 
				p.POID in (${ids}) 
			ORDER BY 
				p.PONUMBER
EOD;

		$return_arr = array();
		$resql=$this->db->query($sql);
		if ($resql)
		{
			while ($obj = $this->db->fetch_object($resql))
			{
				array_push($return_arr,$obj->PONUMBER);
			}
		}
		$this->db->free($resql);

		return $return_arr;
	}
}
?>
