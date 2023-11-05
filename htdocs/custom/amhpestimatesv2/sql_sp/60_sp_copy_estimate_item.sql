DROP PROCEDURE IF EXISTS `copy_estimate_item`;
--
DELIMITER $$
CREATE PROCEDURE `copy_estimate_item`(IN `copyid` INT)
    MODIFIES SQL DATA
    COMMENT 'Creates a copy of an Estimate item'
BEGIN

	DECLARE newid int;
    
	INSERT INTO `llx_ea_estimate_item`(
		estimateid,
		itemno,
		itemtype,
		modtype,
		wintype,
		name,
		image,
		color,
		cost_price,
		sales_price,
		sales_discount,
		inst_price,
		inst_discount,
		otherfees,
		finalprice,
		quantity
	)
		SELECT 
			i1.estimateid,
			(select max(i2.itemno)+1 from llx_ea_estimate_item i2 where i2.estimateid=i1.estimateid),
			i1.itemtype,
			i1.modtype,
			i1.wintype,
			i1.name,
			i1.image,
			i1.color,
			i1.cost_price,
			i1.sales_price,
			i1.sales_discount,
			i1.inst_price,
			i1.inst_discount,
			i1.otherfees,
			i1.finalprice,
			i1.quantity
			
		FROM `llx_ea_estimate_item` i1 WHERE id = copyid;

	SET newid = LAST_INSERT_ID();

	SELECT newid;
    
END$$
DELIMITER ;
