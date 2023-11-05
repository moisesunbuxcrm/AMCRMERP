DROP PROCEDURE IF EXISTS `getDataForNewPermitForSocid`;
--
CREATE PROCEDURE `getDataForNewPermitForSocid`(IN `socid` INT)
    READS SQL DATA
SELECT 
        po.poid, tp.barcode as ref, tp.nom as label, tp.rowid as fk_soc,
        tp_state.code_departement as state, 
        tp.town, truncateZip(tp.zip) as zip, tp.barcode, tpef.lot, tpef.block, tpef.subdivision, tpef.pbpg,
        tpef.metesandbounds, getConst('MAIN_INFO_SIREN') as contractornum, LEFT(getConst('MAIN_INFO_TVAINTRA'),4) as qualifiernum, getConst('MAIN_INFO_SOCIETE_NOM') as contractorname,
        getConst('MAIN_INFO_SOCIETE_MANAGERS') as qualifiername, getConst('MAIN_INFO_SOCIETE_ADDRESS') as contractoraddress, getConst('MAIN_INFO_SOCIETE_TOWN') as contractorcity, 
        co_state.code_departement as contractorstate, truncateZip(getConst('MAIN_INFO_SOCIETE_ZIP')) as contractorzip, getConst('AMHP_DEFAULT_PERMITS_CONTRACTOR_EMAIL') as contractoremail,
        getConst('MAIN_INFO_SOCIETE_TEL') as contractorphone, getConst('MAIN_INFO_SOCIETE_FAX') as contractorfax, getConst('AMHP_DEFAULT_CURRENT_USE') as currentuse,         getConst('AMHP_DEFAULT_CURRENT_USE') as proposeduse, 
        getConst('AMHP_DEFAULT_CURRENT_USE') as proposeduse, 
        'INSTALLATION OF' as descriptionofwork, 'INSTALLATION OF' as description_of_improvements, po.TOTALALUM as totalalum, po.TOTALLINEARFT as totallinearft,
        NULL as units, NULL as floors, po.SALESPRICE as valueofwork, 
        tp.address as tp_address, tp.town as tp_city, tp_state.code_departement as tp_state, truncateZip(tp.zip) as tp_zip, tp.phone as tp_phone, 
        getConst('MAIN_INFO_SOCIETE_MANAGERS') as pickup_name, getConst('MAIN_INFO_SOCIETE_ADDRESS') as pickup_address, getConst('MAIN_INFO_SOCIETE_TOWN') as pickup_city, co_state.code_departement as pickup_state,
        truncateZip(getConst('MAIN_INFO_SOCIETE_ZIP')) as pickup_zip, getConst('MAIN_INFO_SOCIETE_TEL') as pickup_phone, tp.address as jobaddress, tp.nom as owner, tpef.buildingdeptcity as buildingdeptcity,
        bd.customimprovementtype as improvementstype, bd.custompermittype as permittype, null as buildingcategory,
        concat(tp.nom,' ',tp.address,' ',tp.town,' ',tp_state.code_departement, ' ', truncateZip(tp.zip)) as owner_name_address, 
        getConst('AMHP_DEFAULT_INTEREST_IN_PROP') as owner_interest_in_property, 'D/L' as owner_produced_id,
        'Personally Known' as qualifier_personally_known, getConst('AMHP_DEFAULT_NOTARY_NAME') as notary_name, getConst('AMHP_DEFAULT_COMMISION_EXPIRES') as notary_commission_expires,
        'Contractor''s Final Payment Affidavit' as term_attachments, 'OWNER' as comm_ack_as,
        'As...' as comm_ack_individ_flag,
        'all' as notice_of_term_applies_to, 'N/A' as unpaid_labor_and_materials, 'N/A' as unpaid_lienors,
        tpef.unit, tpef.building as building_no, 'President' as qualifiertitle, tp.nom as owner_to_sign,
        'NOT APPLICABLE' as lienorname, 'NOT APPLICABLE' as lienamount, 'Has approval' as hasapproval, 
        'Produced Identification' as owner_personally_known, 'Primary Permit' as primarypermit, tp.fax as tp_fax, tp.email as tp_email,
        po.permit as permitfee,  getConst('AMHP_DEFAULT_COMMISION_NUMBER') as commission_number, 'RESIDENTIAL' as classificationofwork
    FROM 
         llx_societe tp LEFT JOIN 
         llx_ea_po po on po.customerId = tp.rowid LEFT JOIN
         llx_c_departements tp_state on tp_state.rowid = tp.fk_departement LEFT JOIN     
         llx_c_departements co_state on co_state.rowid = getConst('MAIN_INFO_SOCIETE_STATE') LEFT JOIN
         llx_societe_extrafields tpef on tpef.fk_object = tp.rowid LEFT JOIN
         llx_ea_builddepts bd on tpef.buildingdeptcity = bd.city_name
    where 
        tp.rowid = socid
    limit 1
