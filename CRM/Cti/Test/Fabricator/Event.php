<?php

use CRM_Cti_Test_Fabricator_Base as BaseFabricator;

/**
 * Class CRM_Cti_Test_Fabricator_Event
 */
class CRM_Cti_Test_Fabricator_Event extends BaseFabricator {

  /**
   * Entity name.
   *
   * @var string
   */
  protected static $entityName = 'Event';

  /**
   * Fabricates an Event with given parameters.
   *
   * @param array $params
   *
   * @return mixed
   * @throws \CiviCRM_API3_Exception
   */
  public static function fabricate(array $params = []) {
    $params = array_merge(static::getDefaultParams(), $params);
    return parent::fabricate($params);
  }

  private static function getDefaultParams() {
    $now = new DateTime(date("Y-m-d") . " +7 days");
    $eventTypes = self::getEventTypes();
    return [
      'start_date' => $now->format('Y-m-d H:i:s'),
      'title' => 'Fabricated Event Title',
      'event_type_id' => $eventTypes[0]['value'],
    ];
  }

  private static function getEventTypes() {
    return civicrm_api3('OptionValue', 'get', [
      'sequential' => 1,
      'return' => ["label", "value"],
      'option_group_id' => "event_type",
    ])['values'];
  }

}
