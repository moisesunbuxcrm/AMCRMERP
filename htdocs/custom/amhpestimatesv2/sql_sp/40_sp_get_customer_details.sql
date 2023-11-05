DROP PROCEDURE IF EXISTS `get_customer_details`;
--
CREATE PROCEDURE `get_customer_details`(IN `id` int)
SELECT 
	tp.rowid,
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
	contact.address as CONTACTADDRESS,
	tp.address as CUSTOMERADDRESS,
	tp.zip as ZIPCODE,
	tp.town as CITY, 
	tp2.amreltype as reltype,
	d.nom as STATE, 
	tp.phone as PHONENUMBER1, 
	tp2.mobilephone as PHONENUMBER2, 
	tp.fax as FAXNUMBER, 
	tp.email as EMail,
	tp.barcode as folionumber
FROM 
	llx_societe as tp
    LEFT JOIN llx_socpeople contact on tp.rowid = contact.fk_soc
	LEFT JOIN llx_societe_extrafields as tp2 on tp.rowid = tp2.fk_object
	LEFT JOIN llx_c_departements as d ON tp.fk_departement = d.rowid 
WHERE 
	tp.rowid = id
