DROP PROCEDURE IF EXISTS `getPOInitialData`;
----
CREATE PROCEDURE `getPOInitialData`(IN `cid` INT, IN `initials` VARCHAR(50))
    READS SQL DATA
    COMMENT 'Gets customer and contact data for a new PO'
IF (cid is null or not exists(SELECT * FROM llx_societe WHERE rowid = cid))
THEN
    SELECT 
        createPONUMBER(initials) AS PONUMBER,
        NULL as CUSTOMERNAME, 
        NULL as CONTACTNAME,
        NULL as CONTACTPHONE1,
        NULL as CONTACTPHONE2,
        NULL as CUSTOMERADDRESS,
        NULL as ZIPCODE,
        NULL as CITY, 
        NULL as STATE, 
        NULL as PHONENUMBER1, 
        NULL as PHONENUMBER2, 
        NULL as FAXNUMBER, 
        NULL as EMail;
ELSE
    SELECT 
        createPONUMBER(initials) AS PONUMBER,
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
        LEFT JOIN llx_socpeople as contact on tp.rowid = contact.fk_soc 
        LEFT JOIN llx_societe_extrafields as tp2 on tp.rowid = tp2.fk_object 
    	LEFT JOIN llx_c_departements as d ON tp.fk_departement = d.rowid 
    WHERE 
        tp.rowid = cid
    LIMIT 1;
END IF