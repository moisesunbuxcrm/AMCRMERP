--
-- Table structure for table `llx_ea_est_design`
--

CREATE TABLE IF NOT EXISTS `llx_ea_est_design` (
  `id` int(11) NOT NULL auto_increment,
  `estimateitemid` int(11) NOT NULL,

  `provider` varchar(50) default null,

  `product_ref` 	varchar(128) NOT NULL,
  `width` double(12,4) NOT NULL DEFAULT '0.0000',
  `widthtxt` varchar(16) NOT NULL DEFAULT '0',
  `height` double(12,4) NOT NULL DEFAULT '0.0000',
  `heighttxt` varchar(16) NOT NULL DEFAULT '0',

  PRIMARY KEY  (`id`),
  UNIQUE KEY `estimateitemid` (`estimateitemid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `llx_ea_est_design`
  ADD CONSTRAINT `llx_ea_est_design_ibfk_1` FOREIGN KEY (`estimateitemid`) REFERENCES `llx_ea_estimate_item` (`id`);
