DROP FUNCTION IF EXISTS `getConst`;
--
CREATE FUNCTION `getConst`(`name` VARCHAR(100)) RETURNS varchar(255) CHARSET utf8 COLLATE utf8_unicode_ci
    READS SQL DATA
BEGIN
	DECLARE val varchar(255);
	select c.value into val from llx_const c where c.name = name;
    return val;
END
