# AHMP FOR DOLIBARR ERP CRM

## Introduction
Dolibarr Modules allow us to add functionality to Dolibarr without changing any of the system files. This module adds some customizations to Dolibarr for use in A&M Hurricane Protection

To read official documentation on how to develop modules for **Dolibarr** go to [Module development](https://wiki.dolibarr.org/index.php/Module_development)

## Reading this documentation

This documentation is creating using a lightweight markup language called **Markdown**. You can read a much nicer formatted version of this documentation using an appropriate browser extension like [Markdown Viewer](https://chrome.google.com/webstore/detail/markdown-viewer/ckkdlimhmcjmikdlpkmbgfkaikojcbjk).

## Source Code Repository

The code for this module is part of a **Git** sourcecode repository. The entire history of the source code changes are recorded locally and can be access using the Git command line which can be downloaded from [https://git-scm.com/](https://git-scm.com/). In addition, there is a copy maintained in the cloud in a repository managed by Element Alley (Paul Dermody). The remote repository is not required for operation and a full copy of all code is kept locally too.

## Module Definition

The module definition file is in [modAMHP.class.php](core\modules\modAMHP.class.php). All the places that we extend Dolibarr start here.

The following extensions are defined in this module and they are configured in the module definition file.

- module_parts
  - css - lists custom css files included in all pages
  - js - lists custom javascript files included in all pages
  - hooks - a list of "hooks" i.e. predefined places in Dolibarr where we want to add code. These hooks translate into calls into the file [actions_amhp.class.php](class\actions_amhp.class.php).
- langfiles indicates the name of the files in the lang folder with translations into English and Spanish.
- dictionaries - contains additions to the Dictionaries in the Setup section of the Dolibarr portal.
- rights - contains permission definitions which can then be assigned to individual users
- menu - contains links that should be included in different parts of the Dolibarr portal

## Hooks

All the hooks are implemented in file [actions_amhp.class.php](class\actions_amhp.class.php) and are defined in the **module_parts** section of the module definition file [modAMHP.class.php](core\modules\modAMHP.class.php). All the hooks are implemented in their own PHP files and imported as needed into the [actions_amhp.class.php](class\actions_amhp.class.php) file.

The fields with and extension like .js.php will be processed by the PHP backend and converted to a regular javascript file before being downloaded to the browser. These files for the most part contain javascript that dynamically hides or modifies elements on the default Dolibarr pages.

The folders containing extensions are as follows.

- agenda - contains changes to the agenda pages for viewing and editing calendar events
- contact - contains changes to the Contact edit and view pages
- customers - conatins changes to the pages for editing and viewing third parties
- products - contains changes to the pages for editing and viewing products

## Building departments

The building departments are implemented using several components.

- Links added to menus from the [modAMHP.class.php](core\modules\modAMHP.class.php) file.
- The PHP files containing the card, list, and search pages in the buildepts folder
- The Eabuilddepts class implemented in [eabuilddepts.class.php](class\eabuilddepts.class.php).

## SQL

The SQL needed to build the tables for the dictionaries are kept in the sql folder. The SQL creates and then populates the tables. These SQL files are executed automatically by Dolibarr when the module is enabled.

