ALTER TABLE `llx_ea_po` ADD `permitId` INT NULL DEFAULT NULL AFTER `invoiceId`;
ALTER TABLE `dolibarr`.`llx_ea_po` ADD UNIQUE (`permitId`);

