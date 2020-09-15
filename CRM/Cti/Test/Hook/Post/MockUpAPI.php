<?php

/**
 * Class CRM_Cti_Test_Hook_Post_TestSync
 */
class CRM_Cti_Test_Hook_Post_MockUpAPI extends CRM_Cti_Hook_Post_SyncParticipant {

  /**
   * CRM_Cti_Test_Hook_Post_TestSync constructor.
   * @param $objectId
   * @throws CiviCRM_API3_Exception
   */
  public function __construct($objectId) {
    parent::__construct($objectId);
  }

  /**
   * @param $data
   * @throws CiviCRM_API3_Exception
   */
  protected function callAPI($data) {
    $status = 200;
    $response = '{"Details": null,"Message":"Registration created","Reference": "4","ReferenceObject": null,"Severity": "Info"}';
    $this->updateParticipantSyncStatus($status, json_decode($response, TRUE));
  }

}
