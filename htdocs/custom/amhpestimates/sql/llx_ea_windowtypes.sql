--
-- Table structure for table `llx_ea_windowtypes`
--

CREATE TABLE IF NOT EXISTS `llx_ea_windowtypes` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(20) default NULL,
  `active` tinyint(4) NOT NULL default '1',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `windowtypes_unique_name` (`name`),
  KEY `WindowsID` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;