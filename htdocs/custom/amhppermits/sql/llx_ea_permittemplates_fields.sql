--
-- Table structure for table `llx_ea_permittemplates_fields`
--

CREATE TABLE `llx_ea_permittemplates_fields` (
  `rowid` int(11) AUTO_INCREMENT PRIMARY KEY NOT NULL,
  `templateid` int(11) NOT NULL,
  `pageno` int(11) NOT NULL DEFAULT '1',
  `fieldname` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `x` float NOT NULL COMMENT 'inches',
  `y` float NOT NULL COMMENT 'inches',
  `w` float NOT NULL,
  `h` float NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
