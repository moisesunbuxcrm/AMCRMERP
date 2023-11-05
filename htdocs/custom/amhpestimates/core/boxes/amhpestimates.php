<?php
/* Copyright (C) 2004-2017 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) ---Put here your own copyright and developer email---
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * \file    modulebuilder/template/core/boxes/amhpwidget1.php
 * \ingroup amhp
 * \brief   Widget provided by AMHP
 *
 * Put detailed description here.
 */

/** Includes */
include_once DOL_DOCUMENT_ROOT . "/core/boxes/modules_boxes.php";

/**
 * Class to manage the box
 *
 * Warning: for the box to be detected correctly by dolibarr,
 * the filename should be the lowercase classname
 */
class amhpestimates extends ModeleBoxes
{
	/**
	 * @var string Alphanumeric ID. Populated by the constructor.
	 */
	public $boxcode = "amhpbox";

	/**
	 * @var string Box icon (in configuration page)
	 * Automatically calls the icon named with the corresponding "object_" prefix
	 */
	public $boximg = "estimates14@amhpestimates";

	/**
	 * @var string Box label (in configuration page)
	 */
	public $boxlabel;

	/**
	 * @var string[] Module dependencies
	 */
	public $depends = array('amhpestimates');

	/**
	 * @var DoliDb Database handler
	 */
	public $db;

	/**
	 * @var mixed More parameters
	 */
	public $param;

	/**
	 * @var array Header informations. Usually created at runtime by loadBox().
	 */
	public $info_box_head = array();

	/**
	 * @var array Contents informations. Usually created at runtime by loadBox().
	 */
	public $info_box_contents = array();

	/**
	 * Constructor
	 *
	 * @param DoliDB $db Database handler
	 * @param string $param More parameters
	 */
	public function __construct(DoliDB $db, $param = '')
	{
		global $user, $conf, $langs;
		$langs->load("boxes");
		$langs->load('amhpestimates@amhpestimates');

		parent::__construct($db, $param);

		$this->boxlabel = $langs->transnoentitiesnoconv("AMHPEstimatesWidgetName");

		$this->param = $param;

		$this->enabled = 1;//$user->rights->amhp->estimates->read;         // Condition when module is enabled or not
		$this->hidden = 0;//! ($user->rights->amhp->estimates->read);   // Condition when module is visible by user (test on permission)
	}

	/**
	 * Load data into info_box_contents array to show array later. Called by Dolibarr before displaying the box.
	 *
	 * @param int $max Maximum number of records to load
	 * @return void
	 */
	public function loadBox($max = 5)
	{
		global $langs;

		// Use configuration value for max lines count
		$this->max = $max;

		//include_once DOL_DOCUMENT_ROOT . "/amhp/class/amhp.class.php";

		// Populate the head at runtime
		// $text = $langs->trans("AMHPEstimatesWidgetDescription", $max);
		// $this->info_box_head = array(
			// // Title text
			// 'text' => $text,
			// // Add a link
			// 'sublink' => 'http://example.com',
			// // Sublink icon placed after the text
			// 'subpicto' => 'object_amhp@amhp',
			// // Sublink icon HTML alt text
			// 'subtext' => '',
			// // Sublink HTML target
			// 'target' => '',
			// // HTML class attached to the picto and link
			// 'subclass' => 'center',
			// // Limit and truncate with "…" the displayed text lenght, 0 = disabled
			// 'limit' => 0,
			// // Adds translated " (Graph)" to a hidden form value's input (?)
			// 'graph' => false
		// );

		$results = $this->getEstimates();

		// Populate the contents at runtime
		$this->info_box_contents = Array();
		foreach($results as $row)
		{
			array_push($this->info_box_contents, Array(
				0 => array( // First Column
					//  HTML properties of the TR element. Only available on the first column.
					'tr'           => 'align="left"',
					// HTML properties of the TD element
					'td'           => '',

					// Main text for content of cell
					'text'         => $row["PONUMBER"],
					// Link on 'text' and 'logo' elements
					'url'          => DOL_URL_ROOT.'/custom/amhpestimates/card.php?poid='.$row["POID"].'&mainmenu=amhpestimates',
				),
				1 => array( // Second Column
					//  HTML properties of the TR element. Only available on the first column.
					'tr'           => 'align="left"',
					// HTML properties of the TD element
					'td'           => '',

					// Main text for content of cell
					'text'         => $row["customername"],
					// Link on 'text' and 'logo' elements
					'url'          => DOL_URL_ROOT.'/societe/card.php?socid='.$row["socid"],
				),
				2 => array( // Third Column
					//  HTML properties of the TR element. Only available on the first column.
					'tr'           => 'align="left"',
					// HTML properties of the TD element
					'td'           => '',

					// Main text for content of cell
					'text'         => dol_print_date($row["QUOTEDATE"],"day"),
				),
			));
		}
	}

	/**
	 * Method to show box. Called by Dolibarr eatch time it wants to display the box.
	 *
	 * @param array $head Array with properties of box title
	 * @param array $contents Array with properties of box lines
	 * @return void
	 */
	public function showBox($head = null, $contents = null, $nooutput = 0)
	{
		// You may make your own code here…
		// … or use the parent's class function using the provided head and contents templates
		parent::showBox($this->info_box_head, $this->info_box_contents, $nooutput);
	}

	public function getEstimates()
	{
		global $db;
		
		$sql = "SELECT po.poid, po.ponumber, tp.rowid as socid, tp.nom as customername, po.QUOTEDATE ";
		$sql.= " FROM ".MAIN_DB_PREFIX."ea_po as po";
		$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."societe as tp on (tp.rowid = po.customerId)";
		$sql.= " WHERE 1=1 ";

		$sql.= $db->order("po.QUOTEDATE","DESC");
		
		$limit=5;
		$sql.= $db->plimit($limit, 0);
		
		$resql = $db->query($sql);
		if (! $resql)
		{
			dol_print_error($db);
			exit;
		}
		
		$num = $db->num_rows($resql);
		$i = 0;
		$results=array();
		while ($i < min($num, $limit))
		{
			$obj = $db->fetch_object($resql);
			$row = Array();
			$row["POID"] = $obj->poid;
			$row["PONUMBER"] = $obj->ponumber;
			$row["socid"] = $obj->socid;
			$row["customername"] = $obj->customername;
			$row["QUOTEDATE"] = $obj->QUOTEDATE;

			array_push($results,$row);
			$i++;
		}

		return $results;
	}
}
