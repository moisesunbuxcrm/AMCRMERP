--
-- Table structure for table `llx_ea_comm_ack`
--

CREATE TABLE IF NOT EXISTS `llx_ea_comm_ack` (
  `id` int(11) NOT NULL,
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `active` tinyint(4) NOT NULL DEFAULT '1'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Indexes for table `llx_ea_comm_ack`
--
ALTER TABLE `llx_ea_comm_ack`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for table `llx_ea_comm_ack`
--
ALTER TABLE `llx_ea_comm_ack`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
