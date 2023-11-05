<?php
// Put here all includes required by your class file
require_once(DOL_DOCUMENT_ROOT."/core/class/commonobject.class.php");


/**
 *	This class is a CRUD wrapper for accessing the llx_ea_po_item table
 */
class EaProductionOrderItems extends CommonObject
{
	var $db;								//!< To store db handler
	var $error;								//!< To return error code (or message)
	var $errors=array();					//!< To return several error codes (or messages)
	var $element='eaproductionorderitems';	//!< Id that identify managed objects
	var $table_element='ea_po_item';		//!< Name of table without prefix where object is stored

	var $PODescriptionID;
	var $POID;
	var $LineNumber;
	var $OPENINGW;
	var $OPENINGHT;
	var $TRACK;
	var $TYPE;
	var $BLADESQTY;
	var $BLADESSTACK;
	var $BLADESLONG;
	var $LEFT;
	var $RIGHT;
	var $LOCKIN;
	var $LOCKSIZE;
	var $UPPERSIZE;
	var $UPPERTYPE;
	var $LOWERSIZE;
	var $LOWERTYPE;
	var $ANGULARTYPE;
	var $ANGULARSIZE;
	var $ANGULARQTY;
	var $MOUNT;
	var $ALUMINST;
	var $LINEARFT;
	var $OPENINGHT4;
	var $ALUMINST4;
	var $EST8HT;
	var $ALUM;
	var $WINDOWSTYPE;
	var $EXTRAANGULARTYPE;
	var $EXTRAANGULARSIZE;
	var $EXTRAANGULARQTY;
	var $SQFEETPRICE;
	var $PRODUCTTYPE;
	var $PRODUCTTYPENAME;
	var $COLOR;
	var $MATERIAL;
	var $PROVIDER;
	var $INSTFEE;
	var $TUBETYPE;
	var $TUBESIZE;
	var $TUBEQTY;

	/**
	 *  Constructor
	 *
	 *  @param	DoliDb	$db	  Database handler
	 */
	function __construct($db)
	{
		$this->db = $db;
		return 1;
	}

