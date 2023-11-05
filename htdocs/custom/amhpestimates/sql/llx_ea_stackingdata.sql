--
-- Table structure for table `llx_ea_stackingdata`
--

CREATE TABLE IF NOT EXISTS `llx_ea_stackingdata` (
  `STACKINGID` int(11) NOT NULL auto_increment,
  `chartid` int(11) default NULL,
  `BLADES` int(11) default '0',
  `MO` float default '0',
  `STACK` float default '0',
  `TRACK` float default '0',
  `active` tinyint(4) NOT NULL default '1',
  PRIMARY KEY  (`STACKINGID`),
  UNIQUE KEY `stackingdata_unique_mo` (`chartid`,`MO`),
  KEY `ChartId` (`chartid`),
  CONSTRAINT `chartid_fk` FOREIGN KEY (`chartid`) REFERENCES `llx_ea_stackingcharts` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
