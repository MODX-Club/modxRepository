<?php

/**
 * Loads system settings into build
 *
 * @package modextra
 * @subpackage build
 */
global  $modx, $sources;
$settings = array();

$settings['modxRepository.template'] = $modx->newObject('modSystemSetting');
$settings['modxRepository.template']->fromArray(array(
    'key' => 'modxRepository.template',
    'value' => 'default',
    'xtype' => 'textfield',
    'namespace' => 'modxrepository',
    'area' => 'site',
),'',true,true);



return $settings;