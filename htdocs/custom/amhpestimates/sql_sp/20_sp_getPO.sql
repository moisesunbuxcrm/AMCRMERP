DROP PROCEDURE IF EXISTS `getPO`;
----
CREATE PROCEDURE `getPO`(IN `poid` INT)
    READS SQL DATA
    COMMENT 'Gets data about a single Production Order or Estimate'
SELECT 
	po.POID, 
	po.PONUMBER, 
	po.PODATE, 
	po.QUOTEDATE, 
	po.Salesman, 
	tp.nom as CUSTOMERNAME, 
	IF(
		LENGTH(TRIM(IFNULL(contact.firstname, "")))=0 AND 
		LENGTH(TRIM(IFNULL(contact.lastname, "")))=0
		,null
		,TRIM(
			CONCAT(
				TRIM(IFNULL(contact.firstname,""))
				," "
				,TRIM(IFNULL(contact.lastname,""))
			)
		)
	) as CONTACTNAME,
	contact.phone as CONTACTPHONE1,
	IFNULL(contact.phone_perso, contact.phone_mobile) as CONTACTPHONE2,
	tp.address as CUSTOMERADDRESS,
	tp.zip as ZIPCODE,
	tp.town as CITY, 
	d.nom as STATE, 
	tp.phone as PHONENUMBER1, 
	tp2.mobilephone as PHONENUMBER2, 
	tp.fax as FAXNUMBER, 
	tp.email as EMail, 
	po.COLOR, 
	po.HTVALUE, 
	po.DESCRIPTIONOFWORK, 
	po.OBSERVATION, 
	po.TOTALTRACK, 
	po.TAPCONS, 
	po.TOTALLONG, 
	po.FASTENERS, 
	po.TOTALALUMINST, 
	po.TOTALLINEARFT, 
	po.OBSINST, 
	po.SQINSTPRICE, 
	po.INSTSALESPRICE, 
	po.ESTHTVALUE, 
	po.ESTOBSERVATION, 
	po.INSTTIME, 
	po.PERMIT, 
	po.CUSTVALUE, 
	po.CUSTOMIZE, 
	po.SALES_TAX, 
	po.SALESTAXAMOUNT, 
	po.TOTALALUM, 
	po.SALESPRICE,  
	po.SQFEETPRICE, 
	po.OTHERFEES, 
	po.Check50, 
	po.CheckAssIns, 
	po.OrderCompleted, 
	po.Check10YearsWarranty, 
	po.Check10YearsFreeMaintenance, 
	po.CheckFreeOpeningClosing, 
	po.CheckNoPayment, 
	po.YearsWarranty, 
	po.LifeTimeWarranty, 
	po.SignatureReq, 
	po.Discount, 
	po.customerId, 
	po.invoiceId, 
	f.fk_statut is not null and f.fk_statut != 0 as invoiceLocked,
	po.permitId
FROM 
	llx_ea_po as po 
	LEFT JOIN llx_societe as tp on tp.rowid = po.customerId 
	LEFT JOIN llx_socpeople as contact on tp.rowid = contact.fk_soc 
	LEFT JOIN llx_societe_extrafields as tp2 on tp.rowid = tp2.fk_object
	LEFT JOIN llx_c_departements as d ON tp.fk_departement = d.rowid 
	LEFT JOIN llx_facture as f ON po.invoiceId = f.rowid 
WHERE 
	po.POID = poid