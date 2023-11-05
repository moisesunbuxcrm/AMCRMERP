DROP FUNCTION IF EXISTS `createPONUMBER`;
----
CREATE FUNCTION `createPONUMBER`(`initials` VARCHAR(10)) RETURNS varchar(50) CHARSET utf8 
    READS SQL DATA
BEGIN 
	DECLARE d char(8);
    DECLARE ponum VARCHAR(50);
    
	    SET d = DATE_FORMAT(CURDATE(), "%Y");
    
        SELECT 
            CONCAT(
                d,
                CAST(
                    IFNULL(
                        MAX(CAST(mid( CAST(CAST(ponumber as UNSIGNED) AS CHAR), 5, 7) as UNSIGNED))+1
                        ,
                        101
                        ) AS CHAR(10)),
                initials) INTO ponum
        FROM 
            llx_ea_po 
        WHERE 
            PONUMBER LIKE CONCAT(d,'%') COLLATE utf8_general_ci -- Only look at current year
            AND CAST(ponumber as UNSIGNED) < 999999999 -- Ignore YYYYMMDD formated PONUMBERS
        ;
    
    RETURN ponum;
END 
