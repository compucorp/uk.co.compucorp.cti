<?php

use CRM_Cti_SettingsManager as SettingsManager;

/**
 * Class CRM_Cti_Hook_CTISync
 */
class CRM_Cti_Hook_Post_CTIRestSync extends CRM_Cti_Hook_Post_SyncParticipant {

  /**
   * CRM_Cti_Hook_CTISync constructor.
   * @param $objectId
   * @throws CiviCRM_API3_Exception
   */
  public function __construct($objectId) {
    parent::__construct($objectId);
  }

  /**
   * @param $data
   * @param $sessionId
   * @throws CiviCRM_API3_Exception
   */
  protected function callAPI($data, $sessionId) {
    $settings = SettingsManager::getSettingsValue();
    $url = $settings[SettingsManager::API_URL] . '/' . $sessionId . '/registrant';
    $header = [
      'Accept:application/json',
      'AUTHKEY:' . $settings[SettingsManager::AUTH_KEY],
      'USERKEY:' . $settings[SettingsManager::USER_KEY],
      'Content-Type:application/json',
    ];;
    set_time_limit(60);
    $connection = curl_init();
    $payload = json_encode($data);
    curl_setopt_array($connection, [
      CURLOPT_URL            => $url,
      CURLOPT_HTTPHEADER     => $header,
      CURLOPT_POST          => TRUE,
      CURLOPT_POSTFIELDS     => $payload,
      CURLOPT_RETURNTRANSFER => TRUE,
      CURLOPT_TIMEOUT        => 30,
    ]);
    $response = json_decode(curl_exec($connection), TRUE);
    $httpStatus = curl_getinfo($connection, CURLINFO_HTTP_CODE);
    $this->updateParticipantSyncStatus($httpStatus, $response);

    // Check that a connection was made
    if (curl_error($connection)) {
      $this->updateParticipantSyncStatus($httpStatus, $response);
    }
    curl_close($connection);
  }

}
