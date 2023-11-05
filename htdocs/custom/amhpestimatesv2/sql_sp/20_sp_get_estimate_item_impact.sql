DROP PROCEDURE IF EXISTS `get_estimate_item_impact`;
--
CREATE PROCEDURE `get_estimate_item_impact`(IN `id` INT)
    READS SQL DATA
    COMMENT 'Gets data about a single estimate impact product line item'
SELECT 
	ip.id,
	ip.estimateitemid,
	ip.provider,
	ip.is_def_color,
	ip.is_def_glass_color,
	ip.is_standard,
	ip.roomtype,
	ip.roomnum,
	ip.floornum,
	ip.product_ref,
	ip.configuration,
	ip.is_screen,
	ip.frame_color,
	ip.is_colonial,
	ip.colonial_fee,
	ip.colonial_across,
	ip.colonial_down,
	ip.width,
	ip.widthtxt,
	ip.height,
	ip.heighttxt,
	ip.length,
	ip.lengthtxt,
	ip.glass_type,
	ip.glass_color,
	ip.interlayer,
	ip.coating,
	ip.room_description,

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
	llx_ea_est_impact as ip 
	left join llx_ea_estimate_item i on ip.estimateitemid = i.id
WHERE 
	ip.id = id
