<?php

/*
 * @package modxRepository
 * @subpackage build
 * @author Fi1osof
 * http://community.modx-cms.ru/profile/Fi1osof/
 * http://modxstore.ru
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

 
return $settings;