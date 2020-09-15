<?php

use CRM_Cti_Test_Fabricator_Event as EventFabricator;
use CRM_Cti_Test_Fabricator_Participant as ParticipantFabricator;
use CRM_Cti_Test_Fabricator_Setting as SettingFabricator;

require_once __DIR__ . '/../../../../BaseHeadlessTest.php';

/**
 * Runs tests on SyncParticipant.
 *
 * @group headless
 */
class CRM_Cti_Hook_Post_SyncParticipantTest extends BaseHeadlessTest {

  public function setUp() {
    //Fabricate default settings
    SettingFabricator::fabricate();
  }

  /**
   * @throws CiviCRM_API3_Exception
   */
  public function testSync() {
    $ctiSessionIdFieldName = $this->getCTISessionFieldName();
    $event = EventFabricator::fabricate([
      $ctiSessionIdFieldName => 'test_session_id',
    ]);

    $syncStatusCustomField = $this->getSyncStatusCustomField();
    $participant = ParticipantFabricator::fabricate([
      'event_id' => $event['id'],
      $syncStatusCustomField => '',
    ]);

    $postSyncParticipant = new CRM_Cti_Test_Hook_Post_MockUpAPI($participant['id']);
    $postSyncParticipant->sync();

    $updatedParticipant = civicrm_api3('Participant', 'getsingle', [
      'id' => $participant['id'],
    ]);

    $this->assertEquals(200, $updatedParticipant[$syncStatusCustomField]);
  }

  /**
   * @return string
   * @throws CiviCRM_API3_Exception
   */
  private function getCTISessionFieldName () {
    $id = civicrm_api3('CustomField', 'get', [
      'sequential' => 1,
      'custom_group_id' => "cti_events_platform_integration_event",
      'name' => "cti_session_id",
    ])['id'];
    return 'custom_' . $id;
  }

  /**
   * @return string
   * @throws CiviCRM_API3_Exception
   */
  private function getSyncStatusCustomField() {
    $id = civicrm_api3('CustomField', 'get', [
      'sequential' => 1,
      'custom_group_id' => 'cti_events_platform_integration_participant',
      'name' => 'sync_status',
    ])['id'];
    return 'custom_' . $id;
  }

}
