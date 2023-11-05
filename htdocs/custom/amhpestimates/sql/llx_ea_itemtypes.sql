--
-- Table structure for table `llx_ea_itemtypes`
--

CREATE TABLE IF NOT EXISTS `llx_ea_itemtypes` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(10) collate utf8_unicode_ci NOT NULL,
  `active` tinyint(4) NOT NULL default '1',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `itemtypes_unique_name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
