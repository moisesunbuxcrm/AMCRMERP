DROP PROCEDURE IF EXISTS `copy_estimate_item_hardware`;
--
DELIMITER $$
CREATE PROCEDURE `copy_estimate_item_hardware`(IN `copyid` INT, IN `itemId` INT)
    MODIFIES SQL DATA
    COMMENT 'Creates a copy of an Estimate hardware product item'
BEGIN

	DECLARE newid int;
    
	INSERT INTO `llx_ea_est_hardware`(
		estimateitemid,
		provider,
		product_ref,
		hardwaretype,
		configuration
	)
		SELECT 
			itemId,
			i1.provider,
			i1.product_ref,
			i1.hardwaretype,
			i1.configuration
			
		FROM `llx_ea_est_hardware` i1 WHERE id = copyid;

	SET newid = LAST_INSERT_ID();

	SELECT newid;
    
END$$
DELIMITER ;
