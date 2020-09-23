<?php

use CRM_Cti_SettingsManager as SettingsManager;

/*
 * Settings metadata file
 */
return [
  SettingsManager::API_URL => [
    'group_name' => SettingsManager::GROUP_NAME,
    'group' => SettingsManager::GROUP,
    'name' => SettingsManager::API_URL,
    'title' => 'API URL',
    'type' => 'String',
    'html_type' => 'text',
    'quick_form_type' => 'Element',
    'default' => '',
    'is_help' => FALSE,
    'is_required' => TRUE,
    'html_attributes' => ['size' => 50],
    'extra_data' => '',
  ],
  SettingsManager::AUTH_KEY => [
    'group_name' => SettingsManager::GROUP_NAME,
    'group' => SettingsManager::GROUP,
    'name' => SettingsManager::AUTH_KEY,
    'title' => 'API AUTH KEY',
    'type' => 'String',
    'html_type' => 'text',
    'quick_form_type' => 'Element',
    'default' => '',
    'is_help' => FALSE,
    'is_required' => TRUE,
    'html_attributes' => ['size' => 50],
    'extra_data' => '',
  ],
  SettingsManager::USER_KEY => [
    'group_name' => SettingsManager::GROUP_NAME,
    'group' => SettingsManager::GROUP,
    'name' => SettingsManager::USER_KEY,
    'title' => 'API USER KEY',
    'type' => 'String',
    'html_type' => 'text',
    'quick_form_type' => 'Element',
    'default' => '',
    'is_help' => FALSE,
    'is_required' => TRUE,
    'html_attributes' => ['size' => 50],
    'extra_data' => '',
  ],
];
