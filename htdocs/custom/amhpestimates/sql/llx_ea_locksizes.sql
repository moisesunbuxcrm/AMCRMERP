--
-- Table structure for table `llx_ea_locksizes`
--

CREATE TABLE IF NOT EXISTS `llx_ea_locksizes` (
  `id` int(11) NOT NULL auto_increment,
  `size` int(11) NOT NULL,
  `active` tinyint(4) NOT NULL default '1',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `size` (`size`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;