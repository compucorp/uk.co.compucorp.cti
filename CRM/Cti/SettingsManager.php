<?php

/**
 * Helps manage settings for the extension.
 */
class CRM_Cti_SettingsManager {

  /**
   * Constants for setting name
   */
  const GROUP_NAME = 'CTI Integration Settings';
  const GROUP = 'cti_integration';
  const API_URL = 'cti_api_url';
  const AUTH_KEY = 'cti_api_auth_key';
  const USER_KEY = 'cti_api_user_key';

  /**
   * Gets the extension setting fields
   *
   * @return array
   */
  public static function getSettingFields() {
    $settingFields = self::fetchSettingFields();
    if (!isset($settingFields) || empty($settingFields)) {
      $result = civicrm_api3('System', 'flush');

      if ($result['is_error'] == 0){
        $settingFields =  self::fetchSettingFields();
      }
    }

    return $settingFields;
  }

  /**
   * Fetch Settings fields
   */
  private static function fetchSettingFields() {
    return civicrm_api3('setting', 'getfields',[
      'filters' =>[ 'group' => self::GROUP],
    ])['values'];
  }

}
