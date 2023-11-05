--
-- Table structure for table `llx_ea_productconfig`
--

CREATE TABLE `llx_ea_productconfig` (
  `id` int(11) NOT NULL,
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `active` tinyint(4) NOT NULL DEFAULT '1'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Indexes for table `llx_ea_productconfig`
--
ALTER TABLE `llx_ea_productconfig`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for table `llx_ea_productconfig`
--
ALTER TABLE `llx_ea_productconfig`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;
