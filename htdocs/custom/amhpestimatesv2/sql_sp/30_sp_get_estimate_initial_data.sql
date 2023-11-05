DROP PROCEDURE IF EXISTS `get_estimate_initial_data`;
--
CREATE PROCEDURE `get_estimate_initial_data` (IN `initials` VARCHAR(50))
    READS SQL DATA
    COMMENT 'Gets customer and contact data for a new estimate'

    SELECT 
        createPONUMBER(initials) AS estimatenum