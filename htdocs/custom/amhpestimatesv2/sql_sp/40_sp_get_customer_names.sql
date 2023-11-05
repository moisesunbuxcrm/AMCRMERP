DROP PROCEDURE IF EXISTS `get_customer_names`;
--
CREATE PROCEDURE `get_customer_names`()
SELECT 
	tp.rowid,
	tp.nom as CUSTOMERNAME
FROM 
	llx_societe as tp
ORDER BY
	tp.nom