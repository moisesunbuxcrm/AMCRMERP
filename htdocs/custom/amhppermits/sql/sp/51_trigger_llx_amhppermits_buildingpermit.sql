DROP TRIGGER if exists permit_delete;
--
DELIMITER $$
CREATE TRIGGER permit_delete BEFORE DELETE ON llx_amhppermits_buildingpermit
       FOR EACH ROW
       BEGIN
              update llx_ea_po set permitId=null where permitId=OLD.rowid;
              update llx_ea_estimate set permitId=null where permitId=OLD.rowid;
       END
$$
DELIMITER ;

----
DROP TRIGGER if exists permit_insert;
----
DELIMITER $$
CREATE TRIGGER permit_insert AFTER INSERT ON llx_amhppermits_buildingpermit
       FOR EACH ROW 
       BEGIN
              update llx_ea_po set permitId=NEW.rowid where POID=NEW.poid;
              update llx_ea_estimate set permitId=NEW.rowid where id=NEW.eid;
       END
$$
DELIMITER ;
