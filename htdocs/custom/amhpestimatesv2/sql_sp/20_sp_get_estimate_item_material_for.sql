DROP PROCEDURE IF EXISTS `get_estimate_item_material_for`;
--
CREATE PROCEDURE `get_estimate_item_material_for`(IN `id` INT)
    READS SQL DATA
    COMMENT 'Gets data about a single estimate material product line item by the item id'
SELECT 
	ip.id,
	ip.estimateitemid,
	ip.provider,
	ip.product_ref,
	ip.width,
	ip.widthtxt,
	ip.height,
	ip.heighttxt,
	ip.length,
	ip.lengthtxt,

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
	llx_ea_est_material as ip 
	left join llx_ea_estimate_item i on ip.estimateitemid = i.id
WHERE 
	i.id = id
