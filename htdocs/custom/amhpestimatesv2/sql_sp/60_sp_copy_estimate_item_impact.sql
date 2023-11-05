DROP PROCEDURE IF EXISTS `copy_estimate_item_impact`;
--
DELIMITER $$
CREATE PROCEDURE `copy_estimate_item_impact`(IN `copyid` INT, IN `itemId` INT)
    MODIFIES SQL DATA
    COMMENT 'Creates a copy of an Estimate impact product item'
BEGIN

	DECLARE newid int;
    
	INSERT INTO `llx_ea_est_impact`(
		estimateitemid,
		provider,
		is_def_color,
		is_def_glass_color,
		is_standard,
		roomtype,
		roomnum,
		floornum,
		product_ref,
		configuration,
		is_screen,
		frame_color,
		is_colonial,
		colonial_fee,
		colonial_across,
		colonial_down,
		width,
		widthtxt,
		height,
		heighttxt,
		length,
		lengthtxt,
		glass_type,
		glass_color,
		interlayer,
		coating,
		room_description
	)
		SELECT 
			itemId,
			i1.provider,
			i1.is_def_color,
			i1.is_def_glass_color,
			i1.is_standard,
			i1.roomtype,
			i1.roomnum,
			i1.floornum,
			i1.product_ref,
			i1.configuration,
			i1.is_screen,
			i1.frame_color,
			i1.is_colonial,
			i1.colonial_fee,
			i1.colonial_across,
			i1.colonial_down,
			i1.width,
			i1.widthtxt,
			i1.height,
			i1.heighttxt,
			i1.length,
			i1.lengthtxt,
			i1.glass_type,
			i1.glass_color,
			i1.interlayer,
			i1.coating,
			i1.room_description
			
		FROM `llx_ea_est_impact` i1 WHERE id = copyid;

	SET newid = LAST_INSERT_ID();

	SELECT newid;
    
END$$
DELIMITER ;
