--
-- Table structure for table 'llx_ea_custcities'
-- Holds cities used in customer dropdown
-- Can be edited in Dolibarr from dictionaries
--

CREATE TABLE IF NOT EXISTS `llx_ea_custcities` (
  `rowid` int(11) NOT NULL auto_increment,
  `label` varchar(100) collate utf8_unicode_ci NOT NULL,
  `active` tinyint(4) NOT NULL default '1',
  PRIMARY KEY  (`rowid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Holds cities for Customer forms' AUTO_INCREMENT=1 ;