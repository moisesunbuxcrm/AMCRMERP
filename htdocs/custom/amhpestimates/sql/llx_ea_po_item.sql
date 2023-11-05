--
-- Table structure for table `llx_ea_po_item`
--

CREATE TABLE IF NOT EXISTS `llx_ea_po_item` (
  `PODescriptionID` int(11) NOT NULL auto_increment,
  `POID` int(11) NOT NULL DEFAULT '0',
  `LineNumber` int(11) DEFAULT '0',
  `OPENINGW` double(12,4) DEFAULT '0.0000',
  `OPENINGHT` double(12,4) DEFAULT '0.0000',
  `TRACK` double(12,4) DEFAULT '0.0000',
  `TYPE` varchar(4) DEFAULT NULL,
  `BLADESQTY` double(12,4) DEFAULT '0.0000',
  `BLADESSTACK` double(12,4) DEFAULT '0.0000',
  `BLADESLONG` double(12,4) DEFAULT '0.0000',
  `LEFT` varchar(50) DEFAULT NULL,
  `RIGHT` varchar(50) DEFAULT NULL,
  `LOCKIN` varchar(7) DEFAULT NULL,
  `LOCKSIZE` varchar(4) DEFAULT NULL,
  `UPPERSIZE` double(12,4) DEFAULT '0.0000',
  `UPPERTYPE` varchar(4) DEFAULT NULL,
  `LOWERSIZE` double(12,4) DEFAULT '0.0000',
  `LOWERTYPE` varchar(4) DEFAULT NULL,
  `ANGULARTYPE` varchar(4) DEFAULT NULL,
  `ANGULARSIZE` double(12,4) DEFAULT '0.0000',
  `ANGULARQTY` int(11) DEFAULT '0',
  `MOUNT` varchar(4) DEFAULT NULL,
  `ALUMINST` double(12,4) DEFAULT '0.0000',
  `LINEARFT` double(12,4) DEFAULT '0.0000',
  `OPENINGHT4` double(12,4) DEFAULT '0.0000',
  `ALUMINST4` double(12,4) DEFAULT '0.0000',
  `EST8HT` int(11) DEFAULT '0',
  `ALUM` double(12,4) DEFAULT '0.0000',
  `WINDOWSTYPE` varchar(20) DEFAULT NULL,
  `EXTRAANGULARTYPE` varchar(4) DEFAULT NULL,
  `EXTRAANGULARSIZE` double(12,4) DEFAULT '0.0000',
  `EXTRAANGULARQTY` int(11) DEFAULT '0',
  `SQFEETPRICE` double(12,4) DEFAULT '0.0000',
  `ProductType` int(11) NOT NULL,
  `COLOR` varchar(25) DEFAULT NULL,
  `MATERIAL` varchar(25) DEFAULT NULL,
  `PROVIDER` int(11) NOT NULL,
  `INSTFEE` double(12,4) DEFAULT NULL,
  `TUBETYPE` varchar(4) DEFAULT NULL,
  `TUBESIZE` double(12,4) DEFAULT '0.0000',
  `TUBEQTY` int(11) DEFAULT '0',
  PRIMARY KEY  (`PODescriptionID`),
  KEY `POID` (`POID`),
  KEY `lockin` (`LOCKIN`),
  KEY `producttype` (`ProductType`),
  KEY `PROVIDER` (`PROVIDER`),
  CONSTRAINT `llx_ea_po_item_ibfk_4` FOREIGN KEY (`PROVIDER`) REFERENCES `llx_ea_stackingcharts` (`id`),
  CONSTRAINT `llx_ea_po_item_ibfk_1` FOREIGN KEY (`POID`) REFERENCES `llx_ea_po` (`POID`),
  CONSTRAINT `llx_ea_po_item_ibfk_2` FOREIGN KEY (`LOCKIN`) REFERENCES `llx_ea_lockins` (`name`),
  CONSTRAINT `llx_ea_po_item_ibfk_3` FOREIGN KEY (`ProductType`) REFERENCES `llx_ea_producttypes` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

ALTER TABLE `llx_ea_po_item`
  ADD CONSTRAINT `llx_ea_po_item_ibfk_1` FOREIGN KEY (`POID`) REFERENCES `llx_ea_po` (`POID`),
  ADD CONSTRAINT `llx_ea_po_item_ibfk_2` FOREIGN KEY (`LOCKIN`) REFERENCES `llx_ea_lockins` (`name`),
  ADD CONSTRAINT `llx_ea_po_item_ibfk_3` FOREIGN KEY (`ProductType`) REFERENCES `llx_ea_producttypes` (`id`),
  ADD CONSTRAINT `llx_ea_po_item_ibfk_4` FOREIGN KEY (`PROVIDER`) REFERENCES `llx_ea_stackingcharts` (`id`);
