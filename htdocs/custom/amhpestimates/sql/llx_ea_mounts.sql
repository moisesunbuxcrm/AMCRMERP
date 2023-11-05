--
-- Table structure for table `llx_ea_mounts`
--

CREATE TABLE IF NOT EXISTS `llx_ea_mounts` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(10) collate utf8_unicode_ci NOT NULL,
  `active` tinyint(4) NOT NULL default '1',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `mounts_unique_name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;