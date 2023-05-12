<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 5.19                                               |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2019                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the GNU Affero General Public License           |
 | Version 3, 19 November 2007 and the CiviCRM Licensing Exception.   |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the GNU Affero General Public License for more details.        |
 |                                                                    |
 | You should have received a copy of the GNU Affero General Public   |
 | License and the CiviCRM Licensing Exception along                  |
 | with this program; if not, contact CiviCRM LLC                     |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
 */

/*
 * Settings metadata file
 */
return [
  'civithermometer_html' => [
    'group_name' => 'civithermometer',
    'group' => 'civithermometer',
    'name' => 'civithermometer_html',
    'filter' => 'civithermometer',
    'type' => 'String',
    'add' => '5.19',
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => 'HTML elements used to construct the thermometer',
    'title' => 'Thermometer HTML',
    'default' => '',
    'html_type' => 'TextArea',
    'html_attributes' => [
      'cols' => 80,
      'rows' => 20,
    ],
    'quick_form_type' => 'Element',
  ],
  'civithermometer_css' => [
    'group_name' => 'civithermometer',
    'group' => 'civithermometer',
    'name' => 'civithermometer_css',
    'filter' => 'civithermometer',
    'type' => 'String',
    'add' => '5.19',
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => 'CSS used to style thermometer HTML elements',
    'title' => 'Thermometer CSS',
    'html_type' => 'TextArea',
    'html_attributes' => [
      'cols' => 80,
      'rows' => 20,
    ],
    'quick_form_type' => 'Element',
  ],
];

