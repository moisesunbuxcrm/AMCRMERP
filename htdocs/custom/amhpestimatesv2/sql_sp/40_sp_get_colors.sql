DROP PROCEDURE IF EXISTS `get_colors`;
--
CREATE PROCEDURE `get_colors`()
SELECT 
	name
FROM 
	llx_ea_colors