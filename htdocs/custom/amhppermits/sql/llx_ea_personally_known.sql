--
-- Table structure for table `llx_ea_personally_known`
--

CREATE TABLE IF NOT EXISTS `llx_ea_personally_known` (
  `id` int(11) NOT NULL,
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `active` tinyint(4) NOT NULL DEFAULT '1'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Indexes for table `llx_ea_personally_known`
--
ALTER TABLE `llx_ea_personally_known`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for table `llx_ea_personally_known`
--
ALTER TABLE `llx_ea_personally_known`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
