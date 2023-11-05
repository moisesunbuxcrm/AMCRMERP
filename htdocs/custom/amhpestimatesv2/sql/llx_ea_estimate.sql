--
-- Table structure for table `llx_ea_estimate`
--

--drop table if exists `llx_ea_estimate`
CREATE TABLE IF NOT EXISTS `llx_ea_estimate` (
  `id` int(11) NOT NULL auto_increment,
  `estimatenum` varchar(25) NOT NULL,
  `quotedate` datetime NOT NULL,
  `customerid` int(11) NOT NULL,
  `folio` varchar(25) DEFAULT null,
  `vendor` varchar(50) NOT NULL,
  `vendor_phone` varchar(20),
  `defcolor` varchar(25) DEFAULT null,
  `defglasscolor` varchar(25) DEFAULT null,
  `is_alteration` tinyint(1) NOT NULL DEFAULT 0,
  `is_installation_included` tinyint(1) NOT NULL DEFAULT 0,
  `deposit_percent` int(11) NOT NULL default 0,
  `deposit_percent_with_install` int(11) NOT NULL default 0,
  `percent_final_inspection` int(11) NOT NULL default 0,
  `warranty_years` int(11) NOT NULL default 0,
  `pay_upon_completion` tinyint(1) NOT NULL DEFAULT 0,
  `new_construction_owner_responsability` tinyint(1) NOT NULL DEFAULT 0,
  `status` varchar(50) NOT NULL,
  `status_reason` varchar(100) NOT NULL,
  `approved_date` datetime,
  `rejected_date` datetime,
  `delivered_date` datetime,
  `permitId` int(11),

  `add_sales_discount` float NOT NULL DEFAULT 0,
  `add_inst_discount` float NOT NULL DEFAULT 0,
  `permits` double(12,4) NOT NULL DEFAULT '0.0000',
  `salestax` double(12,4) NOT NULL DEFAULT '0.0000',
  `totalprice` double(12,4) NOT NULL DEFAULT '0.0000',

  `notes` varchar(1024) DEFAULT NULL,
  `public_notes` varchar(1024) DEFAULT NULL,

  PRIMARY KEY  (`id`),
  UNIQUE KEY `estimatenum` (`estimatenum`),
  KEY `customerid` (`customerid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `llx_ea_estimate`
  ADD CONSTRAINT `llx_ea_estimate_ibfk_1` FOREIGN KEY (`customerid`) REFERENCES `llx_societe` (`rowid`);

ALTER TABLE `llx_ea_estimate`
  ADD CONSTRAINT `llx_ea_estimate_ibfk_2` FOREIGN KEY (`permitId`) REFERENCES `llx_amhppermits_buildingpermit` (`rowid`);
