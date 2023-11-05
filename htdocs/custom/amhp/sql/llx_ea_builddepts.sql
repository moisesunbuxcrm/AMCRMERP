--
-- Table structure for table 'llx_ea_builddepts'
-- Holds building departments for different cities
--

CREATE TABLE IF NOT EXISTS `llx_ea_builddepts` (
  `rowid` int(11) NOT NULL auto_increment,
  `nom` varchar(128) collate utf8_unicode_ci default NULL,
  `name_alias` varchar(128) collate utf8_unicode_ci default NULL,
  `tms` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `datec` datetime default NULL,
  `status` tinyint(4) default '1',
  `code_client` varchar(24) collate utf8_unicode_ci default NULL,
  `address` varchar(255) collate utf8_unicode_ci default NULL,
  `zip` varchar(25) collate utf8_unicode_ci default NULL,
  `town` varchar(50) collate utf8_unicode_ci default NULL,
  `fk_departement` int(11) default '0',
  `fk_pays` int(11) default '0',
  `phone` varchar(20) collate utf8_unicode_ci default NULL,
  `fax` varchar(20) collate utf8_unicode_ci default NULL,
  `url` varchar(255) collate utf8_unicode_ci default NULL,
  `email` varchar(128) collate utf8_unicode_ci default NULL,
  `note_private` text collate utf8_unicode_ci,
  `note_public` text collate utf8_unicode_ci,
  `fk_user_creat` int(11) default NULL,
  `fk_user_modif` int(11) default NULL,
  `default_lang` varchar(6) collate utf8_unicode_ci default NULL,
  `city_code` varchar(3) collate utf8_unicode_ci default NULL,
  `city_name` varchar(100) collate utf8_unicode_ci default NULL,
  `working_hours` varchar(255) collate utf8_unicode_ci default NULL,
  `county` varchar(50) collate utf8_unicode_ci default NULL,
  `prop_search_url` varchar(255) collate utf8_unicode_ci default NULL,
  `customfieldsdir` varchar(512) COLLATE utf8_unicode_ci DEFAULT NULL,
  `custompermittype` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `customimprovementtype` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL
  PRIMARY KEY  (`rowid`),
  UNIQUE KEY `uk_societe_code_client` (`code_client`),
  KEY `idx_societe_user_creat` (`fk_user_creat`),
  KEY `idx_societe_user_modif` (`fk_user_modif`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=76 ;
