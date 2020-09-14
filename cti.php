<?php

require_once 'cti.civix.php';
use CRM_Cti_ExtensionUtil as E;

/**
 * Implements hook_civicrm_config().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function cti_civicrm_config(&$config) {
  _cti_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_xmlMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function cti_civicrm_xmlMenu(&$files) {
  _cti_civix_civicrm_xmlMenu($files);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function cti_civicrm_install() {
  _cti_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_postInstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_postInstall
 */
function cti_civicrm_postInstall() {
  _cti_civix_civicrm_postInstall();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function cti_civicrm_uninstall() {
  _cti_civix_civicrm_uninstall();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function cti_civicrm_enable() {
  _cti_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function cti_civicrm_disable() {
  _cti_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function cti_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _cti_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function cti_civicrm_managed(&$entities) {
  _cti_civix_civicrm_managed($entities);
}

/**
 * Implements hook_civicrm_caseTypes().
 *
 * Generate a list of case-types.
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function cti_civicrm_caseTypes(&$caseTypes) {
  _cti_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implements hook_civicrm_angularModules().
 *
 * Generate a list of Angular modules.
 *
 * Note: This hook only runs in CiviCRM 4.5+. It may
 * use features only available in v4.6+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_angularModules
 */
function cti_civicrm_angularModules(&$angularModules) {
  _cti_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function cti_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _cti_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

/**
 * Implements hook_civicrm_entityTypes().
 *
 * Declare entity types provided by this module.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_entityTypes
 */
function cti_civicrm_entityTypes(&$entityTypes) {
  _cti_civix_civicrm_entityTypes($entityTypes);
}

/**
 * Implements hook_civicrm_navigationMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_navigationMenu
 */
function cti_civicrm_navigationMenu(&$menu) {
  _cti_civix_insert_navigation_menu($menu, 'Administer/CiviEvent', array(
    'label' => E::ts('CTI Integration Settings'),
    'name' => 'cti_integration_settings',
    'url' => 'civicrm/admin/setting/preferences/event/cti',
    'permission' => 'administer CiviCRM',
    'operator' => 'OR',
    'separator' => 0,
  ));
  _cti_civix_navigationMenu($menu);
}

/**
 * Implements hook_civicrm_post().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_post
 */
function cti_civicrm_post($op, $objectName, $objectId, &$objectRef) {
  if ($objectName != 'Participant' || ($op == 'delete' || $op == 'view')) {
    return;
  }
  if (CRM_Core_Transaction::isActive()) {
    CRM_Core_Transaction::addCallback(
      CRM_Core_Transaction::PHASE_POST_COMMIT,
      'cti_civicrm_post_callback', [$objectId]
    );
  }
  else {
    cti_civicrm_post_callback($objectId);
  }
}

/**
 * @param $op
 * @param $objectName
 * @param $objectId
 * @param $objectRef
 */
function cti_civicrm_post_callback($objectId) {
  $participantHook = new CRM_Cti_Hook_Post_SyncParticipant($objectId);
  $participantHook->sync();
}
