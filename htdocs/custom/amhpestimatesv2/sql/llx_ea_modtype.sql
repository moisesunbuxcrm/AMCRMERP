--
-- Table structure for table `llx_ea_modtype`
--

CREATE TABLE IF NOT EXISTS `llx_ea_modtype` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(30) collate utf8_unicode_ci NOT NULL,
  `active` tinyint(4) NOT NULL default '1',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `modtype_unique_name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
