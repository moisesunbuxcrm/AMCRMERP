DROP PROCEDURE IF EXISTS `get_estimate_item_hardware`;
--
CREATE PROCEDURE `get_estimate_item_hardware`(IN `id` INT)
    READS SQL DATA
    COMMENT 'Gets data about a single estimate hardware line item'
SELECT 
	h.id,
	h.estimateitemid,
	h.provider,
	h.product_ref,
	h.hardwaretype,
	h.configuration,

	i.estimateid,
	i.itemno,
	i.itemtype,
	i.modtype,
	i.wintype,
	i.name,
	i.image,
	i.color,
	i.cost_price,
	i.sales_price,
	i.sales_discount,
	i.inst_price,
	i.inst_discount,
	i.otherfees,
	i.finalprice,
	i.quantity

FROM 
	llx_ea_est_hardware as h 
	left join llx_ea_estimate_item i on h.estimateitemid = i.id
WHERE 
	h.id = id
