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
        "value" => "core/components/modxsite/controllers/",
        "lexicon" => "core:source",
    ),
    "baseUrl" => Array
    (
        "name" => "baseUrl",
        "desc" => "prop_file.baseUrl_desc",
        "type" => "textfield",
        "options" => Array(),
        "value" => "core/components/modxsite/controllers/",
        "lexicon" => "core:source",
    )
);

$mediaSource = $modx->newObject('sources.modMediaSource', array(
    'name' => 'Controllers',
    'class_key' => 'sources.modFileMediaSource',
    'description'   => 'Source for site controllers (modTemplate`s)',
    'properties' => $params,
));

$mediaSources[] = $mediaSource;


$params = array(
    "basePath" => array(
        "name" => "basePath",
        "desc" => "prop_file.basePath_desc",
        "type" => "textfield",
        "options" => Array(),
        "value" => "core/components/modxsite/templates/",
        "lexicon" => "core:source",
    ),
    "baseUrl" => Array
    (
        "name" => "baseUrl",
        "desc" => "prop_file.baseUrl_desc",
        "type" => "textfield",
        "options" => Array(),
        "value" => "core/components/modxsite/templates/",
        "lexicon" => "core:source",
    )
);

$mediaSource = $modx->newObject('sources.modMediaSource', array(
    'name' => 'Templates',
    'class_key' => 'sources.modFileMediaSource',
    'description'   => 'Source for site templates (Site skins)',
    'properties' => $params,
));

$mediaSources[] = $mediaSource;

return $mediaSources;
        
?>
