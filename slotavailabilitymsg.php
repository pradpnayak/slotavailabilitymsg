<?php

require_once 'slotavailabilitymsg.civix.php';
use CRM_Slotavailabilitymsg_ExtensionUtil as E;

/**
 * Implements hook_civicrm_config().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_config/
 */
function slotavailabilitymsg_civicrm_config(&$config) {
  _slotavailabilitymsg_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_xmlMenu().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_xmlMenu
 */
function slotavailabilitymsg_civicrm_xmlMenu(&$files) {
  _slotavailabilitymsg_civix_civicrm_xmlMenu($files);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_install
 */
function slotavailabilitymsg_civicrm_install() {
  _slotavailabilitymsg_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_postInstall().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_postInstall
 */
function slotavailabilitymsg_civicrm_postInstall() {
  _slotavailabilitymsg_civix_civicrm_postInstall();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_uninstall
 */
function slotavailabilitymsg_civicrm_uninstall() {
  _slotavailabilitymsg_civix_civicrm_uninstall();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_enable
 */
function slotavailabilitymsg_civicrm_enable() {
  _slotavailabilitymsg_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_disable
 */
function slotavailabilitymsg_civicrm_disable() {
  _slotavailabilitymsg_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_upgrade
 */
function slotavailabilitymsg_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _slotavailabilitymsg_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_managed
 */
function slotavailabilitymsg_civicrm_managed(&$entities) {
  _slotavailabilitymsg_civix_civicrm_managed($entities);
}

/**
 * Implements hook_civicrm_caseTypes().
 *
 * Generate a list of case-types.
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_caseTypes
 */
function slotavailabilitymsg_civicrm_caseTypes(&$caseTypes) {
  _slotavailabilitymsg_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implements hook_civicrm_angularModules().
 *
 * Generate a list of Angular modules.
 *
 * Note: This hook only runs in CiviCRM 4.5+. It may
 * use features only available in v4.6+.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_angularModules
 */
function slotavailabilitymsg_civicrm_angularModules(&$angularModules) {
  _slotavailabilitymsg_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_alterSettingsFolders
 */
function slotavailabilitymsg_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _slotavailabilitymsg_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

/**
 * Implements hook_civicrm_entityTypes().
 *
 * Declare entity types provided by this module.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_entityTypes
 */
function slotavailabilitymsg_civicrm_entityTypes(&$entityTypes) {
  _slotavailabilitymsg_civix_civicrm_entityTypes($entityTypes);
}

/**
 * Implements hook_civicrm_thems().
 */
function slotavailabilitymsg_civicrm_themes(&$themes) {
  _slotavailabilitymsg_civix_civicrm_themes($themes);
}

/**
 * Implements hook_civicrm_buildForm().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_buildForm
 */
function slotavailabilitymsg_civicrm_buildForm($formName, &$form) {
  if (in_array($formName, [
    'CRM_Event_Form_Registration_Register',
    'CRM_Event_Form_Registration_AdditionalParticipant'])
  ) {

    if ($form->getVar('_priceSetId')) {
      $result = civicrm_api3('PriceFieldValue', 'get', [
        'return' => ['id', 'label'],
        'max_value' => ['IS NOT NULL' => 1],
        'options' => ['limit' => 0],
        'price_field_id.price_set_id' => $form->getVar('_priceSetId'),
      ])['values'];
      if (!empty($result)) {
        $message = ts('Slot available for %priLabel - %priOpLabel : %partCount');
        CRM_Core_Resources::singleton()->addVars('slotavailabilitymsg', [
          'message' => $message,
          'priceFieldIds' => (array_column($result, 'label', 'id')),
          'eventId' => $form->getVar('_eventId'),
        ]);
        CRM_Core_Resources::singleton()->addScriptFile('slotavailabilitymsg', 'js/slotavailabilitymsg.js', 100);
        CRM_Core_Resources::singleton()->addStyleFile('slotavailabilitymsg', 'css/style.css');
      }
    }
  }
}

/**
 * Implements hook_civicrm_alterAPIPermissions().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_alterAPIPermissions
 */
function slotavailabilitymsg_civicrm_alterAPIPermissions($entity, $action, &$params, &$permissions) {
  $permissions['slotavailabilitymsg']['getpartcount'] = [];
}
