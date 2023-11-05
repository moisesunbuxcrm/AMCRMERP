# AHMP ESTIMATES FOR DOLIBARR ERP CRM

## Introduction

This module adds estimate and production order management to **Dolibarr** for use in A&M Hurricane Protection

To read official documentation on how to develop modules for Dolibarr go to [Module development](https://wiki.dolibarr.org/index.php/Module_development)

## Reading this documentation

This documentation is creating using a lightweight markup language called **Markdown**. You can read a much nicer formatted version of this documentation using an appropriate browser extension like [Markdown Viewer](https://chrome.google.com/webstore/detail/markdown-viewer/ckkdlimhmcjmikdlpkmbgfkaikojcbjk).

## Source Code Repository

The code for this module is part of a **Git** sourcecode repository. The entire history of the source code changes are recorded locally and can be access using the Git command line which can be downloaded from [https://git-scm.com/](https://git-scm.com/). In addition, there is a copy maintained in the cloud in a repository managed by Element Alley (Paul Dermody). The remote repository is not required for operation and a full copy of all code is kept locally too.

## Module Definition

The module definition file is in [modAMHPESTIMATES.class.php](core/modules/modAMHPESTIMATES.class.php). All the places that we extend Dolibarr start here.

The following extensions are defined in this module and they are configured in the module definition file.

- module_parts
  - css - lists custom css files included in all pages
  - hooks - a list of "hooks" i.e. predefined places in Dolibarr where we want to add code. These hooks translate into calls into the file [actions_amhpestimates.class.php](class/actions_amhpestimates.class.php).
- const - indicates constant values that are used throughout the code and which can be changed in the "Other setup" section of the Dolibarr admin portal
- langfiles indicates the name of the files in the lang folder with translations into English and Spanish.
- tabs - used to add a new Estimates tab to the Third Party card. This tab is implemented in the file [thirdparty/estimates.php](thirdparty/estimates.php).
- dictionaries - contains additions to the Dictionaries in the Setup section of the Dolibarr portal.
- boxes - includes the content of the file [core/boxes/amhpestimates.php](core/boxes/amhpestimates.php) which implements a widget for the home page showing the latest estimates.
- rights - contains permission definitions which can then be assigned to individual users
- menu - contains links that should be included in different parts of the Dolibarr portal including the top toolbar icon for Estimates
- create_stored_procedures() is a PHP function that creates all the required stored procedures needed for this module.

## Hooks

All the hooks are implemented in file [actions_amhpestimates.class.php](class/actions_amhpestimates.class.php) and are defined in the **module_parts** section of the module definition file [modAMHPESTIMATES.class.php](core/modules/modAMHPESTIMATES.class.php). All the hooks are implemented in their own PHP files and imported as needed into the [actions_amhpestimates.class.php](class/actions_amhpestimates.class.php) file.

The fields with and extension like .js.php will be processed by the PHP backend and converted to a regular javascript file before being downloaded to the browser. These files for the most part contain javascript that dynamically hides or modifies elements on the default Dolibarr pages.

We only created one hook for this module which is used to add a counter to the Estimates tab of the Third Party card. This is done in the file [actions_amhpestimates.class.php](class/actions_amhpestimates.class.php).

## Estimates

This is added as a new icon in the toolbar at the top of the Dolibarr navigation menu. It has several different components.

The estimates are stored in tables in the same database used by Dolibarr but in custom tables.

Note: throughout the code and database, the estimates are referred to as Production Orders or simply PO's.

### Estimate List and the Card

These PHP files are used to display the list of estimates as well as the estimate card. The [list.php](list.php) file displays a list of estimates and allows you to search through all of them. The [card.php](card.php) file loads the ReactJS application used for editing estimates. This is the most complex piece of this module and is described below.

### EaProductionOrders Class
This class is used to load and update esimtates stored in the lx_ea_po table. The class is defined in the file [eaproductionorders.class.php](class/eaproductionorders.class.php).

### EaProductionOrderItems Class
This class is used to load and update line items for esimtates stored in the lx_ea_po_item table. The class is defined in the file [eaproductionorderitems.class.php](class/eaproductionorderitems.class.php).

### RESTful Database updates

All updates made from the React application are done through the PHP files in the 'db' folder in the root of this module. Some updates are:

- Copying an existing PO
- Creating a new Dolibarr invoice from an exiting PO
- Creating and updating PO's and items for PO's
- Delete items and PO's
- Searching the list of PO's

## Printing Estimates

This is implemented using two sets of files. 

The first set are those in the 'print' folder in the root of this module. These files handle the HTTP requests for printing PDF documents.

- [estimate.php](print/estimate.php) - this is used to print an estimate PDF using templates defined in the core/modules/doc folder.
- [posingle.php](print/posingle.php) - this is used to print an production order PDF using templates defined in the core/modules/doc folder.
- [posummary.php](print/posummary.php) - this is used to print a summary for multiple production orders PDF using templates defined in the core/modules/doc folder.

The templates are also PHP files with PHP code that uses the TCPDF library to generate PDF's on the fly. The templates are:

- [pdf_base.php](core/modules/doc/pdf_base.php) - not used directly but is a base class containing common code for all other templates
- [pdf_po_base.php](core/modules/doc/pdf_po_base.php) - not used directly but is a base class containing common code for all other production order templates
- [pdf_po_contract.modules.php](core/modules/doc/pdf_po_contract.modules.php) - template for printing contract PDFs
- [pdf_po_instorder.modules.php](core/modules/doc/pdf_po_instorder.modules.php) - template for printing installation order PDFs
- [pdf_po_invoice.modules.php](core/modules/doc/pdf_po_invoice.modules.php) - template for printing invoice PDFs
- [pdf_po_standard.modules.php](core/modules/doc/pdf_po_standard.modules.php) - template for printing simple estimate PDFs
- [pdf_posingle_standard.modules.php](core/modules/doc/pdf_posingle_standard.modules.php) - template for printing simple produciton order PDFs for a single PO
- [pdf_posummary_standard.modules.php](core/modules/doc/pdf_posummary_standard.modules.php) - template for printing production order summaries from the estimate list page
- [pdf_posummary_base.modules.php](core/modules/doc/pdf_posummary_base.modules.php) - not used directly but is a base class containing common code for other production order templates

## SQL

The SQL needed to build the tables for the estimates support in Dolibarr. The SQL creates and then populates the tables and stored procedures. These SQL files are executed automatically by Dolibarr when the module is enabled.

## Estimates ReactJS Application

The estimate card is implemented using ReactJS which allows us to edit the estimates in a much more convenient way thatis not normally allowed by Dolibarr. ReactJS is a Javascript library that s maintained by Facebook for builting UI's.

The ReactJS application is located in the 'dev' folder but it is compiled into a compressed Javascript file called estimates.js also located in the root folder. Please do not make changes to this file because it is generated automatically. Changes to the application must be made in the dev folder and a new estimates.js file should be created. The application is built using webpack and npm.

To rebuild the app, go into the dev folder. The various build targets are documented in the package.json file. If you use an IDE like Visual Studio Code, it will recognize the build targets automatically and you can build the application from the Visual Studio Code UI.

- npm run build-prod - builds a production verison of the application suitable for deloyment to the A&M server
- npm run build - builds a development version which can be used for debugging and stepping through the code.

## Application structure

The application uses ReactJS for the UI and uses MobX for state management to implement the editor for estimates.

The application is instantiated from the file [card.php](card.php) in the root folder of this module. The ReactJS library will load and run the code in the file [dev\src\index.js](dev\src\index.js). This file instantiates the main ReactJS component called EstimatesApp which is defined in the file [EstimatesApp.js](dev\src\components\EstimatesApp.js). This component is the main entry point into the ReactJS application. This and the other principal components are described here.

- [EstimatesApp](dev\src\components\EstimatesApp.js) - gets the current PO from the [EstimateStore](dev\src\stores\EstimateStore.js) and displays the navigation buttons to move through the PO's, a [ProductionOrder](dev\src\components\ProductionOrders\ProductionOrder.js) component for viewing and editing PO's, and all the buttons below the PO editor for creating, editing, deleting, and printing POs and related documents.

- [EstimateStore](dev\src\stores\EstimateStore.js), [ProductionOrderStore](dev\src\stores\ProductionOrderStore.js), [ProductionOrderItemStore](dev\src\stores\ProductionOrderItemStore.js) - these components manage a cache of ProductionOrder objects and their line items and use the PHP files in the db folder to create, update, and delete PO's and items. They also keep track of which PO is currently visible, and any unsaved changes in the currently cached PO's.

- [ProductionOrder](dev\src\components\ProductionOrders\ProductionOrder.js) - combines all the React components needed to display the current PO and switch between the read-only and the editable view of the PO.

- [ProductionOrderModel](dev\src\models\ProductionOrderModel.js) - this class is the in-memory representation of a PO in ReactJS. It uses MobX to keep the fields in this model and the UI tectfields and other components in sync with each other. Changing a field in this class will automatically update the UI component AND any dependent calculated fields. MobX provides this functionality.

- [ProductionOrderItemModel](dev\src\models\ProductionOrderItemModel.js) - this class is the in-memory representation of a PO line item in ReactJS. Works in the same way as the [ProductionOrderModel](dev\src\models\ProductionOrderModel.js) but at the level of a line item from the PO.


