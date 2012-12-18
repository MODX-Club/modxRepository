<?php
if($modx->context->key == 'mgr')  return;
if(!$modx->checkSiteStatus())   return;
if(!$request_path = $modx->getOption('modxRepository.request_path', $scriptProperties, false)){
    //$modx->log(modX::LOG_LEVEL_ERROR,   'Не указан раздел для запросов');
    return;
}

/*if(!$handler_doc_id = $modx->getOption('modxRepository.handler_doc_id', $scriptProperties, false)){
    //$modx->log(modX::LOG_LEVEL_ERROR, 'Не указан ID документа-обработчика');
    return;
}*/



$request = new modRequest($modx);

$resourceIdentifier = $request->getResourceIdentifier("alias");
/*
 * Если это не раздел для запросов, пропускаем выполнение
 */

if(strpos($resourceIdentifier, $request_path) !== 0){
    return;
}

if(!$action = substr($resourceIdentifier, strlen($request_path))){
    //$modx->log(modX::LOG_LEVEL_ERROR,'Не было получено действие');
    return;
}


// Получаем путь до процессоров
if(!$ns = $modx->getObject('modNamespace', 'modxrepository')){
    $modx->log(xPDO::LOG_LEVEL_ERROR, "Не было пролучено пространство имен modxrepository");
    return;
}

if (!isset($_POST)) $_POST = array();
if (!isset($_GET)) $_GET = array();
$scriptProperties = array_merge($_GET,$_POST);

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

$processors_path = $ns->getCorePath().'processors/';


// print $processors_path;
if(!$response = $modx->runProcessor($action, $scriptProperties, array(
    'processors_path'   => $processors_path,
    'location'          => 'rest',
))){
    $modx->log(xPDO::LOG_LEVEL_ERROR, "Не было пролучено пространство имен modxrepository");
    return;
}



print $response->getResponse();
  
exit;