<?php

/*
 * modxRepository by Fi1osof
 * http://community.modx-cms.ru/profile/Fi1osof/
 * http://modxstore.ru
 */

if($modx->context->key == 'mgr')  return;
if(!$modx->checkSiteStatus())   return;
if(!$request_path = $modx->getOption('modxRepository.request_path', $scriptProperties, false)){
    return;
}

$request = new modRequest($modx);

$resourceIdentifier = $request->getResourceIdentifier("alias");

/*
 * Check for repository path
 */

if(strpos($resourceIdentifier, $request_path) !== 0){
    return;
}

if(!$action = substr($resourceIdentifier, strlen($request_path))){
    return;
} 
// Get processors path
if(!$ns = $modx->getObject('modNamespace', 'modxrepository')){
    $modx->log(xPDO::LOG_LEVEL_ERROR, "Не было пролучено пространство имен modxrepository");
    return;
}
$processors_path = $ns->getCorePath().'processors/';

$options = array(
    'processors_path'   => $processors_path,
    'location'          => 'rest',
);

if (!isset($_POST)) $_POST = array();
if (!isset($_GET)) $_GET = array();
$scriptProperties = array_merge($_GET,$_POST, array(
    'handler_doc_id'   => $modx->getOption('modxRepository.handler_doc_id', null, false),
));

$actionArray = explode('/', $action);

if(count($actionArray) > 1){
    switch($actionArray[0]){
        case 'repository':;
            $action = 'repository/getnodes';
            $scriptProperties = array_merge($scriptProperties, array(
                'repository_id' => $actionArray[1],
            ));
            break;
        case 'download':
            $action = 'download/index';
            break;        
        default :;
    }
}

if(!$response = $modx->runProcessor($action, $scriptProperties, $options)){
    $modx->log(xPDO::LOG_LEVEL_ERROR, "Не было пролучено пространство имен modxrepository");
    return;
}

print $response->getResponse();
exit;