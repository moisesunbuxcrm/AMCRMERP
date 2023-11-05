-- BEGIN MODULEBUILDER INDEXES
ALTER TABLE llx_amhppermits_buildingpermit ADD INDEX idx_amhppermits_buildingpermit_rowid (rowid);
ALTER TABLE llx_amhppermits_buildingpermit ADD INDEX idx_amhppermits_buildingpermit_ref (ref);
ALTER TABLE llx_amhppermits_buildingpermit ADD INDEX idx_amhppermits_buildingpermit_entity (entity);
ALTER TABLE llx_amhppermits_buildingpermit ADD INDEX idx_amhppermits_buildingpermit_fk_soc (fk_soc);
ALTER TABLE llx_amhppermits_buildingpermit ADD INDEX idx_amhppermits_buildingpermit_status (status);
ALTER TABLE llx_amhppermits_buildingpermit ADD INDEX idx_amhppermits_buildingpermit_poid (poid);
ALTER TABLE llx_amhppermits_buildingpermit ADD INDEX idx_amhppermits_buildingpermit_eid (eid);
-- END MODULEBUILDER INDEXES

--ALTER TABLE llx_amhppermits_buildingpermit ADD UNIQUE INDEX uk_amhppermits_buildingpermit_fieldxyz(fieldx, fieldy);

ALTER TABLE `llx_amhppermits_buildingpermit` ADD CONSTRAINT `llx_amhppermits_buildingpermit_po_fk` FOREIGN KEY (`POID`) REFERENCES `llx_ea_po` (`POID`);
ALTER TABLE `llx_amhppermits_buildingpermit` ADD CONSTRAINT `llx_amhppermits_buildingpermit_e_fk` FOREIGN KEY (`EID`) REFERENCES `llx_ea_estimate` (`id`);
