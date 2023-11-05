--
-- Table structure for table `llx_ea_estimate_item`
--

CREATE TABLE IF NOT EXISTS `llx_ea_estimate_item` (
  `id` int(11) NOT NULL auto_increment,
  `estimateid` int(11) NOT NULL,
  `itemno` int(11) NOT NULL,

  `itemtype` varchar(25) NOT NULL,
  `modtype` varchar(25) NOT NULL,
  `wintype` varchar(25) NOT NULL,
  `name` varchar(256) NOT NULL,
  `image` varchar(256) NOT NULL,
  `color` varchar(25) default NULL,
  `cost_price` double(12,4) NOT NULL DEFAULT '0.0000',
  `sales_price` double(12,4) NOT NULL DEFAULT '0.0000',
  `sales_discount` float NOT NULL DEFAULT '0',
  `inst_price` double(12,4) NOT NULL DEFAULT '0.0000',
  `inst_discount` double(12,4) NOT NULL DEFAULT '0.0000',
  `otherfees` double(12,4) NOT NULL DEFAULT '0.0000',
  `finalprice` double(12,4) NOT NULL DEFAULT '0.0000',
  `quantity` int(11) NOT NULL DEFAULT 1,

  PRIMARY KEY  (`id`),
  KEY `estimateid` (`estimateid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `llx_ea_estimate_item`
  ADD CONSTRAINT `llx_ea_estimate_item_ibfk_1` FOREIGN KEY (`estimateid`) REFERENCES `llx_ea_estimate` (`id`);
