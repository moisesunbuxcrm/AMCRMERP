<?php
// Put here all includes required by your class file
require_once(DOL_DOCUMENT_ROOT."/core/class/commonobject.class.php");

/**
 *	This class is a CRUD wrapper for accessing the llx_ea_est_impact table
 */
class EaEstimateItemImpact extends CommonObject
{
	var $db;							//!< To store db handler
	var $error;							//!< To return error code (or message)
	var $errors=array();				//!< To return several error codes (or messages)
	var $element='eaestimpact';			//!< Id that identify managed objects
	var $table_element='ea_est_impact';		//!< Name of table without prefix where object is stored
	var $specimen;

	var $id;
	var $estimateitemid;
	var $provider;
	var $is_def_color;
	var $is_def_glass_color;
	var $is_standard;
	var $roomtype;
	var $roomnum;
	var $floornum;
	var $product_ref;
	var $configuration;
	var $is_screen;
	var $frame_color;
	var $is_colonial;
	var $colonial_fee;
	var $colonial_across;
	var $colonial_down;
	var $width;
	var $widthtxt;
	var $height;
	var $heighttxt;
	var $length;
	var $lengthtxt;
	var $glass_type;
	var $glass_color;
	var $interlayer;
	var $coating;
	var $room_description;

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
		if (isset($this->is_def_color)) $this->is_def_color=trim($this->is_def_color);
		if (isset($this->is_def_glass_color)) $this->is_def_glass_color=trim($this->is_def_glass_color);
		if (isset($this->is_standard)) $this->is_standard=trim($this->is_standard);
		if (isset($this->roomtype)) $this->roomtype=trim($this->roomtype);
		if (isset($this->roomnum)) $this->roomnum=trim($this->roomnum);
		if (isset($this->floornum)) $this->floornum=trim($this->floornum);
		if (isset($this->product_ref)) $this->product_ref=trim($this->product_ref);
		if (isset($this->configuration)) $this->configuration=trim($this->configuration);
		if (isset($this->is_screen)) $this->is_screen=trim($this->is_screen);
		if (isset($this->frame_color)) $this->frame_color=trim($this->frame_color);
		if (isset($this->is_colonial)) $this->is_colonial=trim($this->is_colonial);
		if (isset($this->colonial_fee)) $this->colonial_fee=trim($this->colonial_fee);
		if (isset($this->colonial_across)) $this->colonial_across=trim($this->colonial_across);
		if (isset($this->colonial_down)) $this->colonial_down=trim($this->colonial_down);
		if (isset($this->width)) $this->width=trim($this->width);
		if (isset($this->widthtxt)) $this->widthtxt=trim($this->widthtxt);
		if (isset($this->height)) $this->height=trim($this->height);
		if (isset($this->heighttxt)) $this->heighttxt=trim($this->heighttxt);
		if (isset($this->length)) $this->length=trim($this->length);
		if (isset($this->lengthtxt)) $this->lengthtxt=trim($this->lengthtxt);
		if (isset($this->glass_type)) $this->glass_type=trim($this->glass_type);
		if (isset($this->glass_color)) $this->glass_color=trim($this->glass_color);
		if (isset($this->interlayer)) $this->interlayer=trim($this->interlayer);
		if (isset($this->coating)) $this->coating=trim($this->coating);
		if (isset($this->room_description)) $this->room_description=trim($this->room_description);

