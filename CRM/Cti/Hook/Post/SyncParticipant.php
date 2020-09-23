<?php

/**
 * Class CRM_Cti_Hook_Post_SyncParticipant
 */
abstract class CRM_Cti_Hook_Post_SyncParticipant {

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
   * @throws CiviCRM_API3_Exception
   */
  public function sync() {
    $ctiSessionID = $this->event[$this->ctiSessionField];
    if (empty($ctiSessionID)) {
      return;
    }
    //When the participant is registered via Webform or online registration form
    //the custom fields will not passed if the fields are not created in a profile / forms
    //therefore, custom fields will not exist here so $syncStatus should be assigned as empty
    if (!array_key_exists($this->syncStatusField, $this->participant)) {
      $syncStatus = '';
    }
    else {
      $syncStatus = $this->participant[$this->syncStatusField];
    }

    if ($syncStatus != 'update' && $syncStatus != '') {
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

    $this->callAPI($data, $ctiSessionID);

  }

  /**
   * @param $data
   * @param $ctiSessionID
   * @return mixed
   */
  abstract protected function callAPI($data, $ctiSessionID);

  /**
   * @param $httpStatus
   * @param $response
   * @throws CiviCRM_API3_Exception
   */
  protected function updateParticipantSyncStatus($httpStatus, $response) {
    switch ($httpStatus) {
      case 200:
      case 422:
      case 400:
        $syncStatus = $httpStatus;
        break;

      default:
        $syncStatus = 'other';
    }
    civicrm_api3('Participant', 'create', [
      'id' => $this->participant['id'],
      'event_id' => $this->event['id'],
      'contact_id' => $this->contact['id'],
      $this->syncStatusField => $syncStatus,
      $this->apiResponseField => $response,
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

}
