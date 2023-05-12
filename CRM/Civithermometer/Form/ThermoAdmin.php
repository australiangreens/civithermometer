<?php

use CRM_Civithermometer_ExtensionUtil as E;

/**
 * Form controller class
 *
 * @see https://docs.civicrm.org/dev/en/latest/framework/quickform/
 */
class CRM_Civithermometer_Form_ThermoAdmin extends CRM_Core_Form {
  private $_settingFilter = ['group' => 'civithermometer'];
  private $_submittedValues = [];
  private $_settings = [];

  public function buildQuickForm() {
    $settings = $this->getFormSettings();
    foreach ($settings as $name => $setting) {
			if (isset($setting['quick_form_type'])) {
				$options = NULL;
				if (isset($setting['pseudoconstant'])) {
					$options = civicrm_api3('Setting', 'getoptions', ['field' => $name]);
			  }
				$add = 'add' . $setting['quick_form_type'];
				if ($add == 'addElement') {
					$this->$add(
						$setting['html_type'],
						$name,
						ts($setting['title']),
						($options !== NULL) ? $options['values'] : CRM_Utils_Array::value('html_attributes', $setting, array()),
						($options !== NULL) ? CRM_Utils_array::value('html_attributes', $setting, array()) : NULL
					);
				}
				else {
					$this->$add($name, ts($setting['title']));
				}
				$this->assign("{$name}_description", $setting['description']);
			}
		}

		$this->addButtons([
			[
				'type' => 'submit',
				'name' => ts('Submit'),
				'isDefault' => TRUE,
			]
		]);

    // export form elements
    $this->assign('elementNames', $this->getRenderableElementNames());
    parent::buildQuickForm();
  }

  public function postProcess() {
		$this->_submittedValues = $this->exportValues();
		$this->saveSettings();
		parent::postProcess();
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

	/**
	 * Get the settings we are going to allow to be set on this form
	 *
	 * @return array
	 */
	public function getFormSettings() {
		if (empty($this->_settings)) {
			$settings = civicrm_api3('setting', 'getfields', ['filters' => $this->_settingFilter]);
		}
		return $settings['values'];
	}
	
	/**
	 * Save the settings entered on this form
	 *
	 * @return NULL
	 */
	public function saveSettings() {
		$settings = $this->getFormSettings();
		$values = array_intersect_key($this->_submittedValues, $settings);
		civicrm_api3('setting', 'create', $values);
	}

	/**
	 * Set defaults for the form
	 *
	 * @return array
	 * @see CRM_Core_Form::setDefaultValues()
	 */
	public function setDefaultvalues() {
		$existing = civicrm_api3('setting', 'get', ['return' => array_keys($this->getFormSettings())]);
		$defaults = [];
		$domainID = CRM_Core_Config::domainID();
		foreach ($existing['values'][$domainID] as $name => $value) {
			$defaults[$name] = $value;
		}
		return $defaults;
	}
}
