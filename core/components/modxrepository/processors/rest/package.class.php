<?php
/*
 * Главная страница. Выводит список новых и лучших пакетов
 */
require_once  dirname(__FILE__).'/response.class.php';

class modxRepositoryHome extends modxRepositoryResponse{
    
    var $root = '<packages/>';
    var $category_id = null;
    var $params = array();
      
    
    public function process(){
        
        
        // return $this->toArray(1);
        if($this->category_id = $this->properties['tag']){
            $result = $this->getPackagesByTag();
        }
        else if($signature = $this->properties['signature']){
            $this->root = '<package/>';
            $result = $this->getPackageBySignature($signature);
        }
        else if($query = $this->properties['query']){
            $result = $this->getPackagesByQuery($query);
        }
         
        
        return $this->toXML($result, $this->params);
    }
    
    /*
     * Поиск в репозитории
     */
    
    function getPackagesByTag(){
        // $description = "Desc";
        
        $scriptProperties = array_merge($this->properties, array(
            'where' => array(
                'object_id.value'   => $this->properties['tag'],
            ),
            'limit' => 0,
        ));
        $response = $this->runProcessor('repository/getrepository', $scriptProperties);
        
          
        if(!$repository = current($response->getResponse())){
            $this->failure("Не были получены данные репозитория");
            return false;
        }
        
        $repository = $repository->toArray();
        
        
        $resultObject = $this->prepareRepositoryRow($repository);
        
        // Получаем все пакеты от этого репозитория
        $scriptProperties = array_merge($this->properties, array(
            'where' => array(
                'modResource.parent'   => $repository['id'],
            ),
            'limit' => 0,
            'group' => array('package_id'),
            'sort'  => array('r.publishedon, DESC'),
        ));
        $response = $this->runProcessor('package/getpackages', $scriptProperties);
         
        if($packagesArray = $response->getResponse()){
            $this->fetchPackages($resultObject, $packagesArray);
        }
        
        
        
        return $resultObject;
    }
    
    /*
     * Поиск в репозитории по запросу
     */
    
    function getPackagesByQuery($query){
        $scriptProperties = array_merge($this->properties, array(
            'where' => array(
                'modResource.pagetitle:like'   => "%{$query}%",
            ),
            'limit' => 0,
        ));
        
        $response = $this->runProcessor('package/getpackages', $scriptProperties);
         
        if($packagesArray = $response->getResponse()){
            $this->fetchPackages($resultObject, $packagesArray);
        }
        
        return $resultObject;
    }
    
    
    
    function fetchPackages(& $resultObject, & $packagesArray){
        foreach($packagesArray as $p){
            $resultObject[] = array(
                // 'package' => $this->preparePackageRow($p->toArray()),
                'package' => $this->preparePackageRow($p),
            );
        }
    }
    
    function preparePackageRow($data){
        $response = $this->runProcessor('package/preparerow',  $data);
        return $response->getResponse();
    }
    
  
    function prepareRepositoryRow($data){ 
        return array(
            'description'   => "{$data['description']}",
            'templated'     => !empty($data['templated']) ? $data['templated'] : 0,
            'rank'          => $data['menuindex'],
            'packages'      => 3,
            'createdon'     => $data['createdon'],
            'name' =>   $data['pagetitle'],
            'id'    => $data['object_id'],
        );
    }
    
    /*
     * Поиск пакета по подписи
     */
    
    function getPackageBySignature($signature){ 
        $scriptProperties = array_merge($this->properties, array(
            'where' => array(
                'r.pagetitle'   => $this->properties['signature'],
            ),
            'limit' => 1,
            'group' => array('package_id'),
            'sort'  => array('r.publishedon, DESC'),
        ));
        $response = $this->runProcessor('package/getpackages', $scriptProperties);
        
        if(!$r = $response->getResponse() OR !is_array($r) OR !$result = current($r)){
            $this->failure("Не был получен пакет");
            return;
        }
        
        
        $packArray = $result;
        $package = $this->preparePackageRow($packArray);
         
        // Получаем данные о файле 
        $url = $this->modx->getOption('site_url', null);
        $url  .= $this->modx->getOption('modxRepository.request_path', null).'download/?id=';
        
        $fileData =  array(
            'id'    => $packArray['file_id'],
            'version'    => $packArray['file_id'],
            'filename'    => $packArray['file'].".transport.zip",
            'downloads'    => 3,
            'lastip'    => '',
            'transport' =>  true,
            'location'  =>  $url.$packArray['file_id'],
        );
        
        $package[] = array(
            'file'  => $fileData,
        );
        
        return $package; 
    } 
}
return 'modxRepositoryHome';
?>
