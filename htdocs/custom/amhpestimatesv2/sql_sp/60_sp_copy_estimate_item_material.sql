DROP PROCEDURE IF EXISTS `copy_estimate_item_material`;
--
DELIMITER $$
CREATE PROCEDURE `copy_estimate_item_material`(IN `copyid` INT, IN `itemId` INT)
    MODIFIES SQL DATA
    COMMENT 'Creates a copy of an Estimate material product item'
BEGIN

	DECLARE newid int;
    
	INSERT INTO `llx_ea_est_material`(
		estimateitemid,
		provider,
		product_ref,
		width,
		widthtxt,
		height,
		heighttxt,
		length,
		lengthtxt
	)
		SELECT 
			itemId,
			i1.provider,
			i1.product_ref,
			i1.configuration,
			i1.width,
			i1.widthtxt,
			i1.height,
			i1.heighttxt,
			i1.length,
			i1.lengthtxt
			
		FROM `llx_ea_est_material` i1 WHERE id = copyid;

	SET newid = LAST_INSERT_ID();

	SELECT newid;
    
END$$
DELIMITER ;
