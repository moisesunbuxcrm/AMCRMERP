DROP PROCEDURE IF EXISTS `get_estimate_item`;
--
CREATE PROCEDURE `get_estimate_item`(IN `id` INT)
    READS SQL DATA
    COMMENT 'Gets data about a single estimate line item'
SELECT 
	e.id, 
	e.estimateid,
	e.itemno,
	e.itemtype,
	e.modtype,
	e.wintype,
	e.name,
	e.image,
	e.color,
	e.cost_price,
	e.sales_price,
	e.sales_discount,
	e.inst_price,
	e.inst_discount,
	e.otherfees,
	e.finalprice,
	e.quantity
FROM 
	llx_ea_estimate_item as e 
WHERE 
	e.id = id