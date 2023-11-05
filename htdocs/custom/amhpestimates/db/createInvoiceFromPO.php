<?php
require '../../../main.inc.php';
require_once DOL_DOCUMENT_ROOT . '/compta/facture/class/facture.class.php';
require_once DOL_DOCUMENT_ROOT.'/custom/amhpestimates/class/eaproductionorders.class.php';

/*
1. Get PO from db

2. Check for existing invoice
	delete invoice and lines if draft
	error if not draft

3. Create Invoice object
	type = standard
	status = draft
	Add line

4. Add invoice ref to estimate

5. Redirect to invoice card to display new invoice
*/

$error = 0;
$poid = GETPOST('id', 'int');
$po = new EaProductionOrders($db);
$po->fetch($poid);

if ($po->invoiceId > 0)
{
	$oldfacture = new Facture($db);
	$result=$oldfacture->fetch($po->invoiceId);
	if ($result >= 0) {
		if ($oldfacture->statut != Facture::STATUS_DRAFT)
		{
			echo "<h2>Cannot delete validated invoice ".$po->invoiceId."</h2><br/>";
			dol_print_error($db);
			exit();
		}
		$result = $oldfacture->delete($user);
		if ($result < 0)
		{
			echo "<h2>Cannot delete invoice ".$po->invoiceId."</h2><br/>";
			dol_print_error($db);
			exit();
		}
	}
}

$newfacture = new Facture($db);
$newfacture->socid				= $po->customerId;
$newfacture->type					= Facture::TYPE_STANDARD;
$newfacture->number				= 'provisoire';
$newfacture->date					= dol_now();
$newfacture->note_private	= 'This invoice was created from the estimate '.$po->PONUMBER;
$newfacture->modelpdf			= 'crabe';
$newfacture->cond_reglement_id	= 1;
$newfacture->mode_reglement_id	= 0;
$newfacture->fk_account	 = -1;
$result = $newfacture->create($user);
if ($result < 0) {
	echo "<h2>Cannot create new invoice</h2><br/>";
	dol_print_error($db);
	exit();
}

$result = $newfacture->addline(
	'Materials & Installation & Fees',	// Description of line
	$po->SALESPRICE,	// Unit price without tax (> 0 even for credit note)
	1,						// quantity
	0,						// Force Vat rate, -1 for auto (Can contain the vat_src_code too with syntax '9.9 (CODE)')
	0,						// Local tax 1 rate (deprecated, use instead txtva with code inside)
	0, 						// Local tax 2 rate (deprecated, use instead txtva with code inside)
	0,						// Id of predefined product/service
	0, 						// Percent of discount on line
	0, 						// Date start of service
	0, 						// Date end of service
	0,						// Code of dispatching into accountancy 
	0,						// info_bits
	0,						// Id discount used
	'HT',					// $price_base_type 'HT' or 'TTC'
	$po->SALESPRICE,	// Unit price with tax (> 0 even for credit note)
	1						// Type of line (0=product, 1=service). Not used if fk_product is defined, the type of product is used.
);
if ($result < 0) {
	echo "<h2>Cannot add new line to invoice".$newfacture->id."</h2><br/>";
	dol_print_error($db);
	exit();
}

$po->invoiceId = $newfacture->id;
$result = $po->update();
if ($result < 0) {
	echo "<h2>Cannot update estimate ".$po->PONUMBER." to point to invoice ".$newfacture->id."</h2><br/>";
	dol_print_error($db);
	exit();
}

header('Location: ' . DOL_URL_ROOT . '/compta/facture/card.php?id='.$newfacture->id);
