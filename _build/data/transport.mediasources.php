<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
$mediaSources = array();

$params = array(
    "basePath" => array(
        "name" => "basePath",
        "desc" => "prop_file.basePath_desc",
        "type" => "textfield",
        "options" => Array(),
        "value" => "core/components/modxrepository/controllers/",
        "lexicon" => "core:source",
    ),
    "baseUrl" => Array
    (
        "name" => "baseUrl",
        "desc" => "prop_file.baseUrl_desc",
        "type" => "textfield",
        "options" => Array(),
        "value" => "core/components/modxrepository/controllers/",
        "lexicon" => "core:source",
    )
);

$mediaSource = $modx->newObject('sources.modMediaSource', array(
    'name' => 'Repository Controllers',
    'class_key' => 'sources.modFileMediaSource',
    'description'   => 'Source for Repository Controllers controllers (modTemplate`s)',
    'properties' => $params,
));

$mediaSources[] = $mediaSource;


$params = array(
    "basePath" => array(
        "name" => "basePath",
        "desc" => "prop_file.basePath_desc",
        "type" => "textfield",
        "options" => Array(),
        "value" => "core/components/modxrepository/templates/",
        "lexicon" => "core:source",
    ),
    "baseUrl" => Array
    (
        "name" => "baseUrl",
        "desc" => "prop_file.baseUrl_desc",
        "type" => "textfield",
        "options" => Array(),
        "value" => "core/components/modxrepository/templates/",
        "lexicon" => "core:source",
    )
);

$mediaSource = $modx->newObject('sources.modMediaSource', array(
    'name' => 'Repository Templates',
    'class_key' => 'sources.modFileMediaSource',
    'description'   => 'Source for Repository Templates templates (Skins)',
    'properties' => $params,
));

$mediaSources[] = $mediaSource;

return $mediaSources;
        
?>
