<?php

use CRM_Cti_SettingsManager as SettingsManager;

/**
 * Class CRM_Cti_Test_Fabricator_Setting
 */
class CRM_Cti_Test_Fabricator_Setting {

  /**
   * @param array $params
   */
  public static function fabricate($params = []) {
    $params = array_merge(static::getDefaultParams(), $params);
    foreach ($params as $key => $value) {
      \Civi::settings()->set($key, $value);
    }
  }

  /**
   * @return array
   */
  private static function getDefaultParams() {
    return [
      SettingsManager::API_URL => 'https://sample.com',
      SettingsManager::USER_KEY => 'ctiuserkey',
      SettingsManager::AUTH_KEY => 'ctiauthkey',
    ];
  }

}
