DROP PROCEDURE IF EXISTS `copy_estimate`;
--
DELIMITER $$
CREATE PROCEDURE `copy_estimate`(IN `copyid` INT, IN `initials` VARCHAR(10))
    MODIFIES SQL DATA
    COMMENT 'Creates a copy of an Estimate including it''s items'
BEGIN

	DECLARE newid int;
    
	INSERT INTO `llx_ea_estimate`(
		`estimatenum`,
		`quotedate`,
		`customerid`,
		`vendor`,
		`vendor_phone`,
		`folio`,
		`deposit_percent`,
		`deposit_percent_with_install`,
		`percent_final_inspection`,
		`warranty_years`,
		`pay_upon_completion`,
		`new_construction_owner_responsability`,
		`status`,
		`status_reason`,
		`approved_date`,
		`rejected_date`,
		`delivered_date`,
		`defcolor`,
		`defglasscolor`,
		`is_alteration`,
		`is_installation_included`,
		`add_sales_discount`,
		`add_inst_discount`,
		`permits`,
		`salestax`,
		`totalprice`,
		`notes`,
		`public_notes`
	)
		SELECT 
			create_estimate_numberv2(initials), 
			`quotedate`,
			`customerid`,
			`vendor`,
			`vendor_phone`,
			`folio`,
			`deposit_percent`,
			`deposit_percent_with_install`,
			`percent_final_inspection`,
			`warranty_years`,
			`pay_upon_completion`,
			`new_construction_owner_responsability`,
			`status`,
			`status_reason`,
			`approved_date`,
			`rejected_date`,
			`delivered_date`,
			`defcolor`,
			`defglasscolor`,
			`is_alteration`,
			`is_installation_included`,
			`add_sales_discount`,
			`add_inst_discount`,
			`permits`,
			`salestax`,
			`totalprice`,
			`notes`,
			`public_notes`
			
		FROM `llx_ea_estimate` WHERE id = copyid;

	SET newid = LAST_INSERT_ID();

	SELECT newid;
    
END$$
DELIMITER ;
