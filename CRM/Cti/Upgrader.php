<?php

use CRM_Cti_Uninstall_DeleteInstalledCustomGroup as DeleteInstalledCustomGroups;

/**
 * Collection of upgrade steps.
 */
class CRM_Cti_Upgrader extends CRM_Cti_Upgrader_Base {

  /**
   * Run steps when the module is uninstalled.
   */
  public function uninstall() {
    $steps = [
      new DeleteInstalledCustomGroups(),
    ];

    foreach ($steps as $step) {
      $step->apply();
    }
  }

}
