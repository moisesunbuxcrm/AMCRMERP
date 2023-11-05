DROP PROCEDURE IF EXISTS `get_product_summaries`;
--
CREATE PROCEDURE `get_product_summaries`()
SELECT 
	p.rowid,
	p.label as name,
	p.ref,
    x.amwidthtxt as widthtxt,
    x.amheighttxt as heighttxt,
    x.amlengthtxt as lengthtxt,
    IFNULL(x.amitemtype,  0) as itemtype,
    m.name as modtype,
	sc.name as provider,
    c.name as color,
  	wt.name as wintype,
	gc.name as glass_color,
	il.name as interlayer
FROM 
	llx_product as p 
    left join llx_product_extrafields x on x.fk_object = p.rowid
    left join llx_ea_stackingcharts sc on sc.id = x.amprovider
    left join llx_ea_modtype m on m.id = x.ammodtype
    left join llx_ea_colors c on c.id = x.amcolor
	left join llx_ea_prodwintype wt on wt.id = x.amwintype
    left join llx_ea_colors gc on gc.id = x.amglasscolor
	left join llx_ea_interlayer il on il.id = x.aminterlayer
ORDER BY
	p.label