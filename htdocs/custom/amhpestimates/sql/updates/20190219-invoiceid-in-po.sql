ALTER TABLE `llx_ea_po` ADD `invoiceId` INT NULL DEFAULT NULL AFTER `customerId`;
ALTER TABLE `dolibarr`.`llx_ea_po` ADD UNIQUE (`invoiceId`);

