DROP PROCEDURE IF EXISTS `createPermit`;
--
CREATE PROCEDURE `createPermit`(IN `tpId` INT, IN `poId` INT)
    MODIFIES SQL DATA

	INSERT INTO `llx_amhppermits_buildingpermit` (
        `ref`, `entity`, `label`, `fk_soc`, `POID`,
        `date_creation`, `tms`,
        `fk_user_creat`, `fk_user_modif`, `import_key`, `status`, `state`,
        `town`, `zip`, `barcode`, `lot`, `block`, `subdivision`, `pbpg`,
        `metesandbounds`, `contractornum`, `qualifiernum`, `contractorname`,
        `qualifiername`, `contractoraddress`, `contractorcity`,
        `contractorstate`, `contractorzip`, `currentuse`,
        `descriptionofwork`, `totalalum`, `units`, `floors`, `valueofwork`,
        `tp_address`, `tp_city`, `tp_state`, `tp_zip`, `tp_phone`,
        `pickup_name`, `pickup_address`, `pickup_city`, `pickup_state`,
        `pickup_zip`, `pickup_phone`, `jobaddress`, `owner`) 
    SELECT 
        tp.barcode, '1', tp.nom, tpId, poId,
        NOW(), NOW(), 
        0, 0, NULL, '1', tp_state.nom, 
        tp.town, tp.zip, tp.barcode, tpef.lot, tpef.block, tpef.subdivision, tpef.pbpg,
        tpef.metesandbounds, getConst('MAIN_INFO_SIREN'), LEFT(getConst('MAIN_INFO_TVAINTRA'),4), getConst('MAIN_INFO_SOCIETE_NOM'),
        getConst('MAIN_INFO_SOCIETE_MANAGERS'), getConst('MAIN_INFO_SOCIETE_ADDRESS'), getConst('MAIN_INFO_SOCIETE_TOWN'), 
        co_state.nom, getConst('MAIN_INFO_SOCIETE_ZIP'), getConst('MAIN_INFO_SOCIETE_FORME_JURIDIQUE'), 
        po.DESCRIPTIONOFWORK, po.TOTALALUM, NULL, NULL, po.SALESPRICE, 
        tp.address, tp.town, tp_state.nom, tp.zip, tp.phone, 
        getConst('MAIN_INFO_SOCIETE_NOM'), getConst('MAIN_INFO_SOCIETE_ADDRESS'), getConst('MAIN_INFO_SOCIETE_TOWN'), co_state.nom,
        getConst('MAIN_INFO_SOCIETE_ZIP'), getConst('MAIN_INFO_SOCIETE_TEL'), tp.address, tp.nom
    FROM 
         llx_societe tp LEFT JOIN 
         llx_ea_po po on po.customerId = tp.rowid LEFT JOIN
         llx_c_departements tp_state on tp_state.rowid = tp.fk_departement LEFT JOIN     
         llx_c_departements co_state on co_state.rowid = getConst('MAIN_INFO_SOCIETE_STATE') LEFT JOIN
         llx_societe_extrafields tpef on tpef.fk_object = tp.rowid
    where 
        tp.rowid = tpId AND
        po.POID = poId;

