--
-- Table structure for table `llx_ea_useofproperty`
--

CREATE TABLE IF NOT EXISTS `llx_ea_useofproperty` (
  `id` int(11) NOT NULL,
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `active` tinyint(4) NOT NULL DEFAULT '1'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Indexes for table `llx_ea_useofproperty`
--
ALTER TABLE `llx_ea_useofproperty`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for table `llx_ea_useofproperty`
--
ALTER TABLE `llx_ea_useofproperty`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
