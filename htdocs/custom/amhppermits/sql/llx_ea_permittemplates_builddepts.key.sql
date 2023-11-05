--
-- Indexes for table `llx_ea_permittemplates_builddepts`
--
ALTER TABLE `llx_ea_permittemplates_builddepts`
  ADD UNIQUE KEY `template_id_builddept_id` (`template_id`,`builddept_id`) USING BTREE,
  ADD KEY `template_id` (`template_id`),
  ADD KEY `builddept_id` (`builddept_id`);

--
-- Constraints for table `llx_ea_permittemplates_builddepts`
--
ALTER TABLE `llx_ea_permittemplates_builddepts`
  ADD CONSTRAINT `llx_ea_permittemplates_builddepts_ibfk_1` FOREIGN KEY (`template_id`) REFERENCES `llx_ea_permittemplates` (`rowid`),
  ADD CONSTRAINT `llx_ea_permittemplates_builddepts_ibfk_2` FOREIGN KEY (`builddept_id`) REFERENCES `llx_ea_builddepts` (`rowid`);
