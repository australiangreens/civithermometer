<?php

require_once 'civithermometer.civix.php';
use CRM_Civithermometer_ExtensionUtil as E;

/**
 * Implements hook_civicrm_config().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_config/
 */
function civithermometer_civicrm_config(&$config) {
  _civithermometer_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_install
 */
function civithermometer_civicrm_install() {
  _civithermometer_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_enable
 */
function civithermometer_civicrm_enable() {
  _civithermometer_civix_civicrm_enable();
}

// --- Functions below this ship commented out. Uncomment as required. ---

/**
 * Implements hook_civicrm_preProcess().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_preProcess
 *

 // */

/**
 * Implements hook_civicrm_navigationMenu().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_navigationMenu
 */
function civithermometer_civicrm_navigationMenu(&$menu) {
  _civithermometer_civix_insert_navigation_menu($menu, 'Administer/CiviContribute', [
    'label' => E::ts('CiviThermometer Settings'),
    'name' => 'civithermometer_settings',
    'url' => 'civicrm/admin/thermometer',
    'permission' => 'administer CiviCRM',
    'operator' => 'OR',
    'separator' => 0,
  ]);
  _civithermometer_civix_navigationMenu($menu);
}

function civithermometer_civicrm_tabset($tabsetName, &$tabs, $context) {
  // check if the tab set is Contribution page manage
  if ($tabsetName == 'civicrm/admin/contribute') {
    if (!empty($context['contribution_page_id'])) {
      $contribID = $context['contribution_page_id'];
      $tab['thermometer'] = [
        'title' => ts('Thermometer'),
        'link' => NULL,
        'valid' => 1,
        'active' => 1,
        'current' => false,
      ];
      // Insert this tab into position 2
      $tabs = array_merge(
        array_slice($tabs, 0, 2),
        $tab,
        array_slice($tabs, 2)
       );
    }
    if (!empty($context['urlString']) && !empty($context['urlParams'])) {
      $tabs[CRM_Core_Action::VIEW] = [
        'title' => ts('Thermometer'),
        'name' => ts('Thermometer'),
        'url' => $context['urlString'] . 'thermometer',
        'qs' => $context['urlParams'],
        'uniqueName' => 'thermometer',
      ];
    }
  }
}

function civithermometer_civicrm_entityTypes(&$entityTypes) {
  $entityTypes['CRM_Contribute_DAO_ContributionPage']['fields_callback'][] = function($class, &$fields) {
    $fields['thermometer_is_enabled'] = [
      'name' => 'thermometer_is_enabled',
      'title' => E::ts('Add thermometer to the page'),
      'type' => CRM_Utils_Type::T_BOOLEAN,
      'entity' => 'ContributionPage',
      'bao' => 'CRM_Contribute_BAO_ContributionPage',
      'localizable' => 0,
      'html' => array(
        'type' => 'CheckBox',
      ),
    ];
    $fields['thermometer_is_double'] = [
      'name' => 'thermometer_is_double',
      'title' => E::ts('Is this a double your donation thermometer? (optional)'),
      'type' => CRM_Utils_Type::T_BOOLEAN,
      'entity' => 'ContributionPage',
      'bao' => 'CRM_Contribute_BAO_ContributionPage',
      'localizable' => 0,
      'html' => array(
        'type' => 'CheckBox',
      ),
    ];
    $fields['thermometer_stretch_goal'] = [
      'name' => 'thermometer_stretch_goal',
      'title' => E::ts('Stretch goal if goal amount is reached? (optional)'),
      'type' => CRM_Utils_Type::T_MONEY,
      'entity' => 'ContributionPage',
      'bao' => 'CRM_Contribute_BAO_ContributionPage',
      'localizable' => 0,
      'html' => array(
        'type' => 'Text',
      ),
    ];
    $fields['thermometer_offset_amount'] = [
      'name' => 'thermometer_offset_amount',
      'title' => E::ts('Adjust existing contribution total? (optional; use negative numbers to subtract)'),
      'type' => CRM_Utils_Type::T_MONEY,
      'entity' => 'ContributionPage',
      'bao' => 'CRM_Contribute_BAO_ContributionPage',
      'localizable' => 0,
      'html' => array(
        'type' => 'Text',
      ),
    ];
    $fields['thermometer_offset_donors'] = [
      'name' => 'thermometer_offset_donors',
      'title' => E::ts('Adjust existing number of contributors? (optional; use negative numbers to subtract)'),
      'type' => CRM_Utils_Type::T_INT,
      'entity' => 'ContributionPage',
      'bao' => 'CRM_Contribute_BAO_ContributionPage',
      'localizable' => 0,
      'html' => array(
        'type' => 'Text',
      ),
    ];
  };
}

function civithermometer_civicrm_buildForm($formName, &$form) {
  // Only focus on Contribution Pages
  if ($formName == 'CRM_Contribute_Form_Contribution_Main') {
    $formId = $form->_id;
    $contribPage = \Civi\Api4\ContributionPage::get()
      ->setSelect([
        'thermometer_is_enabled',
        'thermometer_is_double',
        'thermometer_stretch_goal',
        'goal_amount',
        'thermometer_offset_amount',
        'thermometer_offset_donors',
      ])
      ->addWhere('id', '=', $formId)
      ->setCheckPermissions(FALSE)
      ->execute();

    // Only continue if the thermometer_is_enabled variable is set to 1
    if ($contribPage->first()['thermometer_is_enabled'] == 1 && $contribPage->first()['goal_amount'] != NULL) {
      $contributions = \Civi\Api4\Contribution::get()
        ->addWhere('is_test', '=', 0)
        ->addWhere('contribution_status_id', '=', 1)
        ->addWhere('contribution_page_id', '=', $formId)
        ->setCheckPermissions(FALSE)
        ->execute();

      // Prepare variables to pass through to Javascript
      $amountGoal = $contribPage->first()['goal_amount'];
      $amountStretch = $contribPage->first()['thermometer_stretch_goal'];
      $isDouble = $contribPage->first()['thermometer_is_double'];
      $numberDonors = $contributions->count();
      $amountRaised = 0;
      $offsetRaised = $contribPage->first()['thermometer_offset_amount'];
      $offsetDonors = $contribPage->first()['thermometer_offset_donors'];

      if ($numberDonors > 0) {
        $amounts = array_column((array) $contributions, 'total_amount');
        $amountRaised = array_reduce($amounts, function ($a, $b) {
          return ($a += $b);
        });
      }

      // Apply offsets if defined
      $amountRaised += $offsetRaised;
      $numberDonors += $offsetDonors;

      // Get thermometer HTML and CSS
      $thermo_settings = \Civi\Api4\Setting::get()
        ->setSelect([
          'civithermometer_css',
          'civithermometer_html',
        ])
        ->setCheckPermissions(FALSE)
        ->execute();

      $css = $thermo_settings[0]['value'];
      $html = $thermo_settings[1]['value'];

      // Add thermo data to the page so our JS can access it
      CRM_Core_Resources::singleton()->addVars('civithermo', [
        'numberDonors' => $numberDonors,
        'amountGoal' => $amountGoal,
        'amountStretch' => $amountStretch,
        'amountRaised' => $amountRaised,
        'currency' => $form->_values['currency'],
        'isDouble' => $isDouble,
      ]);

      if (empty($form->_pcpInfo['id']) && !empty($form->_values['intro_text'])) {
        $intro_text = $form->_values['intro_text'];
        $intro_text .= html_entity_decode($html);
        $form->assign('intro_text', $intro_text);
      }

      CRM_Core_Resources::singleton()->addStyle($css);
      CRM_Core_Resources::singleton()->addScriptFile('civithermometer', 'js/civithermo.js');
      CRM_Core_Resources::singleton()->addScript('civithermo_render();');
    }
  }
}
