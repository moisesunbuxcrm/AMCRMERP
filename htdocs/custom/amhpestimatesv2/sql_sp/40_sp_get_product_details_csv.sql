DROP PROCEDURE IF EXISTS `get_product_details_csv`;
--
CREATE PROCEDURE `get_product_details_csv`()
SELECT
	p.rowid,
	p.label as name,
	p.ref,
	p.description,
	p.price,
	x.aminstallprice as inst_price,
	p.cost_price,
    x.amwidth as width,
    x.amwidthtxt as widthtxt,
    x.amheight as height,
    x.amheighttxt as heighttxt,
    x.amlength as length,
    x.amlengthtxt as lengthtxt,
	CASE
		WHEN x.amitemtype = 1 THEN "WINDOW"
		WHEN x.amitemtype = 2 THEN "DOOR"
		WHEN x.amitemtype = 3 THEN "HARDWARE"
		ELSE NULL
	END as itemtype,
    m.name as modtype,
    pc.name as configuration,
	sc.name as provider,
    c.name as color,
    wt.name as wintype,
	CASE
		WHEN x.amscreen = 0 THEN "NO"
		WHEN x.amscreen = 1 THEN "YES"
		ELSE NULL
	END as screen,
	fc.name as frame_color,
	gt.name as glass_type,
	gc.name as glass_color,
	il.name as interlayer,
	coat.name as coating
FROM 
	llx_product as p 
    left join llx_product_extrafields x on x.fk_object = p.rowid
    left join llx_ea_stackingcharts sc on sc.id = x.amprovider
    left join llx_ea_modtype m on m.id = x.ammodtype
    left join llx_ea_colors c on c.id = x.amcolor
	left join llx_ea_prodwintype wt on wt.id = x.amwintype
    left join llx_ea_colors fc on fc.id = x.amframecolor
	left join llx_ea_glasstype gt on gt.id = x.amglasstype
    left join llx_ea_colors gc on gc.id = x.amglasscolor
	left join llx_ea_interlayer il on il.id = x.aminterlayer
	left join llx_ea_coating coat on coat.id = x.amcoating
    left join llx_ea_productconfig pc on pc.id = x.amproductconfig
ORDER BY p.rowid ASC
