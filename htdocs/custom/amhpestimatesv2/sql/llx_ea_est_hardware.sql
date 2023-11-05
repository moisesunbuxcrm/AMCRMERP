--
-- Table structure for table `llx_ea_est_hardware`
--

CREATE TABLE IF NOT EXISTS `llx_ea_est_hardware` (
  `id` int(11) NOT NULL auto_increment,
  `estimateitemid` int(11) NOT NULL,

  `provider` varchar(50) default null,
  `product_ref` 	varchar(128) NOT NULL,
  `hardwaretype` varchar(50) NOT NULL,
  `configuration` varchar(50) NOT NULL,

  PRIMARY KEY  (`id`),
  UNIQUE KEY `estimateitemid` (`estimateitemid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `llx_ea_est_hardware`
  ADD CONSTRAINT `llx_ea_est_hardware_ibfk_1` FOREIGN KEY (`estimateitemid`) REFERENCES `llx_ea_estimate_item` (`id`);
