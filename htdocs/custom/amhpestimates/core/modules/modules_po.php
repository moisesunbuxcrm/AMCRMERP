<?php
require_once DOL_DOCUMENT_ROOT.'/core/class/commondocgenerator.class.php';


/**
 *	Base class for generating PDF documents for Production Orders
 */
abstract class PODocModel extends CommonDocGenerator
{
	var $error='';


	/**
	 *  Return list of active generation modules
	 *
     *  @param	DoliDB	$db     			Database handler
     *  @param  integer	$maxfilenamelength  Max length of value to show
     *  @return	array						List of templates
	 */
	static function liste_modeles($db,$maxfilenamelength=0)
	{
		global $conf;

		$type='amhppo';
		$liste=array();

		include_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';
		$liste=getListOfModels($db,$type,$maxfilenamelength);

		return $liste;
	}
}


/**
 *	Base class for enumeration of PO's
 */
abstract class ModeleNumRefPropales
{
	var $error='';

	/**
	 * Return if a module can be used or not
	 *
	 * @return	boolean     true if module can be used
	 */
	function isEnabled()
	{
		return true;
	}

	/**
	 *  Renvoi la description par defaut du modele de numerotation
	 *
	 * 	@return     string      Texte descripif
	 */
	function info()
	{
		global $langs;
		$langs->load("amhpestimates@amhpestimates");
		return $langs->trans("NoDescription");
	}

	/**
	 * 	Renvoi un exemple de numerotation
	 *
	 *  @return     string      Example
	 */
	function getExample()
	{
		global $langs;
		$langs->load("amhpestimates@amhpestimates");
		return $langs->trans("NoExample");
	}

	/**
	 *  Test si les numeros deja en vigueur dans la base ne provoquent pas de
	 *  de conflits qui empechera cette numerotation de fonctionner.
	 *
	 *  @return     boolean     false si conflit, true si ok
	 */
	function canBeActivated()
	{
		return true;
	}

	/**
	 * 	Renvoi prochaine valeur attribuee
	 *
	 *	@param		Societe		$objsoc     Object third party
	 *	@param		Propal		$propal		Object commercial proposal
	 *	@return     string      Valeur
	 */
	function getNextValue($objsoc,$propal)
	{
		global $langs;
		return $langs->trans("NotAvailable");
	}

	/**
	 *  Renvoi version du module numerotation
	 *
	 *  @return     string      Valeur
	 */
	function getVersion()
	{
		return $langs->trans("NotAvailable");
	}
}
