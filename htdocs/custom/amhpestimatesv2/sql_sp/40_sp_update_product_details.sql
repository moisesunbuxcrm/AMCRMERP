DROP PROCEDURE IF EXISTS `update_product_details`;
--
delimiter //
CREATE PROCEDURE `update_product_details`(
    IN _rowid INT,
    IN _name VARCHAR(255),
    IN _ref VARCHAR(128),
    IN _description text,
    IN _price double(24,8),
    IN _inst_price double(24,8),
    IN _cost_price double(24,8),
    IN _width double(24,8),
    IN _amwidthtxt VARCHAR(25),
    IN _height double(24,8),
    IN _amheighttxt VARCHAR(25),
    IN _length double(24,8),
    IN _amlengthtxt VARCHAR(25),
    IN _itemtype VARCHAR(10),
    IN _modtype VARCHAR(30),
    IN _configuration VARCHAR(50),
    IN _provider VARCHAR(50),
    IN _color VARCHAR(20),
    IN _wintype VARCHAR(100),
    IN _screen VARCHAR(5),
    IN _frame_color VARCHAR(20),
    IN _glass_type VARCHAR(50),
    IN _glass_color VARCHAR(20),
    IN _interlayer VARCHAR(50),
    IN _coating VARCHAR(50))

BEGIN

    UPDATE `llx_product` SET 
        `ref`=_ref,
        `label`=_name,
        `description`=_description,
        `price`=_price,
        `cost_price`=_cost_price
	WHERE `rowid`=_rowid ;

	UPDATE `llx_product_extrafields` SET
        `aminstallprice`=_inst_price,
        `amwidth`=_width,
        `amwidthtxt`=_amwidthtxt,
        `amheight`=_height,
        `amheighttxt`=_amheighttxt,
        `amlength`=_length,
        `amlengthtxt`=_amlengthtxt,
        `amitemtype`=CASE
            WHEN _itemtype = "WINDOW" THEN 1
            WHEN _itemtype = "DOOR" THEN 2
            WHEN _itemtype = "HARDWARE" THEN 3
            ELSE NULL
        END,
        `ammodtype`=(SELECT id FROM `llx_ea_modtype` WHERE name=_modtype),
        `amproductconfig`=(SELECT id FROM `llx_ea_productconfig` WHERE name=_configuration),
        `amprovider`=(SELECT id FROM `llx_ea_stackingcharts` WHERE name=_provider),
        `amcolor`=(SELECT id FROM `llx_ea_colors` WHERE name=_color),
        `amwintype`=(SELECT id FROM `llx_ea_prodwintype` WHERE name=_wintype),
        `amscreen`=CASE
            WHEN _screen = "NO" THEN 0
            WHEN _screen = "YES" THEN 1
            ELSE NULL
        END,
        `amframecolor`=(SELECT id FROM `llx_ea_colors` WHERE name=_frame_color),
        `amglasstype`=(SELECT id FROM `llx_ea_glasstype` WHERE name=_glass_type),
        `amglasscolor`=(SELECT id FROM `llx_ea_colors` WHERE name=_glass_color),
        `aminterlayer`=(SELECT id FROM `llx_ea_interlayer` WHERE name=_interlayer),
        `amcoating`=(SELECT id FROM `llx_ea_coating` WHERE name=_coating)
	WHERE `fk_object`=_rowid ;

END //

delimiter ;
