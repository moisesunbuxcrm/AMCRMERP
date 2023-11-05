DROP TRIGGER if exists ins_estimate_ref;
----
CREATE TRIGGER ins_estimate_ref AFTER DELETE ON llx_facture
       FOR EACH ROW update llx_ea_po set invoiceId=null where invoiceId= OLD.rowid;