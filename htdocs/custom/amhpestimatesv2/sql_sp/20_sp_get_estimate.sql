DROP PROCEDURE IF EXISTS `get_estimate`;
--
CREATE PROCEDURE `get_estimate`(IN `id` INT)
    READS SQL DATA
    COMMENT 'Gets data about a single estimate'
SELECT 
	e.id, 
	e.estimatenum, 
	e.quotedate, 
	e.vendor, 
	e.vendor_phone, 
	tp.nom as customername, 
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
	) as contactname,
	contact.phone as contactphone,
	IFNULL(contact.phone_perso, contact.phone_mobile) as contactmobile,
	tp.address as customeraddress,
	tp.zip as customerzip,
	tp.town as customercity, 
	tp2.amreltype as reltype,
	d.nom as customerstate, 
	tp.phone as customerphone, 
	tp2.mobilephone as customermobile, 
	tp.email as customeremail, 
	e.defcolor, 
	e.defglasscolor, 
	e.is_alteration, 
	e.is_installation_included, 
	e.customerid,
	e.folio,
	e.deposit_percent,
	e.deposit_percent_with_install,
	e.percent_final_inspection,
	e.warranty_years,
	e.pay_upon_completion,
	e.new_construction_owner_responsability,
	e.status,
	e.status_reason,
	e.approved_date,
	e.rejected_date,
	e.delivered_date,
	e.permitId,
	e.add_sales_discount,
	e.add_inst_discount,
	e.permits,
	e.salestax,
	e.totalprice,
	e.notes,
	e.public_notes,
	getConst('MAIN_INFO_SOCIETE_MANAGERS') as qualifiername
FROM 
	llx_ea_estimate as e 
	LEFT JOIN llx_societe as tp on tp.rowid = e.customerid 
	LEFT JOIN llx_socpeople as contact on tp.rowid = contact.fk_soc 
	LEFT JOIN llx_societe_extrafields as tp2 on tp.rowid = tp2.fk_object
	LEFT JOIN llx_c_departements as d ON tp.fk_departement = d.rowid 
WHERE 
	e.id = id