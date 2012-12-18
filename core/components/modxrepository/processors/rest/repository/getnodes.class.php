<?php
/*
 * Главная страница. Выводит список новых и лучших пакетов
 */
require_once  dirname(dirname(__FILE__)).'/response.class.php';

class modxRepositoryHome extends modxRepositoryResponse{
    
    var $root = '<repository/>';
    var $repository_id = null;
    var $TVs = array(); 

    function process(){
          
        /*
         * Получаем ID
         */
        if(!$this->repository_id = $this->properties['repository_id']){
            return $this->failure('Не был получен ID репозитория');
        }
          
        /*
         * Получаем текущий репозиторий
         */
        if(!$repositories = $this->getRepositories(array(
            'object_id.value' => $this->repository_id,
        ))){
            return $this->failure('Не был получен репозиторий');
        }
        
        if($this->hasErrors()){
            return false;
        }
         
        $repositoryObject = current($repositories);
        $repository = $this->prepareRepositoryRow( $repositoryObject->toArray()); 
        
        /*
         * Собираем дочерние репы
         */
        $repositories = $this->getRepositories(array(
            "modResource.parent"    => $repositoryObject->id,
        ));
        if(!$this->hasErrors()){
            foreach($repositories as $r){
                $repository[] = array(
                    'tag'   => $this->prepareTagRow($r->toArray()),
                );
            } 
        }
         
        return $this->toXML($repository);
    }
    
    
    function prepareTagRow($data){
        /*
         * $data
         */
        return array(
            'id'    => $data['object_id'],
            'name'    => $data['pagetitle'],
            'packages'    => 0,
            'templated'    => !empty($data['templated']) ? $data['templated'] : 0,
        );
    }
     
    
    /*
     * Получаем информацию по репозиторию
     */
    function getRepositories($where = array(), $limit = 0){ 
        $scriptProperties = array_merge($this->properties, array(
            'where' => $where,
            'limit' => $limit,
        ));
        
        $result = $this->runProcessor('repository/getrepository', $scriptProperties);
        
        return $result->getResponse();
    }
    
    
    function prepareRepositoryRow($data){
        // print_r($data);
        
        return array(
            'description'   => "{$data['description']}>",
            'templated'     => !empty($data['templated']) ? $data['templated'] : 0,
            'rank'          => $data['menuindex'],
            'packages'      => 3,
            'createdon'     => $data['createdon'],
            'name' =>   $data['pagetitle'],
            'id'    => $data['object_id'],
        );
    }
    
    
 
}
return 'modxRepositoryHome';
?>
