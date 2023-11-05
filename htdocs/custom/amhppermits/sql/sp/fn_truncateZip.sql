DROP FUNCTION IF EXISTS `truncateZip`;
--
CREATE FUNCTION `truncateZip`(`zip` VARCHAR(20)) RETURNS varchar(5) CHARSET utf8 COLLATE utf8_unicode_ci
    DETERMINISTIC
BEGIN
	IF (zip IS NOT NULL and LENGTH(zip) > 5) THEN
		SET zip = LEFT(zip, 5);
	END IF;

	RETURN zip;
END