<?php

/**
 * Loads system settings into build
 *
 * @package modextra
 * @subpackage build
 */
$result = array();


/*
 * Templates
 */

$template = $modx->newObject('modTemplate', array(
    'templatename'  => 'Repository',
    'description'   => 'Template for repository',
    'content'       => '',
)); 
$result[] = $template;

$template = $modx->newObject('modTemplate', array(
    'templatename'  => 'Package',
    'description'   => 'Template for package',
    'content'       => '',
)); 
$result[] = $template;

$template = $modx->newObject('modTemplate', array(
    'templatename'  => 'Release',
    'description'   => 'Template for release',
    'content'       => '',
)); 

$result[] = $template;


/*
 * TemplateVars
 */

$TemplateVar = $modx->newObject('modTemplateVar', array(
    'name'              => 'object_id',
    'caption'           => 'Object ID',
    'description'       => '',
    'type'              => 'text',
    'input_properties'   => array(
        'allowBlank'   => false,
    ),
    'rank'              => 10,
)); 
$result[] = $TemplateVar;


$TemplateVar = $modx->newObject('modTemplateVar', array(
    'name'              => 'templated',
    'caption'           => 'Templated',
    'description'       => 'If true, will be thumbnaled packages preview',
    'type'              => 'listbox',
    'elements'          => '0||1',
    'default_text'      => '0',
    'input_properties'   => array(
        'allowBlank'    => false,
        'typeAhead'     => false,
        'typeAheadDelay'=> 250,
        'forceSelection'=> 250,
        'listEmptyText' => '',
    ),
    'rank'              => 20,
)); 
$result[] = $TemplateVar;


$TemplateVar = $modx->newObject('modTemplateVar', array(
    'name'              => 'version_major',
    'caption'           => 'Version major',
    'description'       => '',
    'type'              => 'number',
    'input_properties'   => array(
        'allowBlank'   => false,
    ),
    'rank'              => 30,
)); 
$result[] = $TemplateVar;

$TemplateVar = $modx->newObject('modTemplateVar', array(
    'name'              => 'version_minor',
    'caption'           => 'Version minor',
    'description'       => '',
    'type'              => 'number',
    'input_properties'   => array(
        'allowBlank'   => false,
    ),
    'rank'              => 40,
)); 
$result[] = $TemplateVar;

$TemplateVar = $modx->newObject('modTemplateVar', array(
    'name'              => 'version_patch',
    'caption'           => 'Version patch',
    'description'       => '',
    'type'              => 'number',
    'rank'              => 50,
)); 
$result[] = $TemplateVar;

$TemplateVar = $modx->newObject('modTemplateVar', array(
    'name'              => 'release',
    'caption'           => 'Release',
    'description'       => 'pl|beta|rc and etc.',
    'type'              => 'text',
    'rank'              => 60,
)); 
$result[] = $TemplateVar;

$TemplateVar = $modx->newObject('modTemplateVar', array(
    'name'              => 'vrelease_index',
    'caption'           => 'vRelease index',
    'description'       => '',
    'type'              => 'number',
    'rank'              => 70,
)); 
$result[] = $TemplateVar;

$TemplateVar = $modx->newObject('modTemplateVar', array(
    'name'              => 'file',
    'caption'           => 'Package file',
    'description'       => '',
    'type'              => 'file',
    'input_properties'   => array(
        'allowBlank'   => false,
    ),
    'rank'              => 80,
)); 
$result[] = $TemplateVar;

$TemplateVar = $modx->newObject('modTemplateVar', array(
    'name'              => 'r_description',
    'caption'           => 'Description',
    'description'       => '',
    'type'              => 'richtext',
    'rank'              => 90,
)); 
$result[] = $TemplateVar;

$TemplateVar = $modx->newObject('modTemplateVar', array(
    'name'              => 'instructions',
    'caption'           => 'Instructions',
    'description'       => '',
    'type'              => 'richtext',
    'rank'              => 100,
)); 
$result[] = $TemplateVar;

$TemplateVar = $modx->newObject('modTemplateVar', array(
    'name'              => 'changelog',
    'caption'           => 'Changelog',
    'description'       => '',
    'type'              => 'richtext',
    'rank'              => 110,
)); 
$result[] = $TemplateVar;




return $result;