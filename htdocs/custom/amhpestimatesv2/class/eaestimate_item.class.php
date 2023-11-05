<?php
// Put here all includes required by your class file
require_once(DOL_DOCUMENT_ROOT."/core/class/commonobject.class.php");

/**
 *	This class is a CRUD wrapper for accessing the llx_ea_estimate_item table
 */
class EaEstimateItem extends CommonObject
{
	var $db;							//!< To store db handler
	var $error;							//!< To return error code (or message)
	var $errors=array();				//!< To return several error codes (or messages)
	var $element='eaestimateitem';			//!< Id that identify managed objects
	var $table_element='ea_estimate_item';		//!< Name of table without prefix where object is stored
	var $specimen;

	var $id;
	var $estimateid;
	var $itemno;
	var $itemtype;
	var $modtype;
	var $wintype;
	var $name;
	var $image;
	var $color;
	var $cost_price;
	var $sales_price;
	var $sales_discount;
	var $inst_price;
	var $inst_discount;
	var $otherfees;
	var $finalprice;
	var $quantity;

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
		if (isset($this->estimateid)) $this->estimateid=trim($this->estimateid);
		if (isset($this->itemno)) $this->itemno=trim($this->itemno);
		if (isset($this->itemtype)) $this->itemtype=trim($this->itemtype);
		if (isset($this->modtype)) $this->modtype=trim($this->modtype);
		if (isset($this->wintype)) $this->wintype=trim($this->wintype);
		if (isset($this->name)) $this->name=trim($this->name);
		if (isset($this->image)) $this->image=trim($this->image);
		if (isset($this->color)) $this->color=trim($this->color);
		if (isset($this->cost_price)) $this->cost_price=trim($this->cost_price);
		if (isset($this->sales_price)) $this->sales_price=trim($this->sales_price);
		if (isset($this->sales_discount)) $this->sales_discount=trim($this->sales_discount);
		if (isset($this->inst_price)) $this->inst_price=trim($this->inst_price);
		if (isset($this->inst_discount)) $this->inst_discount=trim($this->inst_discount);
		if (isset($this->otherfees)) $this->otherfees=trim($this->otherfees);
		if (isset($this->finalprice)) $this->finalprice=trim($this->finalprice);
		if (isset($this->quantity)) $this->quantity=trim($this->quantity);
			