		if (!$error)
		{
			// Insert request
			$sql = "INSERT INTO ".MAIN_DB_PREFIX."ea_est_impact (";
			
			$sql.= "estimateitemid,";
			$sql.= "provider,";
			$sql.= "is_def_color,";
			$sql.= "is_def_glass_color,";
			$sql.= "is_standard,";
			$sql.= "roomtype,";
			$sql.= "roomnum,";
			$sql.= "floornum,";
			$sql.= "product_ref,";
			$sql.= "configuration,";
			$sql.= "is_screen,";
			$sql.= "frame_color,";
			$sql.= "is_colonial,";
			$sql.= "colonial_fee,";
			$sql.= "colonial_across,";
			$sql.= "colonial_down,";
			$sql.= "width,";
			$sql.= "widthtxt,";
			$sql.= "height,";
			$sql.= "heighttxt,";
			$sql.= "length,";
			$sql.= "lengthtxt,";
			$sql.= "glass_type,";
			$sql.= "glass_color,";
			$sql.= "interlayer,";
			$sql.= "coating,";
			$sql.= "room_description";
				
			$sql.= ") VALUES (";

			$sql.= " ".(! isset($this->estimateitemid)?'NULL':"'".$this->db->escape($this->estimateitemid)."'").",";
			$sql.= " ".(! isset($this->provider)?'NULL':"'".$this->db->escape($this->provider)."'").",";
			$sql.= " ".($this->is_def_color==true?"1":"0").",";
			$sql.= " ".($this->is_def_glass_color==true?"1":"0").",";
			$sql.= " ".($this->is_standard==true?"1":"0").",";
			$sql.= " ".(! isset($this->roomtype)?'NULL':"'".$this->db->escape($this->roomtype)."'").",";
			$sql.= " ".(! isset($this->roomnum)?'NULL':"'".$this->db->escape($this->roomnum)."'").",";
			$sql.= " ".(! isset($this->floornum)?'NULL':"'".$this->db->escape($this->floornum)."'").",";
			$sql.= " ".(! isset($this->product_ref)?'NULL':"'".$this->db->escape($this->product_ref)."'").",";
			$sql.= " ".(! isset($this->configuration)?'NULL':"'".$this->db->escape($this->configuration)."'").",";
			$sql.= " ".($this->is_screen==true?"1":"0").",";
			$sql.= " ".(! isset($this->frame_color)?'NULL':"'".$this->db->escape($this->frame_color)."'").",";
			$sql.= " ".($this->is_colonial==true?"1":"0").",";
			$sql.= " ".(! isset($this->colonial_fee)?'NULL':"'".$this->db->escape($this->colonial_fee)."'").",";
			$sql.= " ".(! isset($this->colonial_across)?'NULL':"'".$this->db->escape($this->colonial_across)."'").",";
			$sql.= " ".(! isset($this->colonial_down)?'NULL':"'".$this->db->escape($this->colonial_down)."'").",";
			$sql.= " ".(! isset($this->width)?'NULL':"'".$this->db->escape($this->width)."'").",";
			$sql.= " ".(! isset($this->widthtxt)?'NULL':"'".$this->db->escape($this->widthtxt)."'").",";
			$sql.= " ".(! isset($this->height)?'NULL':"'".$this->db->escape($this->height)."'").",";
			$sql.= " ".(! isset($this->heighttxt)?'NULL':"'".$this->db->escape($this->heighttxt)."'").",";
			$sql.= " ".(! isset($this->length)?'NULL':"'".$this->db->escape($this->length)."'").",";
			$sql.= " ".(! isset($this->lengthtxt)?'NULL':"'".$this->db->escape($this->lengthtxt)."'").",";
			$sql.= " ".(! isset($this->glass_type)?'NULL':"'".$this->db->escape($this->glass_type)."'").",";
			$sql.= " ".(! isset($this->glass_color)?'NULL':"'".$this->db->escape($this->glass_color)."'").",";
			$sql.= " ".(! isset($this->interlayer)?'NULL':"'".$this->db->escape($this->interlayer)."'").",";
			$sql.= " ".(! isset($this->coating)?'NULL':"'".$this->db->escape($this->coating)."'").",";
			$sql.= " ".(! isset($this->room_description)?'NULL':"'".$this->db->escape($this->room_description)."'");
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
		$sql = "CALL get_estimate_item_impact(".$id.")";

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
				$this->is_def_color = $obj->is_def_color==1;
				$this->is_def_glass_color = $obj->is_def_glass_color==1;
				$this->is_standard = $obj->is_standard==1;
				$this->roomtype = $obj->roomtype;
				$this->roomnum = $obj->roomnum;
				$this->floornum = $obj->floornum;
				$this->product_ref = $obj->product_ref;
				$this->configuration = $obj->configuration;
				$this->is_screen = $obj->is_screen==1;
				$this->frame_color = $obj->frame_color;
				$this->is_colonial = $obj->is_colonial==1;
				$this->colonial_fee = $obj->colonial_fee;
				$this->colonial_across = $obj->colonial_across;
				$this->colonial_down = $obj->colonial_down;
				$this->width = $obj->width;
				$this->widthtxt = $obj->widthtxt;
				$this->height = $obj->height;
				$this->heighttxt = $obj->heighttxt;
				$this->length = $obj->length;
				$this->lengthtxt = $obj->lengthtxt;
				$this->glass_type = $obj->glass_type;
				$this->glass_color = $obj->glass_color;
				$this->interlayer = $obj->interlayer;
				$this->coating = $obj->coating;
				$this->room_description = $obj->room_description;
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
	 *  Duplicate an impact item
	 *
	 *  @param	int		$id	Id object
	 *  @return int		The id of the impact item to copy
	 */
	function duplicate($id, $newItemId)
	{
		global $langs;
		$newid = 0;
		$sql = "CALL copy_estimate_item_impact(".$id.",".$newItemId.")";

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
		$this->is_def_color=isset($this->is_def_color)?$this->is_def_color:false;
		$this->is_def_glass_color=isset($this->is_def_glass_color)?$this->is_def_glass_color:false;
		$this->is_standard=isset($this->is_standard)?$this->is_standard:false;
		if (isset($this->roomtype)) $this->roomtype=trim($this->roomtype);
		if (isset($this->roomnum)) $this->roomnum=trim($this->roomnum);
		if (isset($this->floornum)) $this->floornum=trim($this->floornum);
		if (isset($this->product_ref)) $this->product_ref=trim($this->product_ref);
		if (isset($this->configuration)) $this->configuration=trim($this->configuration);
		$this->is_screen=isset($this->is_screen)?$this->is_screen:false;
		if (isset($this->frame_color)) $this->frame_color=trim($this->frame_color);
		$this->is_colonial=isset($this->is_colonial)?$this->is_colonial:false;
		if (isset($this->colonial_fee)) $this->colonial_fee=trim($this->colonial_fee);
		if (isset($this->colonial_across)) $this->colonial_across=trim($this->colonial_across);
		if (isset($this->colonial_down)) $this->colonial_down=trim($this->colonial_down);
		if (isset($this->width)) $this->width=trim($this->width);
		if (isset($this->widthtxt)) $this->widthtxt=trim($this->widthtxt);
		if (isset($this->height)) $this->height=trim($this->height);
		if (isset($this->heighttxt)) $this->heighttxt=trim($this->heighttxt);
		if (isset($this->length)) $this->length=trim($this->length);
		if (isset($this->lengthtxt)) $this->lengthtxt=trim($this->lengthtxt);
		if (isset($this->glass_type)) $this->glass_type=trim($this->glass_type);
		if (isset($this->glass_color)) $this->glass_color=trim($this->glass_color);
		if (isset($this->interlayer)) $this->interlayer=trim($this->interlayer);
		if (isset($this->coating)) $this->coating=trim($this->coating);
		if (isset($this->room_description)) $this->room_description=trim($this->room_description);
	
		// Check parameters
		// Put here code to add a control on parameters values

		// Update request
		$sql = "UPDATE ".MAIN_DB_PREFIX."ea_est_impact SET";
		
		// $sql.= " estimateitemid=".(isset($this->estimateitemid)?"'".$this->db->escape($this->estimateitemid)."'":"null").","; // must not update
		$sql.= " provider=".(isset($this->provider)?"'".$this->db->escape($this->provider)."'":"null").",";
		$sql.= " is_def_color=".($this->is_def_color==true?"1":"0").",";
		$sql.= " is_def_glass_color=".($this->is_def_glass_color==true?"1":"0").",";
		$sql.= " is_standard=".($this->is_standard==true?"1":"0").",";
		$sql.= " roomtype=".(isset($this->roomtype)?"'".$this->db->escape($this->roomtype)."'":"null").",";
		$sql.= " roomnum=".(isset($this->roomnum)?"'".$this->db->escape($this->roomnum)."'":"null").",";
		$sql.= " floornum=".(isset($this->floornum)?"'".$this->db->escape($this->floornum)."'":"null").",";
		$sql.= " product_ref=".(isset($this->product_ref)?"'".$this->db->escape($this->product_ref)."'":"null").",";
		$sql.= " configuration=".(isset($this->configuration)?"'".$this->db->escape($this->configuration)."'":"null").",";
		$sql.= " is_screen=".($this->is_screen==true?"1":"0").",";
		$sql.= " frame_color=".(isset($this->frame_color)?"'".$this->db->escape($this->frame_color)."'":"null").",";
		$sql.= " is_colonial=".($this->is_colonial==true?"1":"0").",";
		$sql.= " colonial_fee=".(isset($this->colonial_fee)?"'".$this->db->escape($this->colonial_fee)."'":"null").",";
		$sql.= " colonial_across=".(isset($this->colonial_across)?"'".$this->db->escape($this->colonial_across)."'":"null").",";
		$sql.= " colonial_down=".(isset($this->colonial_down)?"'".$this->db->escape($this->colonial_down)."'":"null").",";
		$sql.= " width=".(isset($this->width)?"'".$this->db->escape($this->width)."'":"null").",";
		$sql.= " widthtxt=".(isset($this->widthtxt)?"'".$this->db->escape($this->widthtxt)."'":"null").",";
		$sql.= " height=".(isset($this->height)?"'".$this->db->escape($this->height)."'":"null").",";
		$sql.= " heighttxt=".(isset($this->heighttxt)?"'".$this->db->escape($this->heighttxt)."'":"null").",";
		$sql.= " length=".(isset($this->length)?"'".$this->db->escape($this->length)."'":"null").",";
		$sql.= " lengthtxt=".(isset($this->lengthtxt)?"'".$this->db->escape($this->lengthtxt)."'":"null").",";
		$sql.= " glass_type=".(isset($this->glass_type)?"'".$this->db->escape($this->glass_type)."'":"null").",";
		$sql.= " glass_color=".(isset($this->glass_color)?"'".$this->db->escape($this->glass_color)."'":"null").",";
		$sql.= " interlayer=".(isset($this->interlayer)?"'".$this->db->escape($this->interlayer)."'":"null").",";
		$sql.= " coating=".(isset($this->coating)?"'".$this->db->escape($this->coating)."'":"null").",";
		$sql.= " room_description=".(isset($this->room_description)?"'".$this->db->escape($this->room_description)."'":"null");
		
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
				$sql = "DELETE FROM ".MAIN_DB_PREFIX."ea_est_impact";
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

		$object=new EaEstimateItemImpact($this->db);

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
		$sql.= " FROM ".MAIN_DB_PREFIX."ea_est_impact as e";

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
			$this->is_def_color=false;
			$this->is_def_glass_color=false;
			$this->is_standard=false;
			$this->roomtype='';
			$this->roomnum='';
			$this->floornum='';
			$this->product_ref='';
			$this->configuration='';
			$this->is_screen=false;
			$this->frame_color='BEIGE';
			$this->is_colonial=false;
			$this->colonial_fee='';
			$this->colonial_across='';
			$this->colonial_down='';
			$this->width='';
			$this->widthtxt='';
			$this->height='';
			$this->heighttxt='';
			$this->length='';
			$this->lengthtxt='';
			$this->glass_type='';
			$this->glass_color='';
			$this->interlayer='';
			$this->coating='';
			$this->room_description='';
		}

		$this->db->free($resql);

	}


