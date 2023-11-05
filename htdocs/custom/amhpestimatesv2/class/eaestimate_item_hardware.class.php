<?php
// Put here all includes required by your class file
require_once(DOL_DOCUMENT_ROOT."/core/class/commonobject.class.php");

/**
 *	This class is a CRUD wrapper for accessing the llx_ea_est_hardware table
 */
class EaEstimateItemHardware extends CommonObject
{
	var $db;							//!< To store db handler
	var $error;							//!< To return error code (or message)
	var $errors=array();				//!< To return several error codes (or messages)
	var $element='eaesthardware';			//!< Id that identify managed objects
	var $table_element='ea_est_hardware';		//!< Name of table without prefix where object is stored
	var $specimen;

	var $id;
	var $estimateitemid;
	var $provider;
	var $product_ref;
	var $hardwaretype;
	var $configuration;

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
		if (isset($this->estimateitemid)) $this->estimateitemid=trim($this->estimateitemid);
		if (isset($this->provider)) $this->provider=trim($this->provider);
		if (isset($this->product_ref)) $this->product_ref=trim($this->product_ref);
		if (isset($this->hardwaretype)) $this->hardwaretype=trim($this->hardwaretype);
		if (isset($this->configuration)) $this->configuration=trim($this->configuration);
	
		if (!$error)
		{
			// Insert request
			$sql = "INSERT INTO ".MAIN_DB_PREFIX."ea_est_hardware (";
			
			$sql.= "estimateitemid,";
			$sql.= "provider,";
			$sql.= "product_ref,";
			$sql.= "hardwaretype,";
			$sql.= "configuration";
				
			$sql.= ") VALUES (";

			$sql.= " ".(! isset($this->estimateitemid)?'NULL':"'".$this->db->escape($this->estimateitemid)."'").",";
			$sql.= " ".(! isset($this->provider)?'NULL':"'".$this->db->escape($this->provider)."'").",";
			$sql.= " ".(! isset($this->product_ref)?'NULL':"'".$this->db->escape($this->product_ref)."'").",";
			$sql.= " ".(! isset($this->hardwaretype)?'NULL':"'".$this->db->escape($this->hardwaretype)."'").",";
			$sql.= " ".(! isset($this->configuration)?'NULL':"'".$this->db->escape($this->configuration)."'")."";
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
		$sql = "CALL get_estimate_item_hardware(".$id.")";

		dol_syslog(get_class($this)."::fetch sql=".$sql, LOG_DEBUG);
		$resql=$this->db->query($sql);
		if ($resql)
		{
			if ($this->db->num_rows($resql))
			{
				$obj = $this->db->fetch_object($resql);
				$this->ref = $obj->id;
								
				$this->id = $obj->id; 
				$this->estimateitemid = $obj->estimateitemid;
				$this->provider = $obj->provider;
				$this->product_ref = $obj->product_ref;
				$this->hardwaretype = $obj->hardwaretype;
				$this->configuration = $obj->configuration;
			}

			$this->db->free($resql);
			$this->db->db->next_result(); // Stored procedure returns an extra result set :(
			return $this->id > 0 ? 1 : -1;
		}
		else
		{
	  		$this->error="Error ".$this->db->lasterror();
			dol_syslog(get_class($this)."::fetch ".$this->error, LOG_ERR);
			return -1;
		}
	}

	/**
	 *  Duplicate an hardware item
	 *
	 *  @param	int		$id	Id object
	 *  @return int		The id of the hardware item to copy
	 */
	function duplicate($id, $newItemId)
	{
		global $langs;
		$newid = 0;
		$sql = "CALL copy_estimate_item_hardware(".$id.",".$newItemId.")";

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
		// if (isset($this->estimateitemid)) $this->estimateitemid=trim($this->estimateitemid); // must not update
		if (isset($this->provider)) $this->provider=trim($this->provider);
		if (isset($this->product_ref)) $this->product_ref=trim($this->product_ref);
		if (isset($this->hardwaretype)) $this->hardwaretype=trim($this->hardwaretype);
		if (isset($this->configuration)) $this->configuration=trim($this->configuration);
	
		// Check parameters
		// Put here code to add a control on parameters values

		// Update request
		$sql = "UPDATE ".MAIN_DB_PREFIX."ea_est_hardware SET";
		
		// $sql.= " estimateitemid=".(isset($this->estimateitemid)?"'".$this->db->escape($this->estimateitemid)."'":"null").","; // mus not update
		$sql.= " provider=".(isset($this->provider)?"'".$this->db->escape($this->provider)."'":"null").",";
		$sql.= " product_ref=".(isset($this->product_ref)?"'".$this->db->escape($this->product_ref)."'":"null").",";
		$sql.= " hardwaretype=".(isset($this->hardwaretype)?"'".$this->db->escape($this->hardwaretype)."'":"null").",";
		$sql.= " configuration=".(isset($this->configuration)?"'".$this->db->escape($this->configuration)."'":"null")."";
	
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
			//$itemObject = new EaProductionOrderItems($this->db);
			$result = true;//$itemObject->deleteByID($this->id);

			if (!$result)
			{
				$error++; 
				$this->error .= ($this->error?', ':'');//.$itemObject->error;
			}
			else
			{
				$sql = "DELETE FROM ".MAIN_DB_PREFIX."ea_est_hardware";
				$sql.= " WHERE id=".$this->id;

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

		$object=new EaEstimateItemHardware($this->db);

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
		$sql.= " FROM ".MAIN_DB_PREFIX."ea_est_hardware as e";

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
			$this->estimateitemid='1';
			$this->provider='';
			$this->product_ref='';
			$this->hardwaretype='';
			$this->configuration='';
		}

		$this->db->free($resql);

	}


	function propsToJSON()
	{
		$res = '{'.PHP_EOL.
			'   "id": ' . $obj->id . ','.PHP_EOL.
			'   "estimateitemid": ' . $obj->estimateitemid . ',' . PHP_EOL .
			'   "provider": "' . cleanTxt($obj->provider) . '",' . PHP_EOL .
			'   "product_ref": "' . cleanTxt($obj->product_ref) . '",' . PHP_EOL .
			'   "hardwaretype": "' . cleanTxt($obj->hardwaretype) . '",' . PHP_EOL .
			'   "configuration": "' . cleanTxt($obj->configuration) . '"' . PHP_EOL .
			'}';
		return $res;
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
			$e = new EaEstimateItemHardware($this->db);
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