		if (!$error)
		{
			// Insert request
			$sql = "INSERT INTO ".MAIN_DB_PREFIX."ea_estimate_item (";
			
			$sql.= "estimateid,";
			$sql.= "itemno,";
			$sql.= "itemtype,";
			$sql.= "modtype,";
			$sql.= "wintype,";
			$sql.= "name,";
			$sql.= "image,";
			$sql.= "color,";
			$sql.= "cost_price,";
			$sql.= "sales_price,";
			$sql.= "sales_discount,";
			$sql.= "inst_price,";
			$sql.= "inst_discount,";
			$sql.= "otherfees,";
			$sql.= "finalprice,";
			$sql.= "quantity";
		
			$sql.= ") VALUES (";

			$sql.= " ".(! isset($this->estimateid)?'NULL':"'".$this->db->escape($this->estimateid)."'").",";
			$sql.= " ".(! isset($this->itemno)?'NULL':"'".$this->db->escape($this->itemno)."'").",";
			$sql.= " ".(! isset($this->itemtype)?'NULL':"'".$this->db->escape($this->itemtype)."'").",";
			$sql.= " ".(! isset($this->modtype)?'NULL':"'".$this->db->escape($this->modtype)."'").",";
			$sql.= " ".(! isset($this->wintype)?'NULL':"'".$this->db->escape($this->wintype)."'").",";
			$sql.= " ".(! isset($this->name)?'NULL':"'".$this->db->escape($this->name)."'").",";
			$sql.= " ".(! isset($this->image)?'NULL':"'".$this->db->escape($this->image)."'").",";
			$sql.= " ".(! isset($this->color)?'NULL':"'".$this->db->escape($this->color)."'").",";
			$sql.= " ".(! isset($this->cost_price)?'NULL':"'".$this->db->escape($this->cost_price)."'").",";
			$sql.= " ".(! isset($this->sales_price)?'NULL':"'".$this->db->escape($this->sales_price)."'").",";
			$sql.= " ".(! isset($this->sales_discount)?'NULL':"'".$this->db->escape($this->sales_discount)."'").",";
			$sql.= " ".(! isset($this->inst_price)?'NULL':"'".$this->db->escape($this->inst_price)."'").",";
			$sql.= " ".(! isset($this->inst_discount)?'NULL':"'".$this->db->escape($this->inst_discount)."'").",";
			$sql.= " ".(! isset($this->otherfees)?'NULL':"'".$this->db->escape($this->otherfees)."'").",";
			$sql.= " ".(! isset($this->finalprice)?'NULL':"'".$this->db->escape($this->finalprice)."'").",";
			$sql.= " ".(! isset($this->quantity)?'NULL':"'".$this->db->escape($this->quantity)."'");

			$sql.= ")";

			$this->db->begin();

			dol_syslog(get_class($this)."::create sql=".$sql, LOG_DEBUG);
			$resql=$this->db->query($sql);
			if (! $resql) { $error++; $this->errors[]="Error ".$this->db->lasterror(); }

			if (! $error)
			{
				$this->id = $this->db->last_insert_id(MAIN_DB_PREFIX."ea_estimate_item");
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
		$sql = "CALL get_estimate_item(".$id.")";

		dol_syslog(get_class($this)."::fetch sql=".$sql, LOG_DEBUG);
		$resql=$this->db->query($sql);
		if ($resql)
		{
			if ($this->db->num_rows($resql) > 0) {
				$obj = $this->db->fetch_object($resql);
				$this->ref = $obj->id;

				$this->id = $obj->id; 
				$this->estimateid = $obj->estimateid; 
				$this->itemno = $obj->itemno; 
				$this->itemtype = $obj->itemtype; 
				$this->modtype = $obj->modtype; 
				$this->wintype = $obj->wintype; 
				$this->name = $obj->name; 
				$this->image = $obj->image; 
				$this->color = $obj->color; 
				$this->cost_price = $obj->cost_price; 
				$this->sales_discount = $obj->sales_discount;
				$this->inst_price = $obj->inst_price;
				$this->inst_discount = $obj->inst_discount;
				$this->otherfees = $obj->otherfees;
				$this->finalprice = $obj->finalprice;
				$this->quantity = $obj->quantity;

				$this->db->free($resql);
				$this->db->db->next_result(); // Stored procedure returns an extra result set :(
				return 1;
			}
			return 0;
		}
		else
		{
	  		$this->error="Error ".$this->db->lasterror();
			dol_syslog(get_class($this)."::fetch ".$this->error, LOG_ERR);
			return -1;
		}
	}

	/**
	 *  Duplicate an item
	 *
	 *  @param	int		$id	Id object
	 *  @return int		The rowid of the new item
	 */
	function duplicate($id)
	{
		global $langs;
		$newrowid = 0;
		$sql = "CALL copy_estimate_item(".$id.")";

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
		if (isset($this->estimateid)) $this->estimateid=trim($this->estimateid);
		if (isset($this->itemno)) $this->itemno=trim($this->itemno);
		if (isset($this->itemtype)) $this->itemtype=trim($this->itemtype);
		if (isset($this->modtype)) $this->modtype=trim($this->modtype);
		if (isset($this->wintype)) $this->wintype=trim($this->wintype);
		if (isset($this->name)) $this->name=trim($this->name);
		if (isset($this->image)) $this->image=trim($this->image);
		if (isset($this->color)) $this->color=trim($this->color);
		if (isset($this->cost_price)) $this->cost_price=trim($this->cost_price);
		if (isset($this->sales_price)) $this->sales_price=trim($this->sales_price);
		if (isset($this->sales_discount)) $this->sales_discount=trim($this->sales_discount);
		if (isset($this->inst_price)) $this->inst_price=trim($this->inst_price);
		if (isset($this->inst_discount)) $this->inst_discount=trim($this->inst_discount);
		if (isset($this->otherfees)) $this->otherfees=trim($this->otherfees);
		if (isset($this->finalprice)) $this->finalprice=trim($this->finalprice);
		if (isset($this->quantity)) $this->quantity=trim($this->quantity);

		// Check parameters
		// Put here code to add a control on parameters values

		// Update request
		$sql = "UPDATE ".MAIN_DB_PREFIX."ea_estimate_item SET";
		
		$sql.= " estimateid=".(isset($this->estimateid)?"'".$this->db->escape($this->estimateid)."'":"null").",";
		$sql.= " itemno=".(isset($this->itemno)?"'".$this->db->escape($this->itemno)."'":"null").",";
		$sql.= " itemtype=".(isset($this->itemtype)?"'".$this->db->escape($this->itemtype)."'":"null").",";
		$sql.= " modtype=".(isset($this->modtype)?"'".$this->db->escape($this->modtype)."'":"null").",";
		$sql.= " wintype=".(isset($this->wintype)?"'".$this->db->escape($this->wintype)."'":"null").",";
		$sql.= " name=".(isset($this->name)?"'".$this->db->escape($this->name)."'":"null").",";
		$sql.= " image=".(isset($this->image)?"'".$this->db->escape($this->image)."'":"null").",";
		$sql.= " color=".(isset($this->color)?"'".$this->db->escape($this->color)."'":"null").",";
		$sql.= " cost_price=".(isset($this->cost_price)?"'".$this->db->escape($this->cost_price)."'":"null").",";
		$sql.= " sales_price=".(isset($this->sales_price)?"'".$this->db->escape($this->sales_price)."'":"null").",";
		$sql.= " sales_discount=".(isset($this->sales_discount)?"'".$this->db->escape($this->sales_discount)."'":"null").",";
		$sql.= " inst_price=".(isset($this->inst_price)?"'".$this->db->escape($this->inst_price)."'":"null").",";
		$sql.= " inst_discount=".(isset($this->inst_discount)?"'".$this->db->escape($this->inst_discount)."'":"null").",";
		$sql.= " otherfees=".(isset($this->otherfees)?"'".$this->db->escape($this->otherfees)."'":"null").",";
		$sql.= " finalprice=".(isset($this->finalprice)?"'".$this->db->escape($this->finalprice)."'":"null").",";
		$sql.= " quantity=".(isset($this->quantity)?"'".$this->db->escape($this->quantity)."'":"null");

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
		if (!$this->id)
			return 0;

		$this->db->begin();
		if (! $error)
		{
			$sql = "DELETE FROM ".MAIN_DB_PREFIX."ea_estimate_item";
			$sql.= " WHERE id=".$this->id;

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

		$object=new EaEstimate($this->db);

		$this->db->begin();

		// Load source object
		$object->fetch($fromid);
		$object->rowid=0;
		$object->ref = $object->rowid;

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
			return $object->rowid;
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
		$sql.= " max(i.rowid) as rowid";		
		$sql.= " FROM ".MAIN_DB_PREFIX."ea_estimate_item as i";

		$return_arr = array();
		$resql=$this->db->query($sql);
		if ($resql)
		{
			while ($obj = $this->db->fetch_object($resql))
			{
				$result = $this->fetch($obj->rowid);
				$this->fetchItems();
				break;
			}
		}
		else
		{
			$this->ref = '';
		
			$this->id=1;
			$this->estimateid=1;
			$this->itemno=1;
			$this->itemtype='Window';
			$this->modtype='Impact Product';
			$this->wintype='Single Hung Series';
			$this->name='';
			$this->image='';
			$this->color='';
			$this->cost_price=0;
			$this->sales_price=0;
			$this->sales_discount=0;
			$this->inst_price=0;
			$this->inst_discount=0;
			$this->otherfees=0;
			$this->finalprice=0;
			$this->quantity=1;
			$this->folionumber='';
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
}
?>
