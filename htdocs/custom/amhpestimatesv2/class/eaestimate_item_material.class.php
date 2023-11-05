<?php
// Put here all includes required by your class file
require_once(DOL_DOCUMENT_ROOT."/core/class/commonobject.class.php");

/**
 *	This class is a CRUD wrapper for accessing the llx_ea_est_material table
 */
class EaEstimateItemMaterial extends CommonObject
{
	var $db;							//!< To store db handler
	var $error;							//!< To return error code (or message)
	var $errors=array();				//!< To return several error codes (or messages)
	var $element='eaestmaterial';			//!< Id that identify managed objects
	var $table_element='ea_est_material';		//!< Name of table without prefix where object is stored
	var $specimen;

	var $id;
	var $estimateitemid;
	var $provider;
	var $product_ref;
	var $width;
	var $widthtxt;
	var $height;
	var $heighttxt;
	var $length;
	var $lengthtxt;

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
		if (isset($this->width)) $this->width=trim($this->width);
		if (isset($this->widthtxt)) $this->widthtxt=trim($this->widthtxt);
		if (isset($this->height)) $this->height=trim($this->height);
		if (isset($this->heighttxt)) $this->heighttxt=trim($this->heighttxt);
		if (isset($this->length)) $this->length=trim($this->length);
		if (isset($this->lengthtxt)) $this->lengthtxt=trim($this->lengthtxt);

		if (!$error)
		{
			// Insert request
			$sql = "INSERT INTO ".MAIN_DB_PREFIX."ea_est_material (";
			
			$sql.= "estimateitemid,";
			$sql.= "provider,";
			$sql.= "product_ref,";
			$sql.= "width,";
			$sql.= "widthtxt,";
			$sql.= "height,";
			$sql.= "heighttxt,";
			$sql.= "length,";
			$sql.= "lengthtxt";
				
			$sql.= ") VALUES (";

			$sql.= " ".(! isset($this->estimateitemid)?'NULL':"'".$this->db->escape($this->estimateitemid)."'").",";
			$sql.= " ".(! isset($this->provider)?'NULL':"'".$this->db->escape($this->provider)."'").",";
			$sql.= " ".(! isset($this->product_ref)?'NULL':"'".$this->db->escape($this->product_ref)."'").",";
			$sql.= " ".(! isset($this->width)?'NULL':"'".$this->db->escape($this->width)."'").",";
			$sql.= " ".(! isset($this->widthtxt)?'NULL':"'".$this->db->escape($this->widthtxt)."'").",";
			$sql.= " ".(! isset($this->height)?'NULL':"'".$this->db->escape($this->height)."'").",";
			$sql.= " ".(! isset($this->heighttxt)?'NULL':"'".$this->db->escape($this->heighttxt)."'").",";
			$sql.= " ".(! isset($this->length)?'NULL':"'".$this->db->escape($this->length)."'").",";
			$sql.= " ".(! isset($this->lengthtxt)?'NULL':"'".$this->db->escape($this->lengthtxt)."'");
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
		$sql = "CALL get_estimate_item_material(".$id.")";

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
				$this->width = $obj->width;
				$this->widthtxt = $obj->widthtxt;
				$this->height = $obj->height;
				$this->heighttxt = $obj->heighttxt;
				$this->length = $obj->length;
				$this->lengthtxt = $obj->lengthtxt;
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
	 *  Duplicate an material item
	 *
	 *  @param	int		$id	Id object
	 *  @return int		The id of the material item to copy
	 */
	function duplicate($id, $newItemId)
	{
		global $langs;
		$newid = 0;
		$sql = "CALL copy_estimate_item_material(".$id.",".$newItemId.")";

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
		if (isset($this->width)) $this->width=trim($this->width);
		if (isset($this->widthtxt)) $this->widthtxt=trim($this->widthtxt);
		if (isset($this->height)) $this->height=trim($this->height);
		if (isset($this->heighttxt)) $this->heighttxt=trim($this->heighttxt);
		if (isset($this->length)) $this->length=trim($this->length);
		if (isset($this->lengthtxt)) $this->lengthtxt=trim($this->lengthtxt);
	
		// Check parameters
		// Put here code to add a control on parameters values

		// Update request
		$sql = "UPDATE ".MAIN_DB_PREFIX."ea_est_material SET";
		
		// $sql.= " estimateitemid=".(isset($this->estimateitemid)?"'".$this->db->escape($this->estimateitemid)."'":"null").","; // must not update
		$sql.= " provider=".(isset($this->provider)?"'".$this->db->escape($this->provider)."'":"null").",";
		$sql.= " product_ref=".(isset($this->product_ref)?"'".$this->db->escape($this->product_ref)."'":"null").",";
		$sql.= " width=".(isset($this->width)?"'".$this->db->escape($this->width)."'":"null").",";
		$sql.= " widthtxt=".(isset($this->widthtxt)?"'".$this->db->escape($this->widthtxt)."'":"null").",";
		$sql.= " height=".(isset($this->height)?"'".$this->db->escape($this->height)."'":"null").",";
		$sql.= " heighttxt=".(isset($this->heighttxt)?"'".$this->db->escape($this->heighttxt)."'":"null").",";
		$sql.= " length=".(isset($this->length)?"'".$this->db->escape($this->length)."'":"null").",";
		$sql.= " lengthtxt=".(isset($this->lengthtxt)?"'".$this->db->escape($this->lengthtxt)."'":"null");
		
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
				$sql = "DELETE FROM ".MAIN_DB_PREFIX."ea_est_material";
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

		$object=new EaEstimateItemMaterial($this->db);

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
		$sql.= " FROM ".MAIN_DB_PREFIX."ea_est_material as e";

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
			$this->width='';
			$this->widthtxt='';
			$this->height='';
			$this->heighttxt='';
			$this->length='';
			$this->lengthtxt='';
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
			'   "width": ' . $obj->width . ',' . PHP_EOL .
			'   "widthtxt": ' . $obj->widthtxt . ',' . PHP_EOL .
			'   "height": ' . $obj->height . ',' . PHP_EOL .
			'   "heighttxt": ' . $obj->heighttxt . ',' . PHP_EOL .
			'   "length": ' . $obj->length . ',' . PHP_EOL .
			'   "lengthtxt": ' . $obj->lengthtxt . PHP_EOL .
			
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
			$e = new EaEstimateItemMaterial($this->db);
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
