<?php

use CRM_Cti_Test_Fabricator_Base as BaseFabricator;

/**
 * Class CRM_Cti_Test_Fabricator_Participant
 */
class CRM_Cti_Test_Fabricator_Participant extends BaseFabricator {


  /**
   * Entity name.
   *
   * @var string
   */
  protected static $entityName = 'Participant';

  /**
   * Fabricates a Participant with given parameters.
   *
   * @param array $params
   *
   * @return mixed
   * @throws \CiviCRM_API3_Exception
   */
  public static function fabricate(array $params = []) {
    if (!isset($params['event_id'])) {
      $event = CRM_Cti_Test_Fabricator_Event::fabricate();
      $params['event'] = $event['id'];
    }
    if (!isset($params['contact_id'])) {
      $event = CRM_Cti_Test_Fabricator_Contact::fabricateWithEmail();
      $params['contact_id'] = $event['id'];
    }
    return parent::fabricate($params);
  }

}
