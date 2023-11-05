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
class amhpestimatesv2 extends ModeleBoxes
{
	/**
	 * @var string Alphanumeric ID. Populated by the constructor.
	 */
	public $boxcode = "amhpbox";

	/**
	 * @var string Box icon (in configuration page)
	 * Automatically calls the icon named with the corresponding "object_" prefix
	 */
	public $boximg = "estimates14@amhpestimatesv2";

	/**
	 * @var string Box label (in configuration page)
	 */
	public $boxlabel;

	/**
	 * @var string[] Module dependencies
	 */
	public $depends = array('amhpestimatesv2');

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
		$langs->load('amhpestimatesv2@amhpestimatesv2');

		parent::__construct($db, $param);

		$this->boxlabel = $langs->transnoentitiesnoconv("AMHPEstimatesV2WidgetName");

		$this->param = $param;

		$this->enabled = 1;//$user->rights->estimatesv2->estimates->read;         // Condition when module is enabled or not
		$this->hidden = 0;//! ($user->rights->estimatesv2->estimates->read);   // Condition when module is visible by user (test on permission)
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
		// $text = $langs->trans("AMHPEstimatesv2WidgetDescription", $max);
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
					'text'         => $row["estimatenum"],
					// Link on 'text' and 'logo' elements
					'url'          => DOL_URL_ROOT.'/custom/amhpestimatesv2/card.php?id='.$row["rowid"].'&mainmenu=amhpestimatesv2',
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
					'text'         => dol_print_date($row["quotedate"],"day"),
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
	public function showBox($head = null, $contents = null)
	{
		// You may make your own code here…
		// … or use the parent's class function using the provided head and contents templates
		parent::showBox($this->info_box_head, $this->info_box_contents);
	}

	public function getEstimates()
	{
		global $db;
		
		$sql = "SELECT e.rowid, e.estimatenum, tp.rowid as socid, tp.nom as customername, e.quotedate ";
		$sql.= " FROM ".MAIN_DB_PREFIX."ea_estimate as e";
		$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."societe as tp on (tp.rowid = e.customerid)";
		$sql.= " WHERE 1=1 ";

		$sql.= $db->order("pe.quotedate","DESC");
		
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
			$row["rowid"] = $obj->rowid;
			$row["estimatenum"] = $obj->estimatenum;
			$row["socid"] = $obj->socid;
			$row["customername"] = $obj->customername;
			$row["quotedate"] = $obj->QUOTEDATE;

			array_push($results,$row);
			$i++;
		}

		return $results;
	}
}
