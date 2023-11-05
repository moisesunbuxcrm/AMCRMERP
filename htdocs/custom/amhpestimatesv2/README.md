# AHMP ESTIMATES FOR DOLIBARR ERP CRM

## Introduction

This module adds estimate and production order management to **Dolibarr** for use in A&M Hurricane Protection

To read official documentation on how to develop modules for Dolibarr go to [Module development](https://wiki.dolibarr.org/index.php/Module_development)

## Reading this documentation

This documentation is creating using a lightweight markup language called **Markdown**. You can read a much nicer formatted version of this documentation using an appropriate browser extension like [Markdown Viewer](https://chrome.google.com/webstore/detail/markdown-viewer/ckkdlimhmcjmikdlpkmbgfkaikojcbjk).

## Source Code Repository

The code for this module is part of a **Git** sourcecode repository. The entire history of the source code changes are recorded locally and can be access using the Git command line which can be downloaded from [https://git-scm.com/](https://git-scm.com/). In addition, there is a copy maintained in the cloud in a repository managed by Element Alley (Paul Dermody). The remote repository is not required for operation and a full copy of all code is kept locally too.

## Module Definition

The module definition file is in [modAMHPESTIMATESV2.class.php](core/modules/modAMHPESTIMATESV2.class.php). All the places that we extend Dolibarr start here.

The following extensions are defined in this module and they are configured in the module definition file.

- module_parts
  - css - lists custom css files included in all pages
  - hooks - a list of "hooks" i.e. predefined places in Dolibarr where we want to add code. These hooks translate into calls into the file [actions_amhpestimatesV2.class.php](class/actions_amhpestimatesV2.class.php).
- const - indicates constant values that are used throughout the code and which can be changed in the "Other setup" section of the Dolibarr admin portal
- langfiles indicates the name of the files in the lang folder with translations into English and Spanish.
- tabs - used to add a new Estimates tab to the Third Party card. This tab is implemented in the file [thirdparty/estimatesv2.php](thirdparty/estimatesv2.php).
- dictionaries - contains additions to the Dictionaries in the Setup section of the Dolibarr portal.
- boxes - includes the content of the file [core/boxes/amhpestimates2.php](core/boxes/amhpestimates2.php) which implements a widget for the home page showing the latest estimates.
- rights - contains permission definitions which can then be assigned to individual users
- menu - contains links that should be included in different parts of the Dolibarr portal including the top toolbar icon for Estimates
- create_stored_procedures() is a PHP function that creates all the required stored procedures needed for this module.

## Hooks

All the hooks are implemented in file [actions_amhpestimatesV2.class.php](class/actions_amhpestimatesV2.class.php) and are defined in the **module_parts** section of the module definition file [modAMHPESTIMATESV2.class.php](core/modules/modAMHPESTIMATESV2.class.php). All the hooks are implemented in their own PHP files and imported as needed into the [actions_amhpestimatesV2.class.php](class/actions_amhpestimatesV2.class.php) file.

The fields with and extension like .js.php will be processed by the PHP backend and converted to a regular javascript file before being downloaded to the browser. These files for the most part contain javascript that dynamically hides or modifies elements on the default Dolibarr pages.

We only created one hook for this module which is used to add a counter to the Estimates tab of the Third Party card. This is done in the file [actions_amhpestimatesV2.class.php](class/actions_amhpestimatesV2.class.php).

Pending...