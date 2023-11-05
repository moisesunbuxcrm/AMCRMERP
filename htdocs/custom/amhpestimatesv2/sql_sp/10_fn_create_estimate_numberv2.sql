DROP FUNCTION IF EXISTS `create_estimate_numberv2`;
--
DELIMITER $$
CREATE FUNCTION `create_estimate_numberv2`(`initials` VARCHAR(10)) RETURNS varchar(50) CHARSET utf8 
    READS SQL DATA
BEGIN 
	DECLARE d char(8);
    DECLARE newnum VARCHAR(50);
    
    SET d = DATE_FORMAT(CURDATE(), "%Y%m%d");

    SELECT 
        CONCAT(
            d,
            CAST(
                IFNULL(
                    MAX(CAST(mid( CAST(CAST(estimatenum as UNSIGNED) AS CHAR), 9, 11) as UNSIGNED))+1
                    ,
                    101
                    ) AS CHAR(11)),
            initials) INTO newnum
    FROM 
        llx_ea_estimate 
    WHERE 
        estimatenum LIKE CONCAT(d,'%') COLLATE utf8_general_ci
    ;
    
    RETURN newnum;
END$$
DELIMITER ;