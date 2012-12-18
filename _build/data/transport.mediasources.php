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
        "value" => "assets/components/modxrepository/packages/",
        "lexicon" => "core:source",
    ),
    "baseUrl" => Array
    (
        "name" => "baseUrl",
        "desc" => "prop_file.baseUrl_desc",
        "type" => "textfield",
        "options" => Array(),
        "value" => "assets/components/modxrepository/packages/",
        "lexicon" => "core:source",
    )
);

$mediaSource = $modx->newObject('sources.modMediaSource', array(
    'name' => 'Repository Packages',
    'class_key' => 'sources.modFileMediaSource',
    'description'   => 'Source for Repository packages',
    'properties' => $params,
));

$mediaSources[] = $mediaSource;
 

return $mediaSources;
        
?>
