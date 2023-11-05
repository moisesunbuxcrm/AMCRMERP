# AMHPPERMITS FOR DOLIBARR ERP CRM

## Introduction

This module adds the permits tool to **Dolibarr** for use in A&M Hurricane Protection

To read official documentation on how to develop modules for Dolibarr go to [Module development](https://wiki.dolibarr.org/index.php/Module_development)

## Reading this documentation

This documentation is creating using a lightweight markup language called **Markdown**. You can read a much nicer formatted version of this documentation using an appropriate browser extension like [Markdown Viewer](https://chrome.google.com/webstore/detail/markdown-viewer/ckkdlimhmcjmikdlpkmbgfkaikojcbjk).

## Source Code Repository

The code for this module is part of a **Git** sourcecode repository. The entire history of the source code changes are recorded locally and can be access using the Git command line which can be downloaded from [https://git-scm.com/](https://git-scm.com/). In addition, there is a copy maintained in the cloud in a repository managed by Element Alley (Paul Dermody). The remote repository is not required for operation and a full copy of all code is kept locally too.

## Module Builder

This module is different from the other two in that it was creatied using the MOdule Builder tool in dolibarr. Because of that it is closely dependent on the [BuildingPermit](class/buildingpermit.class.php) class. This class derives from the CommonObject class which uses the properties in the the **fields** array in BuildingPermit to deduce SQL statements for creating, updating, and deleting permits. The structure of this array is very important and determines. among other things:

- the order of the fields in the view and edit forms
- the component used to display the value or let you edit the value of each field
- which fields are shown in the various views of the permits

This class extends CommonObject which contains a lot of logic for guessing how the BuildingPermit should work.But the BuildingPermit class overrides some of this in order to change how the permits are displayed and edited. Especially since this can change from city to city.

Some of the ways this behaviour is overridden in the BuildingPermit class are as follows.

- dropdown values are injected into the **fields** array in the class constructor using data from the database
- Other attributes are added to some fields in the class constructor for treating some fields as comma separated values
- showPrintPermitActions() - is used to determine which permit templates to display for the current permit. Note that if you hold CTRL when youclick on a button, it opens the PDF in a new tab instead of downloading it. Also, if you hold CTRL and ALT when you click on the button it displays the fieldnames and row id instead of the actual data, this is a great way to find out which field in the database results in data on the permit.
- showOutputField() - change html components used in the building permit forms
- showInputField() - change html components used in the building permit forms

## Module Definition

The module definition file is in [modAMHPPermits.class.php](core/modules/modAMHPPermits.class.php). All the places that we extend Dolibarr start here.

The following extensions are defined in this module and they are configured in the module definition file.

- module_parts
  - css - lists custom css files included in all pages
  - js - lists custom javascript files included in all pages
  - hooks - we are not using hooks in this module
- const - indicates constant values that are used throughout the code and which can be changed in the "Other setup" section of the Dolibarr admin portal
- langfiles indicates the name of the files in the lang folder with translations into English and Spanish.
- tabs - used to add a new Permits tab to the Third Party card. This tab is implemented in the file [thirdparty/permits.php](thirdparty/permits.php).
- dictionaries - contains additions to the Dictionaries in the Setup section of the Dolibarr portal.
- rights - contains permission definitions which can then be assigned to individual users
- menu - contains links that should be included in different parts of the Dolibarr portal including the top toolbar icon for permits
- create_stored_procedures() is a PHP function that creates all the required stored procedures needed for this module.

## Custom Fields

The **customfields** folder contains PHP files which are used to display various building permit fields in custom ways. Usually this is a set of radio buttons or checkboxes that are specific to each city. The folder containing the templates for these fields is referenced in each building department row in the llx_ea_builddepts table.

As an example, the **improvements type** field has a custom set of checkboxes that are implemented in [improvementstype_edit.php](customfields/Miami-Dade%20County/Homestead/improvementstype_edit.php) and in [improvementstype_options.php](customfields/Miami-Dade%20County/Homestead/improvementstype_options.php).

## Printing permits

The entry point for printing permits is the file [printing\print.php](printing\print.php). This receives as parameters the id of the permit, and the template to use to fill out the permit data. The images used as the background for each permit are stored in the printing/templates folder. The position of each field as it is stored in the database table **llx_ea_permittemplates_fields** are very sensitive to changes i the template images so be very careful when updatig the images or you will need to update all the fields for that template.

This PHP file hands off display of the permit to the files [printing\pdf_permit.php](printing\pdf_permit.php) and [printing\pdf_base.php](printing\pdf_base.php). These files basically get the list of all appropriate fields from the **llx_ea_permittemplates_fields** table based on the template id and the permit id passed to [printing\print.php](printing\print.php). The fieldnames in that table can have modifiers which are funcitons that change how the data is displayed. All these functions are implement in the file [include\parse.inc.php](include\parse.inc.php).

## SQL

The SQL needed to build the tables for the estimates support in Dolibarr. The SQL creates and then populates the tables and stored procedures. These SQL files are executed automatically by Dolibarr when the module is enabled.

### Important tables

- llx_amhppermits_buildingpermit - contains the current permits
- llx_ea_permittemplates_builddepts - contains the templates that are enabled for each building department
- llx_ea_permittemplates - Contains the list of defined templates plus the path where the background image for each template is stored
- llx_ea_permittemplates_fields - contains the list of fields to be displayed on each template and where

## How to add a new permit application or PDF

The following pieces are used to display the PDFs for each bilding department.

1. The table `llx_ea_permittemplates` contains a list of templates and the number of pages in each template. If you have a new template you will need to add it here. If you are simply adding a page to an existing template then increase the number of pages in the appropriate. Make sure to also update the `sql/data.sql` file also, because this file is used to repopulate the database when the Dolibarr module is removed/readded.

    For example:

        INSERT IGNORE INTO `llx_ea_permittemplates` (`rowid`, `name`, `buttonorder`, `filename`, `pagewidth`, `pageheight`, `pagecount`, `fontsize`) VALUES
        (21, 'HOA Affidavit', 6, 'templates\\Miami-Dade County\\Miramar\\hoaaffidavit', 8.5, 11, 1, 9),
        (22, 'HS Affidavit', 6, 'templates\\Miami-Dade County\\Pinecrest\\saffidavit', 8.5, 11, 1, 9);

1. Add images to the appropriate folder (or create a new folder) in `printing/templates`. Create an image for each page and follow the required format for the page names: "`name_p1.png`" where name is a unique name for this template and p1 is the page number.

1. The table `llx_ea_permittemplates_builddepts` will need to be updated if this is a new template to associate the target building departments with the new template. If you are just adding a page then this table will already have the necessary entries. If this template replaces an existing template then you need an update:

    For example:

        UPDATE llx_ea_permittemplates_builddepts SET template_id=21 WHERE builddept_id=40 and template_id=1;
        UPDATE llx_ea_permittemplates_builddepts SET template_id=22 WHERE builddept_id=20 and template_id=1;

    If the template is a new template in addition to existing templates then you need to do an insert.

        INSERT IGNORE INTO `llx_ea_permittemplates_builddepts` (`template_id`, `builddept_id`) VALUES
        (21, 40),
        (22, 20);

1. If you need to customize some of the fields in the editor then check the next section

1. To add data to the PDF you must add entries to the `llx_ea_permittemplates_fields` table. For example:

        INSERT IGNORE INTO `llx_ea_permittemplates_fields` (`rowid`, `templateid`, `pageno`, `fieldname`, `x`, `y`, `w`, `h`) VALUES
        (773, 20, 1, 'qualifiername', 5, 5.69, 2.85, 0.17),
        (774, 20, 1, 'contractoremail', 5, 5.85, 2.85, 0.17),
        (775, 20, 1, 'contractorphone', 5, 6.01, 2.85, 0.17),
        (776, 20, 1, 'owner_to_sign', 1.05, 7.8, 2.85, 0.17);

    Again, remember to keep the `sql/data.sql` file up to date. The `x`, `y`, `w`, and `h` columns are measured in inches.

    The fieldname is one of the fields from the BuildingPermit class defined in htdocs\custom\amhppermits\class\buildingpermit.class.php. The fieldname can also contain simple functions which are defined in the file htdocs\custom\amhppermits\include\parse.inc.php.

### Tips

1. To view a PDF you must go to the Dolibarr CRM and click on the Permits button at the top of the page. Then select an appropriate permit from the building department that you want to test. This will show all the data for the permit. At the bottom of the page you will find buttons to display the various PDFs. 

1. Hold CTRL when you click on a button to show the PDF in the browser instead of downloading it.

1. Hold CTRL and ALT to show the PDF in the browser and display the row id and the field name that would be printed, rather than the actual values themselves. This is very useful for identifying which database rows produce each pice of data.

## How to customize fields in the editor for permits
If you want to change the fields displayed in the editor for the permittype field then take the following steps.

1. Search the llx_ea_builddepts table for the building department that will need a new custom list of permit types. For example, the "Town of Cutler Bay". Change the customfieldsdir column in the database to point to the folder containing the custom fields for that building department. For example: "Miami-Dade County/Cutler Bay".

    The SQL for this might look like this:

        update llx_ea_builddepts set customfieldsdir='Miami-Dade County/Cutler Bay' where rowid=35

1. In some cases, you may need to update the default value for a field customized like this. In the case of Cutler Bay we need to ensure the default permit type is 'Building' by changing the value of the custompermittype column.

    The SQL for this might look like this:

        update llx_ea_builddepts set custompermittype='Building' where rowid=35


1. Create a new folder in D:\wamp64\www\AMCRMERP\htdocs\custom\amhppermits\customfields\Miami-Dade County\Cutler Bay

1. In this folder, create the appropriate files to customize various fields. See other folders for examples. To customize the permittypes you need two files.

    1. permittype_options.php contains the valid values for the permittype field

        ~~~
            <?php
                $options = array(
                    'Building',             'Change Contractor',
                    'Electrical',           'Extension',
                    'Mechanical',           'Renewal', 
                    'Plumbing/Gas',         'Shop Drawing',
                    'Paving/Drainage',      'Sign',
                    'Roofing',              'Zoning',
                    'Public Works',         'Other'
                );
            ?>
        ~~~

    1. permittype_edit.php contains the code to display an editor for the permittype. The value '2' indicates the number of columns to display.

        ~~~
            <?php
                include("permittype_options.php");
                showRadioButtons($key, $value, $options, 2);
            ?>
        ~~~

You should now see the new permit types in the editor for any permit from that building department.

