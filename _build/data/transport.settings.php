<?php

/**
 * Loads system settings into build
 *
 * @package modextra
 * @subpackage build
 */
global  $modx, $sources;
$settings = array();

$settings['modxRepository.handler_doc_id'] = $modx->newObject('modSystemSetting');
$settings['modxRepository.handler_doc_id']->fromArray(array(
    'key' => 'modxRepository.handler_doc_id',
    'value' => '',
    'xtype' => 'textfield',
    'namespace' => 'modxrepository',
    'area' => 'site',
),'',true,true);


$settings['modxRepository.request_path'] = $modx->newObject('modSystemSetting');
$settings['modxRepository.request_path']->fromArray(array(
    'key' => 'modxRepository.request_path',
    'value' => 'extras/',
    'xtype' => 'textfield',
    'namespace' => 'modxrepository',
    'area' => 'site',
),'',true,true);


/*$settings['modxRepository.packages_path_url'] = $modx->newObject('modSystemSetting');
$settings['modxRepository.packages_path_url']->fromArray(array(
    'key' => 'modxRepository.packages_path_url',
    'value' => '{site_url}packages/',
    'xtype' => 'textfield',
    'namespace' => 'modxrepository',
    'area' => 'site',
),'',true,true);*/
 

 
return $settings;