--
-- Table structure for table `llx_ea_glasstype`
--

CREATE TABLE `llx_ea_glasstype` (
  `id` int(11) NOT NULL,
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `active` tinyint(4) NOT NULL DEFAULT '1'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `llx_ea_glasstype`
--

--
-- Indexes for table `llx_ea_glasstype`
--
ALTER TABLE `llx_ea_glasstype`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for table `llx_ea_glasstype`
--
ALTER TABLE `llx_ea_glasstype`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

