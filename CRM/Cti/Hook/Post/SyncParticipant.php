<?php

use CRM_Cti_SettingsManager as SettingsManager;

/**
 * Class CRM_Cti_Hook_Post_SyncParticipant
 */
class CRM_Cti_Hook_Post_SyncParticipant {

  /**
   * @var mixed
   */
  private $participant;
  /**
   * @var mixed
   */
  private $event;
  /**
   * @var mixed
   */
  private $contact;
  /**
   * @var string
   */
  private $syncStatusField;
  /**
   * @var string
   */
  private $apiResponseField;
  /**
   * @var string
   */
  private $ctiSessionField;

  /**
   * CRM_Cti_Hook_Post_SyncParticipant constructor.
   * @param $objectId
   * @throws CiviCRM_API3_Exception
   */
  public function __construct($objectId) {
    $this->participant = civicrm_api3('Participant', 'get', [
      'sequential' => 1,
      'id' => $objectId,
      'api.Event.get' => [],
      'api.Contact.get' => [],
    ])['values'][0];
    $this->event = $this->participant['api.Event.get']['values'][0];
    $this->contact = $this->participant['api.Contact.get']['values'][0];
    $this->setCTISessionField();
    $this->setParticipantCustomFields();
  }

  /**
   * @param string $mode
   * @throws CiviCRM_API3_Exception
   */
  public function sync($mode = 'live') {
    $ctiSessionID = $this->event[$this->ctiSessionField];
    if (empty($ctiSessionID)) {
      return;
    }

    if (!array_key_exists($this->syncStatusField, $this->participant)) {
      return;
    }

    $syncStatus = $this->participant[$this->syncStatusField];
    if ($syncStatus != 'update' && is_null($syncStatus)) {
      return;
    }

    $data = [
      'BadgeNumber' => $this->contact['id'],
      'RegistrationCode' => 'REG,1,' . $ctiSessionID,
      'FirstName' => $this->contact['first_name'],
      'LastName' => $this->contact['last_name'],
      'EmailAddress' => $this->getPrimaryEmailAddressByContactId($this->contact['id']),
      'DisplayName' => $this->contact['display_name'],
    ];

    if ($mode == 'live') {
      $this->callCtiAPI($data);
    }
    else {
      $this->mockAPIResponse($data);
    }
  }

  /**
   * @param $data
   * @throws CiviCRM_API3_Exception
   */
  private function callCtiAPI($data) {
    $settings = SettingsManager::getSettingsValue();
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
      CURLOPT_URL            => $settings[SettingsManager::API_URL],
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
      //TODO: Log error if the connection was not made
    }
    curl_close($connection);
  }

  /**
   * @param $httpStatus
   * @param $response
   * @throws CiviCRM_API3_Exception
   */
  private function updateParticipantSyncStatus($httpStatus, $response) {
    civicrm_api3('Participant', 'create', [
      'id' => $this->participant['id'],
      'event_id' => $this->event['id'],
      'contact_id' => $this->contact['id'],
      $this->syncStatusField => $httpStatus,
      $this->apiResponseField => $response['Message'],
    ]);
  }

  /**
   * @throws CiviCRM_API3_Exception
   */
  private function setCTISessionField () {
    $id = civicrm_api3('CustomField', 'get', [
      'sequential' => 1,
      'custom_group_id' => "cti_events_platform_integration_event",
      'name' => "cti_session_id",
    ])['id'];

    $this->ctiSessionField = 'custom_' . $id;
  }

  /**
   * @throws CiviCRM_API3_Exception
   */
  private function setParticipantCustomFields() {
    $fields = civicrm_api3('CustomField', 'get', [
      'sequential' => 1,
      'custom_group_id' => "cti_events_platform_integration_participant",
    ])['values'];
    foreach ($fields as $field) {
      switch ($field) {
        case $field['name'] == 'sync_status':
          $this->syncStatusField = 'custom_' . $field['id'];
          break;

        case $field['name'] == 'api_response':
          $this->apiResponseField = 'custom_' . $field['id'];
          break;
      }
    }
  }

  /**
   * @param $contactId
   * @return mixed
   * @throws CiviCRM_API3_Exception
   */
  private function getPrimaryEmailAddressByContactId($contactId) {
    return civicrm_api3('Email', 'getsingle', [
      'sequential' => 1,
      'contact_id' => $contactId,
      'is_primary' => 1,
    ])['email'];
  }

  /**
   * This method is used for mocking API response for testing only
   *
   * @param $status
   * @throws CiviCRM_API3_Exception
   */
  private function mockAPIResponse () {
    $status = 200;
    $response = '{"Details": null,"Message":"Registration created","Reference": "4","ReferenceObject": null,"Severity": "Info"}';
    $this->updateParticipantSyncStatus($status, json_decode($response, TRUE));
  }

}
