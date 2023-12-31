--
-- Table structure for table `llx_ea_po`
--

CREATE TABLE IF NOT EXISTS `llx_ea_po` (
  `POID` int(11) NOT NULL auto_increment,
  `PONUMBER` varchar(50) NOT NULL,
  `PODATE` datetime DEFAULT NULL,
  `QUOTEDATE` datetime DEFAULT NULL,
  `Salesman` varchar(50) DEFAULT NULL,
  `CUSTOMERNAME` varchar(50) DEFAULT NULL,
  `CONTACTNAME` varchar(50) DEFAULT NULL,
  `CUSTOMERADDRESS` varchar(75) DEFAULT NULL,
  `ZIPCODE` varchar(10) DEFAULT NULL,
  `CITY` varchar(25) DEFAULT NULL,
  `STATE` varchar(2) DEFAULT NULL,
  `PHONENUMBER1` varchar(14) DEFAULT NULL,
  `PHONENUMBER2` varchar(14) DEFAULT NULL,
  `FAXNUMBER` varchar(14) DEFAULT NULL,
  `EMail` varchar(125) DEFAULT NULL,
  `COLOR` varchar(25) DEFAULT NULL,
  `HTVALUE` int(11) DEFAULT '3',
  `DESCRIPTIONOFWORK` longtext,
  `OBSERVATION` longtext,
  `TOTALTRACK` double(12,4) DEFAULT '0.0000',
  `TAPCONS` double(12,4) DEFAULT '0.0000',
  `TOTALLONG` double(12,4) DEFAULT '0.0000',
  `FASTENERS` double(12,4) DEFAULT '0.0000',
  `TOTALALUMINST` double(12,4) DEFAULT '0.0000',
  `TOTALLINEARFT` double(12,4) DEFAULT '0.0000',
  `OBSINST` longtext,
  `SQINSTPRICE` double(12,4) DEFAULT '0.0000',
  `INSTSALESPRICE` double(12,4) DEFAULT '0.0000',
  `ESTHTVALUE` int(11) DEFAULT '6',
  `ESTOBSERVATION` longtext,
  `INSTTIME` int(11) DEFAULT '0',
  `PERMIT` double(12,4) DEFAULT '0.0000',
  `custvalue` double(12,4) DEFAULT NULL,
  `CUSTOMIZE` double(12,4) DEFAULT '0.0000',
  `SALES_TAX` double(12,4) DEFAULT '0.0000',
  `SALESTAXAMOUNT` double(12,4) DEFAULT '0.0000',
  `TOTALALUM` double(12,4) DEFAULT '0.0000',
  `SALESPRICE` double(12,4) DEFAULT '0.0000',
  `SQFEETPRICE` double(12,4) DEFAULT '0.0000',
  `OTHERFEES` double(12,4) DEFAULT '0.0000',
  `Check50` tinyint(1) DEFAULT '1',
  `CheckAssIns` tinyint(1) DEFAULT '1',
  `OrderCompleted` tinyint(1) DEFAULT '0',
  `Check10YearsWarranty` tinyint(1) DEFAULT '1',
  `Check10YearsFreeMaintenance` tinyint(1) DEFAULT '1',
  `CheckFreeOpeningClosing` tinyint(1) DEFAULT '1',
  `CheckNoPayment` tinyint(1) DEFAULT '1',
  `YearsWarranty` int(11) DEFAULT '10',
  `LifeTimeWarranty` tinyint(1) DEFAULT '0',
  `SignatureReq` tinyint(1) DEFAULT '0',
  `Discount` float NOT NULL DEFAULT '0',
  `customerId` int(11) DEFAULT NULL,
  `invoiceId` int(11) DEFAULT NULL,
  `permitId` int(11) DEFAULT NULL,
  PRIMARY KEY  (`POID`),
  UNIQUE KEY `PONUMBER` (`PONUMBER`),
  UNIQUE KEY `invoiceId` (`invoiceId`),
  UNIQUE KEY `permitId` (`permitId`),
  KEY `CustomerId` (`customerId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `llx_ea_po`
  ADD CONSTRAINT `llx_ea_po_ibfk_1` FOREIGN KEY (`customerId`) REFERENCES `llx_societe` (`rowid`);
