CREATE TABLE `llx_amhppermits_buildingpermit` (
  `rowid` int(11) AUTO_INCREMENT PRIMARY KEY NOT NULL,
  `ref` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `entity` int(11) NOT NULL DEFAULT '1',
  `label` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fk_soc` int(11) DEFAULT NULL,
  `POID` int(11),
  `EID` int(11),
  `date_creation` datetime NOT NULL,
  `tms` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fk_user_creat` int(11) NOT NULL,
  `fk_user_modif` int(11) DEFAULT NULL,
  `import_key` varchar(14) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` int(11) NOT NULL,
  `state` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `town` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `zip` varchar(25) COLLATE utf8_unicode_ci DEFAULT NULL,
  `barcode` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `lot` varchar(25) COLLATE utf8_unicode_ci DEFAULT NULL,
  `block` varchar(25) COLLATE utf8_unicode_ci DEFAULT NULL,
  `subdivision` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `pbpg` varchar(25) COLLATE utf8_unicode_ci DEFAULT NULL,
  `metesandbounds` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `contractornum` varchar(25) COLLATE utf8_unicode_ci DEFAULT NULL,
  `qualifiernum` varchar(25) COLLATE utf8_unicode_ci DEFAULT NULL,
  `contractorname` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `qualifiername` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `contractoraddress` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `contractorcity` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `contractorstate` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `contractorzip` varchar(25) COLLATE utf8_unicode_ci DEFAULT NULL,
  `currentuse` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `descriptionofwork` varchar(512) COLLATE utf8_unicode_ci DEFAULT NULL,
  `totalalum` double(24,8) DEFAULT NULL,
  `units` int(11) DEFAULT NULL,
  `floors` int(11) DEFAULT NULL,
  `valueofwork` double(24,8) DEFAULT NULL,
  `tp_address` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `tp_city` varchar(25) COLLATE utf8_unicode_ci DEFAULT NULL,
  `tp_state` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `tp_zip` varchar(25) COLLATE utf8_unicode_ci DEFAULT NULL,
  `tp_phone` varchar(25) COLLATE utf8_unicode_ci DEFAULT NULL,
  `pickup_name` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `pickup_address` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `pickup_city` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `pickup_state` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `pickup_zip` varchar(25) COLLATE utf8_unicode_ci DEFAULT NULL,
  `pickup_phone` varchar(25) COLLATE utf8_unicode_ci DEFAULT NULL,
  `jobaddress` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `owner` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `archname` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `archaddress` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `archcity` varchar(25) COLLATE utf8_unicode_ci DEFAULT NULL,
  `archstate` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `archzip` varchar(25) COLLATE utf8_unicode_ci DEFAULT NULL,
  `archphone` varchar(25) COLLATE utf8_unicode_ci DEFAULT NULL,
  `bondname` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `bondaddress` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `bondcity` varchar(25) COLLATE utf8_unicode_ci DEFAULT NULL,
  `bondstate` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `bondzip` varchar(25) COLLATE utf8_unicode_ci DEFAULT NULL,
  `bondphone` varchar(25) COLLATE utf8_unicode_ci DEFAULT NULL,
  `lendername` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `lenderaddress` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `lendercity` varchar(25) COLLATE utf8_unicode_ci DEFAULT NULL,
  `lenderstate` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `lenderzip` varchar(25) COLLATE utf8_unicode_ci DEFAULT NULL,
  `lenderphone` varchar(25) COLLATE utf8_unicode_ci DEFAULT NULL,
  `buildingdeptcity` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `improvementstype` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
  `permittype` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
  `buildingcategory` varchar(25) COLLATE utf8_unicode_ci DEFAULT NULL,
  `owner_name_address` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `owner_interest_in_property` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `titleholder_name_address` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `surety_name_address_phone` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `surety_bond` double(24,8) DEFAULT NULL,
  `lender_name_address` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `notices_name_address_phone` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `lienor_name_address_phone` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `expiration_date` datetime DEFAULT NULL,
  `notice_of_commencement_date` datetime DEFAULT NULL,
  `dade_or_book` varchar(25) COLLATE utf8_unicode_ci DEFAULT NULL,
  `dade_or_page` varchar(25) COLLATE utf8_unicode_ci DEFAULT NULL,
  `notice_of_commencement_term_date` datetime DEFAULT NULL,
  `notice_of_term_applies_to` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `notice_of_term_portion` varchar(600) COLLATE utf8_unicode_ci DEFAULT NULL,
  `unpaid_labor_and_materials` varchar(600) COLLATE utf8_unicode_ci DEFAULT NULL,
  `qualifier_produced_id` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `owner_produced_id` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `notary_name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `comm_ack_as` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `term_attachments` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description_of_improvements` varchar(512) COLLATE utf8_unicode_ci DEFAULT NULL,
  `cnf` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `notary_commission_expires` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `unit` varchar(25) COLLATE utf8_unicode_ci DEFAULT NULL,
  `building_no` varchar(25) COLLATE utf8_unicode_ci DEFAULT NULL,
  `flood_zone` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `bfe` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `floor_area` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `construction_type` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `occupancy_group` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `attachment` int(11) DEFAULT '-1',
  `owner_builder` int(11) DEFAULT '-1',
  `permittype_other` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `improvementstype_other` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `contractorphone` varchar(25) COLLATE utf8_unicode_ci DEFAULT NULL,
  `contractoremail` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `titleholdername` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `titleholderaddress` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `titleholdercity` varchar(25) COLLATE utf8_unicode_ci DEFAULT NULL,
  `titleholderstate` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `titleholderzip` varchar(25) COLLATE utf8_unicode_ci DEFAULT NULL,
  `titleholderphone` varchar(25) COLLATE utf8_unicode_ci DEFAULT NULL,
  `archemail` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `notice_of_commencement_expr_date` datetime DEFAULT NULL,
  `qualifiertitle` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `coowner` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `legal_attached` int(11) DEFAULT '-1',
  `designatednameln` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `designatedphoneln` varchar(25) COLLATE utf8_unicode_ci DEFAULT NULL,
  `owner_title` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `coowner_title` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `designatedname` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `designatedaddress` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `designatedcity` varchar(25) COLLATE utf8_unicode_ci DEFAULT NULL,
  `designatedstate` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `designatedzip` varchar(25) COLLATE utf8_unicode_ci DEFAULT NULL,
  `designatedphone` varchar(25) COLLATE utf8_unicode_ci DEFAULT NULL,
  `owner_to_sign` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `comm_ack_individ_flag` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `owner_personally_known` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `qualifier_personally_known` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `lienorname` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `lienoraddress` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `lienorcity` varchar(25) COLLATE utf8_unicode_ci DEFAULT NULL,
  `lienorstate` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `lienorzip` varchar(25) COLLATE utf8_unicode_ci DEFAULT NULL,
  `lienorphone` varchar(25) COLLATE utf8_unicode_ci DEFAULT NULL,
  `unpaid_lienors` varchar(600) COLLATE utf8_unicode_ci DEFAULT NULL,
  `lienamount` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `hasapproval` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `processnum` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `owner_signature_date` datetime DEFAULT NULL,
  `primarypermit` varchar(50) COLLATE utf8_unicode_ci DEFAULT 'Primary Permit',
  `tp_fax` varchar(25) COLLATE utf8_unicode_ci DEFAULT NULL,
  `tp_email` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `permitfee` double(24,8) DEFAULT NULL,
  `contractorfax` varchar(25) COLLATE utf8_unicode_ci DEFAULT NULL,
  `tract` varchar(25) COLLATE utf8_unicode_ci DEFAULT NULL,
  `commission_number` varchar(25) COLLATE utf8_unicode_ci DEFAULT NULL,
  `permitno` varchar(25) COLLATE utf8_unicode_ci DEFAULT NULL,
  `totallinearft` double(24,8) DEFAULT NULL,
  `proposeduse` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `homeownerassoc` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `dumpsterpermit` int(11) DEFAULT '1',
  `classificationofwork` varchar(512) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