	/**
	 *  Create object into database
	 *
	 *  @return int	<0 if KO, Id of created object if OK
	 */
	function create()
	{
		global $conf, $langs;
		$error=0;

		// Clean parameters
		if (isset($this->POID)) $this->POID=trim($this->POID);
		if (isset($this->LineNumber)) $this->LineNumber=trim($this->LineNumber);
		if (isset($this->OPENINGW)) $this->OPENINGW=trim($this->OPENINGW);
		if (isset($this->OPENINGHT)) $this->OPENINGHT=trim($this->OPENINGHT);
		if (isset($this->TRACK)) $this->TRACK=trim($this->TRACK);
		if (isset($this->TYPE)) $this->TYPE=trim($this->TYPE);
		if (isset($this->BLADESQTY)) $this->BLADESQTY=trim($this->BLADESQTY);
		if (isset($this->BLADESSTACK)) $this->BLADESSTACK=trim($this->BLADESSTACK);
		if (isset($this->BLADESLONG)) $this->BLADESLONG=trim($this->BLADESLONG);
		if (isset($this->LEFT)) $this->LEFT=trim($this->LEFT);
		if (isset($this->RIGHT)) $this->RIGHT=trim($this->RIGHT);
		if (isset($this->LOCKIN)) $this->LOCKIN=trim($this->LOCKIN);
		if (isset($this->LOCKSIZE)) $this->LOCKSIZE=trim($this->LOCKSIZE);
		if (isset($this->UPPERSIZE)) $this->UPPERSIZE=trim($this->UPPERSIZE);
		if (isset($this->UPPERTYPE)) $this->UPPERTYPE=trim($this->UPPERTYPE);
		if (isset($this->LOWERSIZE)) $this->LOWERSIZE=trim($this->LOWERSIZE);
		if (isset($this->LOWERTYPE)) $this->LOWERTYPE=trim($this->LOWERTYPE);
		if (isset($this->ANGULARTYPE)) $this->ANGULARTYPE=trim($this->ANGULARTYPE);
		if (isset($this->ANGULARSIZE)) $this->ANGULARSIZE=trim($this->ANGULARSIZE);
		if (isset($this->ANGULARQTY)) $this->ANGULARQTY=trim($this->ANGULARQTY);
		if (isset($this->MOUNT)) $this->MOUNT=trim($this->MOUNT);
		if (isset($this->ALUMINST)) $this->ALUMINST=trim($this->ALUMINST);
		if (isset($this->LINEARFT)) $this->LINEARFT=trim($this->LINEARFT);
		if (isset($this->OPENINGHT4)) $this->OPENINGHT4=trim($this->OPENINGHT4);
		if (isset($this->ALUMINST4)) $this->ALUMINST4=trim($this->ALUMINST4);
		if (isset($this->EST8HT)) $this->EST8HT=trim($this->EST8HT);
		if (isset($this->ALUM)) $this->ALUM=trim($this->ALUM);
		if (isset($this->WINDOWSTYPE)) $this->WINDOWSTYPE=trim($this->WINDOWSTYPE);
		if (isset($this->EXTRAANGULARTYPE)) $this->EXTRAANGULARTYPE=trim($this->EXTRAANGULARTYPE);
		if (isset($this->EXTRAANGULARSIZE)) $this->EXTRAANGULARSIZE=trim($this->EXTRAANGULARSIZE);
		if (isset($this->EXTRAANGULARQTY)) $this->EXTRAANGULARQTY=trim($this->EXTRAANGULARQTY);
		if (isset($this->SQFEETPRICE)) $this->SQFEETPRICE=trim($this->SQFEETPRICE);
		if (isset($this->PRODUCTTYPE)) $this->PRODUCTTYPE=trim($this->PRODUCTTYPE);
		if (isset($this->COLOR)) $this->COLOR=trim($this->COLOR);
		if (isset($this->MATERIAL)) $this->MATERIAL=trim($this->MATERIAL);
		if (isset($this->PROVIDER)) $this->PROVIDER=trim($this->PROVIDER);
		if (isset($this->INSTFEE)) $this->INSTFEE=trim($this->INSTFEE);
		if (isset($this->TUBETYPE)) $this->TUBETYPE=trim($this->TUBETYPE);
		if (isset($this->TUBESIZE)) $this->TUBESIZE=trim($this->TUBESIZE);
		if (isset($this->TUBEQTY)) $this->TUBEQTY=trim($this->TUBEQTY);
						
		if (!$this->LOCKIN)
			$this->LOCKIN = NULL;
		if (!$this->LOCKSIZE)
			$this->LOCKSIZE = NULL;

		// Check parameters
		if (! $this->POID) { $error++; $this->errors[]="Missing POID"; }

		if (!$error)
		{
			// Insert request
			$sql = "INSERT INTO ".MAIN_DB_PREFIX."ea_po_item (";
			
			$sql.= "POID,";
			$sql.= " LineNumber,";
			$sql.= " OPENINGW,";
			$sql.= " OPENINGHT,";
			$sql.= " TRACK,";
			$sql.= " TYPE,";
			$sql.= " BLADESQTY,";
			$sql.= " BLADESSTACK,";
			$sql.= " BLADESLONG,";
			$sql.= " `LEFT`,";
			$sql.= " `RIGHT`,";
			$sql.= " LOCKIN,";
			$sql.= " LOCKSIZE,";
			$sql.= " UPPERSIZE,";
			$sql.= " UPPERTYPE,";
			$sql.= " LOWERSIZE,";
			$sql.= " LOWERTYPE,";
			$sql.= " ANGULARTYPE,";
			$sql.= " ANGULARSIZE,";
			$sql.= " ANGULARQTY,";
			$sql.= " MOUNT,";
			$sql.= " ALUMINST,";
			$sql.= " LINEARFT,";
			$sql.= " OPENINGHT4,";
			$sql.= " ALUMINST4,";
			$sql.= " EST8HT,";
			$sql.= " ALUM,";
			$sql.= " WINDOWSTYPE,";
			$sql.= " EXTRAANGULARTYPE,";
			$sql.= " EXTRAANGULARSIZE,";
			$sql.= " EXTRAANGULARQTY,";
			$sql.= " SQFEETPRICE,";
			$sql.= " PRODUCTTYPE,";
			$sql.= " COLOR,";
			$sql.= " MATERIAL,";
			$sql.= " PROVIDER,";
			$sql.= " INSTFEE,";
			$sql.= " TUBETYPE,";
			$sql.= " TUBESIZE,";
			$sql.= " TUBEQTY";
			
			$sql.= ") VALUES (";

			$sql.= " ".(! isset($this->POID)?'NULL':"'".$this->db->escape($this->POID)."'").",";
			$sql.= " ".(! isset($this->LineNumber)?'NULL':"'".$this->db->escape($this->LineNumber)."'").",";
			$sql.= " ".(! isset($this->OPENINGW)?'NULL':"'".$this->db->escape($this->OPENINGW)."'").",";
			$sql.= " ".(! isset($this->OPENINGHT)?'NULL':"'".$this->db->escape($this->OPENINGHT)."'").",";
			$sql.= " ".(! isset($this->TRACK)?'NULL':"'".$this->db->escape($this->TRACK)."'").",";
			$sql.= " ".(! isset($this->TYPE)?'NULL':"'".$this->db->escape($this->TYPE)."'").",";
			$sql.= " ".(! isset($this->BLADESQTY)?'NULL':"'".$this->db->escape($this->BLADESQTY)."'").",";
			$sql.= " ".(! isset($this->BLADESSTACK)?'NULL':"'".$this->db->escape($this->BLADESSTACK)."'").",";
			$sql.= " ".(! isset($this->BLADESLONG)?'NULL':"'".$this->db->escape($this->BLADESLONG)."'").",";
			$sql.= " ".(! isset($this->LEFT)?'NULL':"'".$this->db->escape($this->LEFT)."'").",";
			$sql.= " ".(! isset($this->RIGHT)?'NULL':"'".$this->db->escape($this->RIGHT)."'").",";
			$sql.= " ".(! isset($this->LOCKIN)?'NULL':"'".$this->db->escape($this->LOCKIN)."'").",";
			$sql.= " ".(! isset($this->LOCKSIZE)?'NULL':"'".$this->db->escape($this->LOCKSIZE)."'").",";
			$sql.= " ".(! isset($this->UPPERSIZE)?'NULL':"'".$this->db->escape($this->UPPERSIZE)."'").",";
			$sql.= " ".(! isset($this->UPPERTYPE)?'NULL':"'".$this->db->escape($this->UPPERTYPE)."'").",";
			$sql.= " ".(! isset($this->LOWERSIZE)?'NULL':"'".$this->db->escape($this->LOWERSIZE)."'").",";
			$sql.= " ".(! isset($this->LOWERTYPE)?'NULL':"'".$this->db->escape($this->LOWERTYPE)."'").",";
			$sql.= " ".(! isset($this->ANGULARTYPE)?'NULL':"'".$this->db->escape($this->ANGULARTYPE)."'").",";
			$sql.= " ".(! isset($this->ANGULARSIZE)?'NULL':"'".$this->db->escape($this->ANGULARSIZE)."'").",";
			$sql.= " ".(! isset($this->ANGULARQTY)?'NULL':"'".$this->db->escape($this->ANGULARQTY)."'").",";
			$sql.= " ".(! isset($this->MOUNT)?'NULL':"'".$this->db->escape($this->MOUNT)."'").",";
			$sql.= " ".(! isset($this->ALUMINST)?'NULL':"'".$this->db->escape($this->ALUMINST)."'").",";
			$sql.= " ".(! isset($this->LINEARFT)?'NULL':"'".$this->db->escape($this->LINEARFT)."'").",";
			$sql.= " ".(! isset($this->OPENINGHT4)?'NULL':"'".$this->db->escape($this->OPENINGHT4)."'").",";
			$sql.= " ".(! isset($this->ALUMINST4)?'NULL':"'".$this->db->escape($this->ALUMINST4)."'").",";
			$sql.= " ".(! isset($this->EST8HT)?'NULL':"'".$this->db->escape($this->EST8HT)."'").",";
			$sql.= " ".(! isset($this->ALUM)?'NULL':"'".$this->db->escape($this->ALUM)."'").",";
			$sql.= " ".(! isset($this->WINDOWSTYPE)?'NULL':"'".$this->db->escape($this->WINDOWSTYPE)."'").",";
			$sql.= " ".(! isset($this->EXTRAANGULARTYPE)?'NULL':"'".$this->db->escape($this->EXTRAANGULARTYPE)."'").",";
			$sql.= " ".(! isset($this->EXTRAANGULARSIZE)?'NULL':"'".$this->db->escape($this->EXTRAANGULARSIZE)."'").",";
			$sql.= " ".(! isset($this->EXTRAANGULARQTY)?'NULL':"'".$this->db->escape($this->EXTRAANGULARQTY)."'").",";
			$sql.= " ".(! isset($this->SQFEETPRICE)?'NULL':"'".$this->db->escape($this->SQFEETPRICE)."'").",";
			$sql.= " ".(! isset($this->PRODUCTTYPE)?'NULL':"'".$this->db->escape($this->PRODUCTTYPE)."'").",";
			$sql.= " ".(! isset($this->COLOR)?'NULL':"'".$this->db->escape($this->COLOR)."'").",";
			$sql.= " ".(! isset($this->MATERIAL)?'NULL':"'".$this->db->escape($this->MATERIAL)."'").",";
			$sql.= " ".(! isset($this->PROVIDER)?'NULL':"'".$this->db->escape($this->PROVIDER)."'").",";
			$sql.= " ".(! isset($this->INSTFEE)?'NULL':"'".$this->db->escape($this->INSTFEE)."'").",";
			$sql.= " ".(! isset($this->TUBETYPE)?'NULL':"'".$this->db->escape($this->TUBETYPE)."'").",";
			$sql.= " ".(! isset($this->TUBESIZE)?'NULL':"'".$this->db->escape($this->TUBESIZE)."'").",";
			$sql.= " ".(! isset($this->TUBEQTY)?'NULL':"'".$this->db->escape($this->TUBEQTY)."'");
			
			$sql.= ")";

			$this->db->begin();

			dol_syslog(get_class($this)."::create sql=".$sql, LOG_DEBUG);
			$resql=$this->db->query($sql);
			if (! $resql) { $error++; $this->errors[]="Error ".$this->db->lasterror(); }

			if (! $error)
			{
				$this->PODescriptionID = $this->db->last_insert_id(MAIN_DB_PREFIX."ea_po_item");
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
	 *  @return int		<0 if KO, >0 if OK
	 */
	function fetch($id)
	{
		global $langs;
		$sql = "SELECT";

		$sql.= " i.PODescriptionID,";
		$sql.= " i.POID,";
		$sql.= " i.LineNumber,";
		$sql.= " i.OPENINGW,";
		$sql.= " i.OPENINGHT,";
		$sql.= " i.TRACK,";
		$sql.= " i.TYPE,";
		$sql.= " i.BLADESQTY,";
		$sql.= " i.BLADESSTACK,";
		$sql.= " i.BLADESLONG,";
		$sql.= " i.`LEFT`,";
		$sql.= " i.`RIGHT`,";
		$sql.= " i.LOCKIN,";
		$sql.= " i.LOCKSIZE,";
		$sql.= " i.UPPERSIZE,";
		$sql.= " i.UPPERTYPE,";
		$sql.= " i.LOWERSIZE,";
		$sql.= " i.LOWERTYPE,";
		$sql.= " i.ANGULARTYPE,";
		$sql.= " i.ANGULARSIZE,";
		$sql.= " i.ANGULARQTY,";
		$sql.= " i.MOUNT,";
		$sql.= " i.ALUMINST,";
		$sql.= " i.LINEARFT,";
		$sql.= " i.OPENINGHT4,";
		$sql.= " i.ALUMINST4,";
		$sql.= " i.EST8HT,";
		$sql.= " i.ALUM,";
		$sql.= " i.WINDOWSTYPE,";
		$sql.= " i.EXTRAANGULARTYPE,";
		$sql.= " i.EXTRAANGULARSIZE,";
		$sql.= " i.EXTRAANGULARQTY,";
		$sql.= " i.SQFEETPRICE,";
		$sql.= " i.PRODUCTTYPE,";
		$sql.= " pt.name as PRODUCTTYPENAME,";
		$sql.= " i.COLOR,";
		$sql.= " i.MATERIAL,";
		$sql.= " i.PROVIDER,";
		$sql.= " i.INSTFEE,";
		$sql.= " i.TUBETYPE,";
		$sql.= " i.TUBESIZE,";
		$sql.= " i.TUBEQTY";

		$sql.= " FROM ".MAIN_DB_PREFIX."ea_po_item i";
		$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."ea_producttypes pt on i.PRODUCTTYPE=pt.id";
		$sql.= " WHERE i.PODescriptionID = ".$id;

		dol_syslog(get_class($this)."::fetch sql=".$sql, LOG_DEBUG);
		$resql=$this->db->query($sql);
		if ($resql)
		{
			if ($this->db->num_rows($resql))
			{
				$obj = $this->db->fetch_object($resql);

				$this->ref = $this->PODescriptionID;
								
				$this->PODescriptionID = $obj->PODescriptionID;
				$this->POID = $obj->POID;
				$this->LineNumber = $obj->LineNumber;
				$this->OPENINGW = $obj->OPENINGW;
				$this->OPENINGHT = $obj->OPENINGHT;
				$this->TRACK = $obj->TRACK;
				$this->TYPE = $obj->TYPE;
				$this->BLADESQTY = $obj->BLADESQTY;
				$this->BLADESSTACK = $obj->BLADESSTACK;
				$this->BLADESLONG = $obj->BLADESLONG;
				$this->LEFT = $obj->LEFT;
				$this->RIGHT = $obj->RIGHT;
				$this->LOCKIN = $obj->LOCKIN;
				$this->LOCKSIZE = $obj->LOCKSIZE;
				$this->UPPERSIZE = $obj->UPPERSIZE;
				$this->UPPERTYPE = $obj->UPPERTYPE;
				$this->LOWERSIZE = $obj->LOWERSIZE;
				$this->LOWERTYPE = $obj->LOWERTYPE;
				$this->ANGULARTYPE = $obj->ANGULARTYPE;
				$this->ANGULARSIZE = $obj->ANGULARSIZE;
				$this->ANGULARQTY = $obj->ANGULARQTY;
				$this->MOUNT = $obj->MOUNT;
				$this->ALUMINST = $obj->ALUMINST;
				$this->LINEARFT = $obj->LINEARFT;
				$this->OPENINGHT4 = $obj->OPENINGHT4;
				$this->ALUMINST4 = $obj->ALUMINST4;
				$this->EST8HT = $obj->EST8HT;
				$this->ALUM = $obj->ALUM;
				$this->WINDOWSTYPE = $obj->WINDOWSTYPE;
				$this->EXTRAANGULARTYPE = $obj->EXTRAANGULARTYPE;
				$this->EXTRAANGULARSIZE = $obj->EXTRAANGULARSIZE;
				$this->EXTRAANGULARQTY = $obj->EXTRAANGULARQTY;
				$this->SQFEETPRICE = $obj->SQFEETPRICE;
				$this->PRODUCTTYPE = $obj->PRODUCTTYPE;
				$this->PRODUCTTYPENAME = $obj->PRODUCTTYPENAME;
				$this->COLOR = $obj->COLOR ? $obj->COLOR : "NONE";
				$this->MATERIAL = $obj->MATERIAL;
				$this->PROVIDER = $obj->PROVIDER;
				$this->INSTFEE = $obj->INSTFEE;
				$this->TUBETYPE = $obj->TUBETYPE;
				$this->TUBESIZE = $obj->TUBESIZE;
				$this->TUBEQTY = $obj->TUBEQTY;
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
	 *  Update object into database
	 *
	 *  @return int	 		   	 <0 if KO, >0 if OK
	 */
	function update()
	{
		global $conf, $langs;
		$error=0;

		// Clean parameters
		if (isset($this->POID)) $this->POID=trim($this->POID);
		if (isset($this->LineNumber)) $this->LineNumber=trim($this->LineNumber);
		if (isset($this->OPENINGW)) $this->OPENINGW=trim($this->OPENINGW);
		if (isset($this->OPENINGHT)) $this->OPENINGHT=trim($this->OPENINGHT);
		if (isset($this->TRACK)) $this->TRACK=trim($this->TRACK);
		if (isset($this->TYPE)) $this->TYPE=trim($this->TYPE);
		if (isset($this->BLADESQTY)) $this->BLADESQTY=trim($this->BLADESQTY);
		if (isset($this->BLADESSTACK)) $this->BLADESSTACK=trim($this->BLADESSTACK);
		if (isset($this->BLADESLONG)) $this->BLADESLONG=trim($this->BLADESLONG);
		if (isset($this->LEFT)) $this->LEFT=trim($this->LEFT);
		if (isset($this->RIGHT)) $this->RIGHT=trim($this->RIGHT);
		if (isset($this->LOCKIN)) $this->LOCKIN=trim($this->LOCKIN);
		if (isset($this->LOCKSIZE)) $this->LOCKSIZE=trim($this->LOCKSIZE);
		if (isset($this->UPPERSIZE)) $this->UPPERSIZE=trim($this->UPPERSIZE);
		if (isset($this->UPPERTYPE)) $this->UPPERTYPE=trim($this->UPPERTYPE);
		if (isset($this->LOWERSIZE)) $this->LOWERSIZE=trim($this->LOWERSIZE);
		if (isset($this->LOWERTYPE)) $this->LOWERTYPE=trim($this->LOWERTYPE);
		if (isset($this->ANGULARTYPE)) $this->ANGULARTYPE=trim($this->ANGULARTYPE);
		if (isset($this->ANGULARSIZE)) $this->ANGULARSIZE=trim($this->ANGULARSIZE);
		if (isset($this->ANGULARQTY)) $this->ANGULARQTY=trim($this->ANGULARQTY);
		if (isset($this->MOUNT)) $this->MOUNT=trim($this->MOUNT);
		if (isset($this->ALUMINST)) $this->ALUMINST=trim($this->ALUMINST);
		if (isset($this->LINEARFT)) $this->LINEARFT=trim($this->LINEARFT);
		if (isset($this->OPENINGHT4)) $this->OPENINGHT4=trim($this->OPENINGHT4);
		if (isset($this->ALUMINST4)) $this->ALUMINST4=trim($this->ALUMINST4);
		if (isset($this->EST8HT)) $this->EST8HT=trim($this->EST8HT);
		if (isset($this->ALUM)) $this->ALUM=trim($this->ALUM);
		if (isset($this->WINDOWSTYPE)) $this->WINDOWSTYPE=trim($this->WINDOWSTYPE);
		if (isset($this->EXTRAANGULARTYPE)) $this->EXTRAANGULARTYPE=trim($this->EXTRAANGULARTYPE);
		if (isset($this->EXTRAANGULARSIZE)) $this->EXTRAANGULARSIZE=trim($this->EXTRAANGULARSIZE);
		if (isset($this->EXTRAANGULARQTY)) $this->EXTRAANGULARQTY=trim($this->EXTRAANGULARQTY);
		if (isset($this->SQFEETPRICE)) $this->SQFEETPRICE=trim($this->SQFEETPRICE);
		if (isset($this->PRODUCTTYPE)) $this->PRODUCTTYPE=trim($this->PRODUCTTYPE);
		if (isset($this->COLOR)) $this->COLOR=trim($this->COLOR);
		if (isset($this->MATERIAL)) $this->MATERIAL=trim($this->MATERIAL);
		if (isset($this->PROVIDER)) $this->PROVIDER=trim($this->PROVIDER);
		if (isset($this->INSTFEE)) $this->INSTFEE=trim($this->INSTFEE);
		if (isset($this->TUBETYPE)) $this->TUBETYPE=trim($this->TUBETYPE);
		if (isset($this->TUBESIZE)) $this->TUBESIZE=trim($this->TUBESIZE);
		if (isset($this->TUBEQTY)) $this->TUBEQTY=trim($this->TUBEQTY);
				

		if (!$this->LOCKIN)
			$this->LOCKIN = NULL;
		if (!$this->LOCKSIZE)
			$this->LOCKSIZE = NULL;

		// Check parameters
		// Put here code to add a control on parameters values

		// Update request
		$sql = "UPDATE ".MAIN_DB_PREFIX."ea_po_item SET";
		
		$sql.= " POID=".(isset($this->POID)?"'".$this->db->escape($this->POID)."'":"null").",";
		$sql.= " LineNumber=".(isset($this->LineNumber)?"'".$this->db->escape($this->LineNumber)."'":"null").",";
		$sql.= " OPENINGW=".(isset($this->OPENINGW)?"'".$this->db->escape($this->OPENINGW)."'":"null").",";
		$sql.= " OPENINGHT=".(isset($this->OPENINGHT)?"'".$this->db->escape($this->OPENINGHT)."'":"null").",";
		$sql.= " TRACK=".(isset($this->TRACK)?"'".$this->db->escape($this->TRACK)."'":"null").",";
		$sql.= " TYPE=".(isset($this->TYPE)?"'".$this->db->escape($this->TYPE)."'":"null").",";
		$sql.= " BLADESQTY=".(isset($this->BLADESQTY)?"'".$this->db->escape($this->BLADESQTY)."'":"null").",";
		$sql.= " BLADESSTACK=".(isset($this->BLADESSTACK)?"'".$this->db->escape($this->BLADESSTACK)."'":"null").",";
		$sql.= " BLADESLONG=".(isset($this->BLADESLONG)?"'".$this->db->escape($this->BLADESLONG)."'":"null").",";
		$sql.= " `LEFT`=".(isset($this->LEFT)?"'".$this->db->escape($this->LEFT)."'":"null").",";
		$sql.= " `RIGHT`=".(isset($this->RIGHT)?"'".$this->db->escape($this->RIGHT)."'":"null").",";
		$sql.= " LOCKIN=".(isset($this->LOCKIN)?"'".$this->db->escape($this->LOCKIN)."'":"null").",";
		$sql.= " LOCKSIZE=".(isset($this->LOCKSIZE)?"'".$this->db->escape($this->LOCKSIZE)."'":"null").",";
		$sql.= " UPPERSIZE=".(isset($this->UPPERSIZE)?"'".$this->db->escape($this->UPPERSIZE)."'":"null").",";
		$sql.= " UPPERTYPE=".(isset($this->UPPERTYPE)?"'".$this->db->escape($this->UPPERTYPE)."'":"null").",";
		$sql.= " LOWERSIZE=".(isset($this->LOWERSIZE)?"'".$this->db->escape($this->LOWERSIZE)."'":"null").",";
		$sql.= " LOWERTYPE=".(isset($this->LOWERTYPE)?"'".$this->db->escape($this->LOWERTYPE)."'":"null").",";
		$sql.= " ANGULARTYPE=".(isset($this->ANGULARTYPE)?"'".$this->db->escape($this->ANGULARTYPE)."'":"null").",";
		$sql.= " ANGULARSIZE=".(isset($this->ANGULARSIZE)?"'".$this->db->escape($this->ANGULARSIZE)."'":"null").",";
		$sql.= " ANGULARQTY=".(isset($this->ANGULARQTY)?"'".$this->db->escape($this->ANGULARQTY)."'":"null").",";
		$sql.= " MOUNT=".(isset($this->MOUNT)?"'".$this->db->escape($this->MOUNT)."'":"null").",";
		$sql.= " ALUMINST=".(isset($this->ALUMINST)?"'".$this->db->escape($this->ALUMINST)."'":"null").",";
		$sql.= " LINEARFT=".(isset($this->LINEARFT)?"'".$this->db->escape($this->LINEARFT)."'":"null").",";
		$sql.= " OPENINGHT4=".(isset($this->OPENINGHT4)?"'".$this->db->escape($this->OPENINGHT4)."'":"null").",";
		$sql.= " ALUMINST4=".(isset($this->ALUMINST4)?"'".$this->db->escape($this->ALUMINST4)."'":"null").",";
		$sql.= " EST8HT=".(isset($this->EST8HT)?"'".$this->db->escape($this->EST8HT)."'":"null").",";
		$sql.= " ALUM=".(isset($this->ALUM)?"'".$this->db->escape($this->ALUM)."'":"null").",";
		$sql.= " WINDOWSTYPE=".(isset($this->WINDOWSTYPE)?"'".$this->db->escape($this->WINDOWSTYPE)."'":"null").",";
		$sql.= " EXTRAANGULARTYPE=".(isset($this->EXTRAANGULARTYPE)?"'".$this->db->escape($this->EXTRAANGULARTYPE)."'":"null").",";
		$sql.= " EXTRAANGULARSIZE=".(isset($this->EXTRAANGULARSIZE)?"'".$this->db->escape($this->EXTRAANGULARSIZE)."'":"null").",";
		$sql.= " EXTRAANGULARQTY=".(isset($this->EXTRAANGULARQTY)?"'".$this->db->escape($this->EXTRAANGULARQTY)."'":"null").",";
		$sql.= " SQFEETPRICE=".(isset($this->SQFEETPRICE)?"'".$this->db->escape($this->SQFEETPRICE)."'":"null").",";
		$sql.= " PRODUCTTYPE=".(isset($this->PRODUCTTYPE)?"'".$this->db->escape($this->PRODUCTTYPE)."'":"null").",";
		$sql.= " COLOR=".(isset($this->COLOR)?"'".$this->db->escape($this->COLOR)."'":"null").",";
		$sql.= " MATERIAL=".(isset($this->MATERIAL)?"'".$this->db->escape($this->MATERIAL)."'":"null").",";
		$sql.= " PROVIDER=".(isset($this->PROVIDER)?"'".$this->db->escape($this->PROVIDER)."'":"null").",";
		$sql.= " INSTFEE=".(isset($this->INSTFEE)?"'".$this->db->escape($this->INSTFEE)."'":"null").",";
		$sql.= " TUBETYPE=".(isset($this->TUBETYPE)?"'".$this->db->escape($this->TUBETYPE)."'":"null").",";
		$sql.= " TUBESIZE=".(isset($this->TUBESIZE)?"'".$this->db->escape($this->TUBESIZE)."'":"null").",";
		$sql.= " TUBEQTY=".(isset($this->TUBEQTY)?"'".$this->db->escape($this->TUBEQTY)."'":"null");

		$sql.= " WHERE PODescriptionID=".$this->PODescriptionID;

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
	 *  Delete object in database
	 *
	 *  @return	int <0 if KO, >0 if OK
	 */
	function delete()
	{
		global $conf, $langs;
		$error=0;

		$this->db->begin();

		if (! $error)
		{
			$sql = "DELETE FROM ".MAIN_DB_PREFIX."ea_po_item";
			$sql.= " WHERE PODescriptionID=".$this->PODescriptionID;

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
	 *  Delete objects for the given POID
	 *
	 *  @return	int <0 if KO, >0 if OK
	 */
	function deleteByPOID($id)
	{
		global $conf, $langs;
		$error=0;

		$this->db->begin();

		if (! $error)
		{
			$sql = "DELETE FROM ".MAIN_DB_PREFIX."ea_po_item";
			$sql.= " WHERE POID=".$id;

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
	 *	@param	int		$fromid	 Id of object to clone
	 * 	@return	int		New id of clone
	 */
	function createFromClone($fromid)
	{
		global $langs;

		$error=0;

		$object=new EaProductionOrderItems($this->db);

		$this->db->begin();

		// Load source object
		$object->fetch($fromid);
		$object->PODescriptionID=0;
		$object->ref = $object->PODescriptionID;

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
			return $object->PODescriptionID;
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
		$this->ref = '';
		
		$this->PODescriptionID='';
		$this->POID='';
		$this->LineNumber='';
		$this->OPENINGW='';
		$this->OPENINGHT='';
		$this->TRACK='';
		$this->TYPE='';
		$this->BLADESQTY='';
		$this->BLADESSTACK='';
		$this->BLADESLONG='';
		$this->LEFT='';
		$this->RIGHT='';
		$this->LOCKIN='';
		$this->LOCKSIZE='';
		$this->UPPERSIZE='';
		$this->UPPERTYPE='';
		$this->LOWERSIZE='';
		$this->LOWERTYPE='';
		$this->ANGULARTYPE='';
		$this->ANGULARSIZE='';
		$this->ANGULARQTY='';
		$this->MOUNT='';
		$this->ALUMINST='';
		$this->LINEARFT='';
		$this->OPENINGHT4='';
		$this->ALUMINST4='';
		$this->EST8HT='';
		$this->ALUM='';
		$this->WINDOWSTYPE='';
		$this->EXTRAANGULARTYPE='';
		$this->EXTRAANGULARSIZE='';
		$this->EXTRAANGULARQTY='';
		$this->SQFEETPRICE='';
		$this->PRODUCTTYPE='';
		$this->PRODUCTTYPENAME='';
		$this->COLOR='';
		$this->MATERIAL='';
		$this->PROVIDER='';
		$this->INSTFEE='';
		$this->TUBETYPE='';
		$this->TUBESIZE='';
		$this->TUBEQTY='';
	}

	/**
	 *	Return label of status (activity, closed)
	 */
	function getLibStatut($mode=0)
	{
		return ""; // Status not used for building departments
	}

	/**
	 *  Load objects for given Production Order from the database
	 */
	function fetchByPOID($id)
	{
		$result = -1;
		$sql = "SELECT";
		$sql.= " i.PODescriptionID,";
		$sql.= " i.POID,";
		$sql.= " i.LineNumber,";
		$sql.= " i.OPENINGW,";
		$sql.= " i.OPENINGHT,";
		$sql.= " i.TRACK,";
		$sql.= " i.TYPE,";
		$sql.= " i.BLADESQTY,";
		$sql.= " i.BLADESSTACK,";
		$sql.= " i.BLADESLONG,";
		$sql.= " i.`LEFT`,";
		$sql.= " i.`RIGHT`,";
		$sql.= " i.LOCKIN,";
		$sql.= " i.LOCKSIZE,";
		$sql.= " i.UPPERSIZE,";
		$sql.= " i.UPPERTYPE,";
		$sql.= " i.LOWERSIZE,";
		$sql.= " i.LOWERTYPE,";
		$sql.= " i.ANGULARTYPE,";
		$sql.= " i.ANGULARSIZE,";
		$sql.= " i.ANGULARQTY,";
		$sql.= " i.MOUNT,";
		$sql.= " i.ALUMINST,";
		$sql.= " i.LINEARFT,";
		$sql.= " i.OPENINGHT4,";
		$sql.= " i.ALUMINST4,";
		$sql.= " i.EST8HT,";
		$sql.= " i.ALUM,";
		$sql.= " i.WINDOWSTYPE,";
		$sql.= " i.EXTRAANGULARTYPE,";
		$sql.= " i.EXTRAANGULARSIZE,";
		$sql.= " i.EXTRAANGULARQTY,";
		$sql.= " i.SQFEETPRICE,";
		$sql.= " i.PRODUCTTYPE,";
		$sql.= " pt.name as PRODUCTTYPENAME,";
		$sql.= " i.COLOR,";
		$sql.= " i.MATERIAL,";
		$sql.= " i.PROVIDER,";
		$sql.= " i.INSTFEE,";
		$sql.= " i.TUBETYPE,";
		$sql.= " i.TUBESIZE,";
		$sql.= " i.TUBEQTY";
		$sql.= " FROM ".MAIN_DB_PREFIX."ea_po_item i";
		$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."ea_producttypes pt on i.PRODUCTTYPE=pt.id";
		$sql.= " WHERE i.POID = ".$id;
		$sql.= " ORDER BY i.LineNumber ";

		$return_arr = array();
		dol_syslog(get_class($this)."::fetchByPOID sql=".$sql, LOG_DEBUG);
		$resql=$this->db->query($sql);
		dol_syslog(get_class($this)."::fetchByPOID done!!!", LOG_DEBUG);
		if ($resql)
		{
			while ($obj = $this->db->fetch_object($resql))
			{
				$item = new EaProductionOrderItems($this->db);
				$item->PODescriptionID = $obj->PODescriptionID;
				$item->ref = $item->PODescriptionID;
				$item->POID = $obj->POID;
				$item->LineNumber = $obj->LineNumber;
				$item->OPENINGW = $obj->OPENINGW;
				$item->OPENINGHT = $obj->OPENINGHT;
				$item->TRACK = $obj->TRACK;
				$item->TYPE = $obj->TYPE;
				$item->BLADESQTY = $obj->BLADESQTY;
				$item->BLADESSTACK = $obj->BLADESSTACK;
				$item->BLADESLONG = $obj->BLADESLONG;
				$item->LEFT = $obj->LEFT;
				$item->RIGHT = $obj->RIGHT;
				$item->LOCKIN = $obj->LOCKIN;
				$item->LOCKSIZE = $obj->LOCKSIZE;
				$item->UPPERSIZE = $obj->UPPERSIZE;
				$item->UPPERTYPE = $obj->UPPERTYPE;
				$item->LOWERSIZE = $obj->LOWERSIZE;
				$item->LOWERTYPE = $obj->LOWERTYPE;
				$item->ANGULARTYPE = $obj->ANGULARTYPE;
				$item->ANGULARSIZE = $obj->ANGULARSIZE;
				$item->ANGULARQTY = $obj->ANGULARQTY;
				$item->MOUNT = $obj->MOUNT;
				$item->ALUMINST = $obj->ALUMINST;
				$item->LINEARFT = $obj->LINEARFT;
				$item->OPENINGHT4 = $obj->OPENINGHT4;
				$item->ALUMINST4 = $obj->ALUMINST4;
				$item->EST8HT = $obj->EST8HT;
				$item->ALUM = $obj->ALUM;
				$item->WINDOWSTYPE = $obj->WINDOWSTYPE;
				$item->EXTRAANGULARTYPE = $obj->EXTRAANGULARTYPE;
				$item->EXTRAANGULARSIZE = $obj->EXTRAANGULARSIZE;
				$item->EXTRAANGULARQTY = $obj->EXTRAANGULARQTY;
				$item->SQFEETPRICE = $obj->SQFEETPRICE;
				$item->PRODUCTTYPE = $obj->PRODUCTTYPE;
				$item->PRODUCTTYPENAME = $obj->PRODUCTTYPENAME;
				$item->COLOR = $obj->COLOR ? $obj->COLOR : "NONE";
				$item->MATERIAL = $obj->MATERIAL;
				$item->PROVIDER = $obj->PROVIDER;
				$item->INSTFEE = $obj->INSTFEE;
				$item->TUBETYPE = $obj->TUBETYPE;
				$item->TUBESIZE = $obj->TUBESIZE;
				$item->TUBEQTY = $obj->TUBEQTY;
				array_push($return_arr,$item);
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

	/*
	 * Gets all accordian items from the given PO's with given color
	 */
	static function fetchByColorsAndPOID($db, $color, $ids)
	{
		$result = -1;
		$sql = <<<EOD
			SELECT 
				pi.PODescriptionID
			FROM 
				llx_ea_po p 
				LEFT JOIN llx_ea_po_item pi ON p.POID = pi.POID 
			WHERE 
				p.POID in (${ids})
				and (
					('$color' = '' and (pi.COLOR is null || pi.COLOR = 'NONE' || pi.COLOR = ''))
					or (pi.COLOR = '$color' )
				)
				and pi.ProductType = 1
			ORDER BY 
				pi.COLOR
EOD;

		$return_arr = array();
		$resql=$db->query($sql);
		if ($resql)
		{
			while ($obj = $db->fetch_object($resql))
			{
				if (!is_null($obj->PODescriptionID))
				{
					$item = new EaProductionOrderItems($db);
					$item->fetch($obj->PODescriptionID);
					array_push($return_arr,$item);
				}
			}
		}
		$db->free($resql);

		return $return_arr;
	}


	static function posummary_fetchSUMBLADESLONG($db,$ids)
	{
		$result = -1;
		$sql = <<<EOD
			SELECT 
				pi.BLADESLONG, 
				Sum(pi.BLADESQTY) AS SumOfBLADESQTY 
			FROM 
				llx_ea_po_item pi
			WHERE 
				pi.PODescriptionID in (${ids}) 
				and pi.ProductType = 1
			GROUP BY 
				pi.BLADESLONG 
			ORDER BY 
				pi.BLADESLONG  DESC
EOD;

		$return_arr = array();
		$resql=$db->query($sql);
		if ($resql)
		{
			while ($obj = $db->fetch_object($resql))
			{
				array_push($return_arr,$obj);
			}
		}
		$db->free($resql);

		return $return_arr;
	}

	static function posummary_fetchSTARTERS($db,$ids)
	{
		$result = -1;
		$sql = <<<EOD
		SELECT 
			pi.BLADESLONG, 
			Count(pi.BLADESLONG) AS CountOfBLADESLONG, 
			Count(pi.BLADESLONG)*2 AS STARTERSQTY 
		FROM 
			llx_ea_po_item pi
		WHERE 
			pi.PODescriptionID in (${ids}) 
			and pi.ProductType = 1
		GROUP BY 
			pi.BLADESLONG
		ORDER BY 
			pi.BLADESLONG DESC 
EOD;

		$return_arr = array();
		$resql=$db->query($sql);
		if ($resql)
		{
			while ($obj = $db->fetch_object($resql))
			{
				array_push($return_arr,$obj);
			}
		}
		$db->free($resql);

		return $return_arr;
	}


	static function posummary_fetchCENTERMALE($db,$ids)
	{
		$result = -1;
		$sql = <<<EOD
		SELECT 
			pi.BLADESLONG, 
			Count(pi.BLADESLONG) AS CountOfBLADESLONG 
		FROM 
			llx_ea_po_item pi
		WHERE 
			pi.PODescriptionID in (${ids}) 
			and pi.ProductType = 1
		GROUP BY 
			pi.BLADESLONG 
		ORDER BY 
			pi.BLADESLONG DESC
EOD;

		$return_arr = array();
		$resql=$db->query($sql);
		if ($resql)
		{
			while ($obj = $db->fetch_object($resql))
			{
				array_push($return_arr,$obj);
			}
		}
		$db->free($resql);

		return $return_arr;
	}

	static function posummary_fetchCENTERFEMALE($db,$ids)
	{
		$result = -1;
		$sql = <<<EOD
		SELECT 
			pi.BLADESLONG, 
			Count(pi.BLADESLONG) AS CountOfBLADESLONG 
		FROM 
			llx_ea_po_item pi
		WHERE 
			pi.PODescriptionID in (${ids}) 
			and pi.ProductType = 1
		GROUP BY 
			pi.BLADESLONG 
		ORDER BY 
			pi.BLADESLONG DESC
EOD;

		$return_arr = array();
		$resql=$db->query($sql);
		if ($resql)
		{
			while ($obj = $db->fetch_object($resql))
			{
				array_push($return_arr,$obj);
			}
		}
		$db->free($resql);

		return $return_arr;
	}

	static function posummary_fetchUPPERTRACK($db,$ids)
	{
		$result = -1;
		$sql = <<<EOD
		SELECT 
			pi.UPPERTYPE, 
			pi.UPPERSIZE,
			Count(pi.UPPERSIZE) AS CountOfUPPERSIZE 
		FROM 
			llx_ea_po_item pi
		WHERE 
			pi.PODescriptionID in (${ids}) 
			and pi.ProductType = 1
		GROUP BY 
			pi.UPPERTYPE, 
			pi.UPPERSIZE
		ORDER BY 
			pi.UPPERTYPE, pi.UPPERSIZE DESC
EOD;

		$return_arr = array();
		$resql=$db->query($sql);
		if ($resql)
		{
			while ($obj = $db->fetch_object($resql))
			{
				array_push($return_arr,$obj);
			}
		}
		$db->free($resql);

		return $return_arr;
	}

	static function posummary_fetchLOWERTRACK($db,$ids)
	{
		$result = -1;
		$sql = <<<EOD
		SELECT 
			pi.LOWERTYPE, 
			pi.LOWERSIZE,
			Count(pi.LOWERSIZE) AS CountOfLOWERSIZE 
		FROM 
			llx_ea_po_item pi
		WHERE 
			pi.PODescriptionID in (${ids}) 
			and pi.ProductType = 1
		GROUP BY 
			pi.LOWERTYPE, 
			pi.LOWERSIZE
		ORDER BY 
			pi.LOWERTYPE, pi.LOWERSIZE DESC
EOD;

		$return_arr = array();
		$resql=$db->query($sql);
		if ($resql)
		{
			while ($obj = $db->fetch_object($resql))
			{
				array_push($return_arr,$obj);
			}
		}
		$db->free($resql);

		return $return_arr;
	}

	static function posummary_fetchANGULAR($db,$ids)
	{
		$result = -1;
		$sql = <<<EOD
		SELECT 
			pi.ANGULARTYPE, 
			pi.ANGULARSIZE, 
			Sum(pi.ANGULARQTY) AS SumOfANGULARQTY 
		FROM 
			llx_ea_po_item pi
		WHERE 
			pi.PODescriptionID in (${ids}) 
			and pi.ProductType = 1
		GROUP BY 
			pi.ANGULARTYPE, 
			pi.ANGULARSIZE 
		ORDER BY 
			pi.ANGULARTYPE, 
			pi.ANGULARSIZE DESC
EOD;

		$return_arr = array();
		$resql=$db->query($sql);
		if ($resql)
		{
			while ($obj = $db->fetch_object($resql))
			{
				array_push($return_arr,$obj);
			}
		}
		$db->free($resql);

		return $return_arr;
	}

	static function posummary_fetchEXTRAANG($db,$ids)
	{
		$result = -1;
		$sql = <<<EOD
		SELECT 
			pi.EXTRAANGULARTYPE, 
			pi.EXTRAANGULARSIZE, 
			Sum(pi.EXTRAANGULARQTY) AS SumOfEXTRAANGULARQTY 
		FROM 
			llx_ea_po_item pi
		WHERE 
			pi.PODescriptionID in (${ids}) 
			and pi.ProductType = 1
		GROUP BY 
			pi.EXTRAANGULARTYPE, 
			pi.EXTRAANGULARSIZE 
		ORDER BY 
			pi.EXTRAANGULARTYPE, 
			pi.EXTRAANGULARSIZE DESC
EOD;

		$return_arr = array();
		$resql=$db->query($sql);
		if ($resql)
		{
			while ($obj = $db->fetch_object($resql))
			{
				array_push($return_arr,$obj);
			}
		}
		$db->free($resql);

		return $return_arr;
	}

	static function posummary_fetchTUBES($db,$ids)
	{
		$result = -1;
		$sql = <<<EOD
		SELECT 
			pi.TUBETYPE, 
			pi.TUBESIZE, 
			Sum(pi.TUBEQTY) AS SumOfTUBEQTY 
		FROM 
			llx_ea_po_item pi
		WHERE 
			pi.PODescriptionID in (${ids}) 
			and pi.ProductType = 1
		GROUP BY 
			pi.TUBETYPE, 
			pi.TUBESIZE 
		ORDER BY 
			pi.TUBETYPE, 
			pi.TUBESIZE DESC
EOD;

		$return_arr = array();
		$resql=$db->query($sql);
		if ($resql)
		{
			while ($obj = $db->fetch_object($resql))
			{
				array_push($return_arr,$obj);
			}
		}
		$db->free($resql);

		return $return_arr;
	}
}
?>
