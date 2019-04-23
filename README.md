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

The Civi DemTool takes the powerful, popular and well established [CiviCRM](https://civicrm.org/) contact management system, adds some NDI-created custom extensions, and then another set of additional publically contributed extensions to make it easy to use for typical political, civic or governmental groups in the developing world.” Civi enables civic organizations, government officials, and political parties to track, analyze, connect with and respond to the needs of their constituents and members online. This repository contains instructions for installing the basic CiviCRM software, followed by how to install the NDI-specific Civi extension found in this repository. Further Civi extensions that are used by NDI are listed at the bottom.

### Installing CiviCRM

There are several ways to install CiviCRM. Some of them can be found here. NDI uses Drupal as its preferred Content Management System (CMS) for hosting CiviCRM (a full list of CiviCRM-compatible CMS's can be found [here](https://docs.civicrm.org/sysadmin/en/latest/planning/cms/)). Specifically, NDI uses [Aegir](http://www.aegirproject.org/) as a multi-hosting solution for Drupal sites that provides an easy interface for creating additional Civi sites. The instructions below detail the most straightforward way to install CiviCRM using Drupal, or alternatively from an image on AWS for users who use this service.

#### Installing from AWS AMI

If you use Amazon Web Services (AWS), a prebuilt version of Civi can be found [here](https://bitnami.com/stack/civicrm/cloud/aws/amis). These images contain an already installed version of CiviCRM ready for deployment on an Amazon EC2 server. If you use this solution, you can skip the section labeled *Installing using Drupal*.

To install from an AMI image, follow the link above and select the AMI for the region you want to host in. Clicking on the AMI link will take you to the instance deployment page. Your server size will depend on the amount of usage you expect on your site. A test site can run on a t2.micro. To access the site, go to the public ip generated for the EC2 instance. The username by default is 'user', and the password can be found by right-clicking on the instance and going to 'Instance Settings' > 'Get System Log'.

#### Installing using Drupal

NDI installs Civi using Drupal as a CMS. For the lastest Drupal install instructions, see [here](https://www.drupal.org/docs/7/modules/features/getting-started).

Once Drupal is installed, follow the steps [here](https://docs.civicrm.org/sysadmin/en/latest/install/drupal7/) to install CiviCRM.

### Installing DemTools CiviCRM Extension

Once CiviCRM is installed, navigate to where the application has been installed and go into the directory `civicrm/ext`. This directory is where all extensions will be located. To add an extension, simply use Git to clone a copy of the extension inside this folder.

Much of the custom functionality NDI provides to adapt Civi to the needs of political partners has been consolidated into one “DemTools CiviCRM” extension. To install the DemTools CiviCRM extension, copy this repository into the civicrm/ext directory by navigating there and then running `git clone https://github.com/nditech/org.ndi.demtools.git`. After doing this, log in to Civi, and go to `https://name-of-your-site/en/civicrm/admin/extensions` to see the extensions present. The “DemTools CiviCRM” should now appear. From here, extensions can be installed to enable, or disabled.

### Additional Recommended Extensions

There are a wide range of useful community-created CiviCRM extensions that NDI uses to make the system easier to use, adapt for typical political or governmental organizations needs, or add functionality for the developing world. Below are additional Civi extensions that can be installed to add these NDI-recommended capabilities. 

The process for installing these will be the same as installing the NDI Civi extension - simply go to `civicrm/ext` and clone the extension, substituting in the repository url for the extension you are installing.

* [Chatbot](https://github.com/nditech/civicrm-messenger-extension.git)
* [SMS Conversation](https://github.com/3sd/civicrm-sms-conversation.git)
* [Translation Helper](https://github.com/coopsymbiotic/coop.symbiotic.translationhelper/)
* [CiviRules](https://docs.civicrm.org/civirules/en/latest/)
* [Angular Profiles](https://github.com/ginkgostreet/org.civicrm.angularprofiles)
* [API v4](https://github.com/civicrm/org.civicrm.api4)
* [Contact Layout Editor](https://github.com/civicrm/org.civicrm.contactlayout.git)
* [Doctor When](https://github.com/civicrm/org.civicrm.doctorwhen)
* [FlexMailer](https://github.com/civicrm/org.civicrm.flexmailer/)
* [Contact Numbers](https://github.com/nditech/org.ndi.contactnumbers)
* [Custom Field Value Permissioning](https://github.com/aghstrategies/org.ndi.fieldvaluepermission)
* [Telerivet SMS Integration](https://github.com/nditech/org.ndi.sms.telerivet)
* [Mosaico](https://github.com/veda-consulting/uk.co.vedaconsulting.mosaico)
* [Shoreditch](https://github.com/civicrm/org.civicrm.shoreditch)


#### Other Extensions To Consider:
In addition to the extensions above, listed below are other extensions you can consider installing to extend Civi's functionality.

* [Mandrill Transactional Emails](https://github.com/JMAConsulting/biz.jmaconsulting.mte)
* [CiviCRM Export to Excel](https://lab.civicrm.org/extensions/civiexportexcel.git)
* [SparkPost Integration](https://github.com/proexchange/com.pesc.sparkpost)
* [Event Calendar](https://github.com/osseed/com.osseed.eventcalendar.git)
* [SYSTOPIA Birthdays](https://github.com/systopia/de.systopia.birthdays.git)
* [Civisualize](https://github.com/TechToThePeople/civisualize.git)
* [Send Event Conf](https://lab.civicrm.org/extensions/sendgrid.git)
* [CiviVolunteer](https://github.com/civicrm/org.civicrm.volunteer)
* [General Data Protection Regulation](https://github.com/veda-consulting/uk.co.vedaconsulting.gdpr.git)
* [MailChimp](https://github.com/veda-consulting/uk.co.vedaconsulting.mailchimp.git)
* [CiviPoints](https://github.com/futurefirst/uk.org.futurefirst.networks.civipoints.git)

##### Location Extensions
The following country-specific extensions load updated and complete lists of states/provinces and counties for various countries where NDI has supported implementation of Civi. These extensions can be loaded to better allow users in these countries to accurately list the location of their contacts, activities, and events.

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
