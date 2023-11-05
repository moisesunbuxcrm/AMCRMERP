DROP PROCEDURE IF EXISTS `getThirdPartiesForEstimate`;
----
CREATE PROCEDURE `getThirdPartiesForEstimate`()
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
	tp.address as CUSTOMERADDRESS,
	tp.zip as ZIPCODE,
	tp.town as CITY, 
	d.nom as STATE, 
	tp.phone as PHONENUMBER1, 
	tp2.mobilephone as PHONENUMBER2, 
	tp.fax as FAXNUMBER, 
	tp.email as EMail
FROM 
	llx_societe as tp
    LEFT JOIN llx_socpeople contact on tp.rowid = contact.fk_soc
	LEFT JOIN llx_societe_extrafields as tp2 on tp.rowid = tp2.fk_object
	LEFT JOIN llx_c_departements as d ON tp.fk_departement = d.rowid 
WHERE 
	contact.rowid = (SELECT rowid from llx_socpeople WHERE fk_soc = tp.rowid LIMIT 1)
    OR
    contact.rowid is null
ORDER BY
	tp.nom