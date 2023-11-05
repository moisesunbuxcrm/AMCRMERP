--
-- Table structure for table `llx_ea_est_impact`
--

CREATE TABLE IF NOT EXISTS `llx_ea_est_impact` (
  `id` int(11) NOT NULL auto_increment,
  `estimateitemid` int(11) NOT NULL,

  `provider` varchar(50) default null,
  `is_def_color` tinyint(1) NOT NULL default 0,
  `is_def_glass_color` tinyint(1) NOT NULL default 0,
  `is_standard` tinyint(1) NOT NULL default 1,
  `roomtype` int(11) NOT NULL,
  `roomnum` int(11) NOT NULL,
  `floornum` int(11) NOT NULL default 1,

  `product_ref` 	varchar(128) NOT NULL,
  `configuration` varchar(50) NOT NULL,
  `is_screen` tinyint(1) NOT NULL,
  `frame_color` varchar(50) NOT NULL,
  `is_colonial` tinyint(1) NOT NULL,
  `colonial_fee` double(12,4) NOT NULL DEFAULT '0.0000',
  `colonial_across` int(11) NOT NULL DEFAULT '0',
  `colonial_down` int(11) NOT NULL DEFAULT '0',
  `width` double(12,4) NOT NULL DEFAULT '0.0000',
  `widthtxt` varchar(16) NOT NULL DEFAULT '0',
  `height` double(12,4) NOT NULL DEFAULT '0.0000',
  `heighttxt` varchar(16) NOT NULL DEFAULT '0',
  `length` double(12,4) NOT NULL DEFAULT '0.0000',
  `lengthtxt` varchar(16) NOT NULL DEFAULT '0',
  `glass_type` varchar(50) NOT NULL,
  `glass_color` varchar(50) NOT NULL,
  `interlayer` varchar(50) NOT NULL,
  `coating` varchar(50) NOT NULL,
  `room_description` 	varchar(128) NOT NULL,

  PRIMARY KEY  (`id`),
  KEY `roomtype` (`roomtype`),
  UNIQUE KEY `estimateitemid` (`estimateitemid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `llx_ea_est_impact`
  ADD CONSTRAINT `llx_ea_est_impact_ibfk_1` FOREIGN KEY (`estimateitemid`) REFERENCES `llx_ea_estimate_item` (`id`);

ALTER TABLE `llx_ea_est_impact`
  ADD CONSTRAINT `llx_ea_est_impact_ibfk_2` FOREIGN KEY (`roomtype`) REFERENCES `llx_ea_rooms` (`id`);
