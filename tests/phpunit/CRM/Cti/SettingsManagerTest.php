<?php

use CRM_Cti_SettingsManager as SettingsManager;

require_once __DIR__ . '/../../BaseHeadlessTest.php';

/**
 * Runs tests on SettingsManager.
 *
 * @group headless
 */
class CRM_Cti_SettingsManagerTest extends BaseHeadlessTest {

  /**
   * Tests getSettingFields
   */
  public function testGetSettingFields() {
    $settingFields = SettingsManager::getSettingFields();
    $this->assertArrayHasKey(SettingsManager::AUTH_KEY, $settingFields);
    $this->assertArrayHasKey(SettingsManager::USER_KEY, $settingFields);
    $this->assertArrayHasKey(SettingsManager::API_URL, $settingFields);
  }

  /**
   * @throws CiviCRM_API3_Exception
   */
  public function testGetSettingsValue() {
    $settingFields = SettingsManager::getSettingFields();
    $value  = [
      SettingsManager::API_URL => 'https://sample.com',
      SettingsManager::USER_KEY => 'ctiuserkey',
      SettingsManager::AUTH_KEY => 'ctiauthkey',
    ];
    $valuesToSave = array_intersect_key($value, $settingFields);
    $result = civicrm_api3('setting', 'create', $valuesToSave);
    $this->assertNotEmpty($result['values']);
    $settingsValues = SettingsManager::getSettingsValue();
    $this->assertEquals($value[SettingsManager::API_URL], $settingsValues[SettingsManager::API_URL]);
    $this->assertEquals($value[SettingsManager::USER_KEY], $settingsValues[SettingsManager::USER_KEY]);
    $this->assertEquals($value[SettingsManager::AUTH_KEY], $settingsValues[SettingsManager::AUTH_KEY]);
  }

}
