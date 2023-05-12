<?php
use CRM_Civithermometer_ExtensionUtil as E;

/**
 * Collection of upgrade steps.
 */
class CRM_Civithermometer_Upgrader extends CRM_Extension_Upgrader_Base {

  // By convention, functions that look like "function upgrade_NNNN()" are
  // upgrade tasks. They are executed in order (like Drupal's hook_update_N).

  /**
   * Example: Run an external SQL script when the module is installed.
   *
   */
  public function install() {
    // Run install SQL if the thermometer columns don't exist
    if (!CRM_Core_BAO_SchemaHandler::checkIfFieldExists('civicrm_contribution_page', 'thermometer_is_enabled')) {
      $this->executeSqlFile('sql/install.sql');
    }
  }

  /**
   * Add extension specific columns to civicrm_contribution_page table
   *
   * @return TRUE on success
   * @throws Exception
   */
  public function upgrade_4200() {
    $this->ctx->log->info('Applying update 4200');
    if (!CRM_Core_BAO_SchemaHandler::checkIfFieldExists('civicrm_contribution_page', 'thermometer_is_enabled')) {
      $this->executeSqlFile('sql/install.sql');
    }
    return TRUE;
  }


  /**
   * Add two new columns to civicrm_contribution_page table
   *
   * @return TRUE on success
   * @throws Exception
   */
  public function upgrade_4201() {
    $this->ctx->log->info('Applying update 4201');
    if (!CRM_Core_BAO_SchemaHandler::checkIfFieldExists('civicrm_contribution_page', 'therometer_offset_amount')) {
      $this->executeSqlFile('sql/upgrade_4201.sql');
    }
    return TRUE;
  }

}
