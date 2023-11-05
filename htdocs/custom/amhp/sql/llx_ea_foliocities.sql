--
-- Table structure for table 'llx_ea_foliocities'
-- Holds counties, folios, cities, and building departments
--

CREATE TABLE IF NOT EXISTS `llx_ea_foliocities` (
  `id` int(11) NOT NULL auto_increment,
  `county` varchar(128) collate utf8_unicode_ci default NULL,
  `folio_prefix` char(2) collate utf8_unicode_ci default NULL,
  `city` varchar(128) collate utf8_unicode_ci default NULL,
  `builddept` varchar(128) collate utf8_unicode_ci default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;