	function propsToJSON()
	{
		$res = '{'.PHP_EOL.
			'   "id": ' . $obj->id . ','.PHP_EOL.
			'   "estimateitemid": ' . $obj->estimateitemid . ',' . PHP_EOL .
			'   "provider": "' . cleanTxt($obj->provider) . '",' . PHP_EOL .
			'   "is_def_color": ' . $obj->is_def_color . ',' . PHP_EOL .
			'   "is_def_glass_color": ' . $obj->is_def_glass_color . ',' . PHP_EOL .
			'   "is_standard": ' . $obj->is_standard . ',' . PHP_EOL .
			'   "roomtype": ' . $obj->roomtype . ',' . PHP_EOL .
			'   "roomnum": ' . $obj->roomnum . ',' . PHP_EOL .
			'   "floornum": ' . $obj->floornum . ',' . PHP_EOL .
			'   "product_ref": "' . cleanTxt($obj->product_ref) . '",' . PHP_EOL .
			'   "configuration": "' . cleanTxt($obj->configuration) . '",' . PHP_EOL .
			'   "is_screen": ' . $obj->is_screen . ',' . PHP_EOL .
			'   "frame_color": "' . cleanTxt($obj->frame_color) . '",' . PHP_EOL .
			'   "is_colonial": ' . $obj->is_colonial . ',' . PHP_EOL .
			'   "colonial_fee": ' . $obj->colonial_fee . ',' . PHP_EOL .
			'   "colonial_across": ' . $obj->colonial_across . ',' . PHP_EOL .
			'   "colonial_down": ' . $obj->colonial_down . ',' . PHP_EOL .
			'   "width": ' . $obj->width . ',' . PHP_EOL .
			'   "widthtxt": ' . $obj->widthtxt . ',' . PHP_EOL .
			'   "height": ' . $obj->height . ',' . PHP_EOL .
			'   "heighttxt": ' . $obj->heighttxt . ',' . PHP_EOL .
			'   "length": ' . $obj->length . ',' . PHP_EOL .
			'   "lengthtxt": ' . $obj->lengthtxt . ',' . PHP_EOL .
			'   "glass_type": "' . cleanTxt($obj->glass_type) . '",' . PHP_EOL .
			'   "glass_color": "' . cleanTxt($obj->glass_color) . '",' . PHP_EOL .
			'   "interlayer": "' . cleanTxt($obj->interlayer) . '",' . PHP_EOL .
			'   "coating": "' . cleanTxt($obj->coating) . '",' . PHP_EOL .
			'   "room_description": "' . cleanTxt($obj->room_description) . '"' . PHP_EOL .
			
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
			$e = new EaEstimateItemImpact($this->db);
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
