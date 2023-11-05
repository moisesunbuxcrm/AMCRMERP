DROP PROCEDURE IF EXISTS `copy_estimate_item_design`;
--
DELIMITER $$
CREATE PROCEDURE `copy_estimate_item_design`(IN `copyid` INT, IN `itemId` INT)
    MODIFIES SQL DATA
    COMMENT 'Creates a copy of an Estimate design product item'
BEGIN

	DECLARE newid int;
    
	INSERT INTO `llx_ea_est_design`(
		estimateitemid,
		provider,
		product_ref,
		width,
		widthtxt,
		height,
		heighttxt
	)
		SELECT 
			itemId,
			i1.provider,
			i1.product_ref,
			i1.configuration,
			i1.width,
			i1.widthtxt,
			i1.height,
			i1.heighttxt
			
		FROM `llx_ea_est_design` i1 WHERE id = copyid;

	SET newid = LAST_INSERT_ID();

	SELECT newid;
    
END$$
DELIMITER ;
