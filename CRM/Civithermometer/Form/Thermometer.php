<?php

use CRM_Civithermometer_ExtensionUtil as E;

/**
 * Form controller class
 *
 * @see https://docs.civicrm.org/dev/en/latest/framework/quickform/
 */
class CRM_Civithermometer_Form_Thermometer extends CRM_Contribute_Form_ContributionPage {

  public function getDefaultContext() {
    return 'create';
  }

  public function getDefaultEntity() {
    return 'ContributionPage';
  }

  public function buildQuickForm() {

    // add fields
    $this->addField('thermometer_is_enabled');
    $this->addField('thermometer_stretch_goal');
    $this->addField('thermometer_is_double');
    $this->addField('thermometer_offset_amount');
    $this->addField('thermometer_offset_donors');

    $this->addButtons([
      [
        'type' => 'submit',
        'name' => E::ts('Save'),
        'isDefault' => TRUE,
      ],
    ]);

    $this->assign('elementNames', $this->getRenderableElementNames());
    parent::buildQuickForm();
  }

  public function preProcess() {
    parent::preProcess();
  }

  public function postProcess() {

    // get the submitted form values.
    $params = $this->controller->exportValues($this->_name);
    if ($this->_action & CRM_Core_Action::UPDATE) {
      $params['id'] = $this->_id;
    }
    $params['thermometer_is_enabled'] = CRM_Utils_Array::value('thermometer_is_enabled', $params, FALSE);
    if ($params['thermometer_is_enabled']) {
      $params['thermometer_is_double'] = CRM_Utils_Array::value('thermometer_is_double', $params, FALSE);
      $params['thermometer_stretch_goal'] = CRM_Utils_Array::value('thermometer_stretch_goal', $params, FALSE);
    }

    $dao = CRM_Contribute_BAO_ContributionPage::create($params);
    $className = CRM_Utils_String::getClassName($this->_name);
    $subPage = strtolower($className);
    $nextPage = 'membership';

    CRM_Core_Session::setStatus(ts("'%1' information has been saved.",
      [1 => CRM_Utils_Array::value('title', CRM_Utils_Array::value($subPage, $this->get('tabHeader')), $className)]
    ), $this->getTitle(), 'success');

    $this->postProcessHook();
    if ($this->controller->getButtonName('submit') == "_qf_{$className}_next") {
      CRM_Utils_System::redirect(CRM_Utils_System::url("civicrm/admin/contribute/{$subPage}",
        "action=update&reset=1&id={$this->_id}"
      ));
    }
    elseif ($this->controller->getButtonName('submit') == "_qf_{$className}_submit_savenext") {
      if ($nextPage) {
        CRM_Utils_System::redirect(CRM_Utils_System::url("civicrm/admin/contribute/{$nextPage}",
          "action=update&reset=1&id={$this->_id}"
        ));
      }
      else {
        CRM_Utils_System::redirect(CRM_Utils_System::url("civicrm/admin/contribute",
          "reset=1"
        ));
      }
    }
    else {
      CRM_Utils_System::redirect(CRM_Utils_System::url("civicrm/admin/contribute", 'reset=1'));
    }
  }

  /**
   * Get the fields/elements defined in this form.
   *
   * @return array (string)
   */
  public function getRenderableElementNames() {
    // The _elements list includes some items which should not be
    // auto-rendered in the loop -- such as "qfKey" and "buttons".  These
    // items don't have labels.  We'll identify renderable by filtering on
    // the 'label'.
    $elementNames = [];
    foreach ($this->_elements as $element) {
      /** @var HTML_QuickForm_Element $element */
      $label = $element->getLabel();
      if (!empty($label)) {
        $elementNames[] = $element->getName();
      }
    }
    return $elementNames;
  }

}
