DROP PROCEDURE IF EXISTS `get_product_details`;
--
CREATE PROCEDURE `get_product_details`(IN `id` int)
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
    IFNULL(x.amitemtype,  0) as itemtype,
    m.name as modtype,
    x.amwintype as wintype,
    pc.name as configuration,
	sc.name as provider,
    c.name as color,
  	wt.name as wintype,
	x.amscreen as screen,
	fc.name as frame_color,
	gt.name as glass_type,
	gc.name as glass_color,
	il.name as interlayer,
	coat.name as coating,
	concat('/AMCRMERP/htdocs/viewimage.php?modulepart=produit&entity=1&file=',p.ref,'/',f.filename) as image
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
	left join llx_ecm_files f on f.filepath = concat('produit/', p.ref)
WHERE
	p.rowid = id
ORDER BY f.position ASC
LIMIT 1
