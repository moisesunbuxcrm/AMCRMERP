--
-- Table structure for table `llx_ea_permittemplates`
--

CREATE TABLE `llx_ea_permittemplates` (
  `rowid` int(11) AUTO_INCREMENT PRIMARY KEY NOT NULL,
  `name` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `buttonorder` int(11) DEFAULT NULL,
  `filename` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
  `pagewidth` float NOT NULL DEFAULT '8.5',
  `pageheight` float NOT NULL DEFAULT '11',
  `pagecount` int(11) NOT NULL DEFAULT '1',
  `fontsize` int(11) NOT NULL DEFAULT '12'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
