<h1 align="center">
  <a href="https://www.ndi.org/"><img src="https://www.ndi.org/sites/all/themes/ndi/images/NDI_logo_svg.svg" alt="NDI Logo" width="200"></a>
</h1>

<h1 align="center">
  Civi
</h1>

  ### Table of Contents
  1. [Introduction](#introduction)
  1. [CiviCRM Installation](#installing-civicrm)
  1. [Installing NDI Civi Extension](#installing-ndi-civi-extension)
  1. [Additional Modules](#additional-modules)



### Introduction

The Civi DemTool is based on the open-source [CiviCRM](https://civicrm.org/) contact management software . Civi enables civic organizations, government officials, and political parties to track, analyze, connect with and respond to the needs of their constituents and members online. This repository contains instructions for installing the basic CiviCRM software, followed by how to install the NDI-specific Civi extension found in this repository. Further Civi extensions that are used by NDI are listed at the bottom.

### Installing CiviCRM

There are several ways to install CiviCRM. Some of them can be found here. NDI uses Drupal as its preferred Content Management System (CMS) for hosting CiviCRM (a full list of CiviCRM-compatible CMS's can be found [here](https://docs.civicrm.org/sysadmin/en/latest/planning/cms/)). Specifically, NDI uses [Aegir](http://www.aegirproject.org/) as a multi-hosting solution for Drupal sites that provides an easy interface for creating additional Civi sites. The instructions below detail the most straightforward way to install CiviCRM using Drupal, or alternatively from an image on AWS for users who use this service.

#### Installing from AWS AMI

If you use Amazon Web Services (AWS), a prebuilt version of Civi can be found [here](https://bitnami.com/stack/civicrm/cloud/aws/amis). These images contain an already installed version of CiviCRM ready for deployment on an Amazon EC2 server. If you use this solution, you can skip the section labeled *Installing using Drupal*.

To install from an AMI image, follow the link above and select the AMI for the region you want to host in. Clicking on the AMI link will take you to the instance deployment page. It is recommended to use at least a t2.small server size.

#### Installing using Drupal

NDI installs Civi using Drupal as a CMS. For the lastest Drupal install instructions, see [here](https://www.drupal.org/docs/7/modules/features/getting-started).

Once Drupal is installed, follow the steps [here](https://docs.civicrm.org/sysadmin/en/latest/install/drupal7/) to install CiviCRM.

### Installing NDI Civi Extension

Once CiviCRM is installed, go where the application has been installed and go into the directory `civicrm/ext`. This directory is where all extensions will be located. To add an extension, simply clone a copy of the extension inside this folder. So, to install the NDI Civi extension, copy this repository into the folder using `git clone https://github.com/nditech/org.ndi.demtools.git`. When you log in to Civi, the NDI Civi Extension should now appear.

### Additional Extensions

Below are additional Civi extensions that can be installed to add additional functionality. The process for installing these will be the same as installing the NDI Civi extension - simply go to `civicrm/ext` and clone the extension, substituting in the repository url and folder name for the extension you are installing.


##### Recommended Extensions: 
While the above process enables the NDI Civi extension, we recommend adding the following extensions for a full Civi experience:

Chatbot
Chatbot CiviRules Integration
SMS Conversation
Translation Helper
CiviRules
Angular Profiles
API v4
Contact Layout Editor
Doctor When
FlexMailer
CiviCRM Bootstrap Theme
[Contact Numbers](https://github.com/nditech/org.ndi.contactnumbers)
Custom Field Value Permissioning
[Telerivet SMS Integration](https://github.com/nditech/org.ndi.sms.telerivet)
[Mosaico](https://github.com/veda-consulting/uk.co.vedaconsulting.mosaico)

[](https://github.com/nditech/uk.org.futurefirst.networks.civipoints)
[](https://github.com/nditech/io.3sd.chainedsms)


#### Other Extensions To Consider:
In addition to the extensions above, listed below are other extensions you can consider installing to extend Civi's functionality.

Mandrill Transactional Emails
CiviCRM Export to Excel
SparkPost Integration
Event Calendar
SYSTOPIA Birthdays
Civisualize
Send Event Conf
CiviVolunteer
General Data Protection Regulation
MailChimp
CiviPoints

##### Location Extensions
These extensions include the location layer information various countries:

* [Kenya Constituencies](https://github.com/nditech/org.ndi.kenyaconstituencies)
* [Kenya States](https://github.com/nditech/org.ndi.kenyastates)
* [Liberia Counties](https://github.com/nditech/org.ndi.liberiacounties)
* [Liberia States](https://github.com/nditech/org.ndi.liberiastates)
* [Macedonia Provinces](https://github.com/nditech/org.ndi.macedoniaprovinces)
* [Malawi Constituencies](https://github.com/nditech/org.ndi.malawiconstituencies)
* [Malawi Districts](https://github.com/nditech/org.ndi.malawidistricts)
* [Moldova States](https://github.com/nditech/org.ndi.moldovarayons)
* [Morocco Communes](https://github.com/nditech/org.ndi.moroccocommunes)
* [Morocco Provinces](https://github.com/nditech/org.ndi.moroccoprovinces)
* [Nigeria Local Government Areas](https://github.com/nditech/org.ndi.nigerialgas)
* [Nigeria States](https://github.com/nditech/org.ndi.nigeriastates)
* [Serbia Municipalities](https://github.com/nditech/org.ndi.serbiamunicipalities)
* [Serbia Counties](https://github.com/nditech/org.ndi.serbiacounties)
* [Ukraine Counties](https://github.com/nditech/ukrainerayons)