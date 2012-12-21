<?php
/*
 * Выводим данные по всем репозиториям для дерева разделов
 */
require_once  dirname(__FILE__).'/response.class.php';
 
class modxRepositoryRepository extends modxRepositoryResponse{
    var $params = array();
    var $root = '<repositories/>';
    var $parent = null;
    
    
    public function process(){
        
        if(!$this->parent = $this->modx->getOption('modxRepository.handler_doc_id', null, false)){
            return $this->failure('Не был получен ID раздела');
        }
        
        $data = $this->getData();
        
        return $this->toXML($data, $this->params);
    }
    
    function getData(){
        $result = array(); 
        $params = array_merge($this->getProperties(), array(
            'where' => array(
                'parent'    => $this->parent,
            )
        ));
        
        $response = $this->runProcessor('repository/getrepositories',$params );
        if(!$repositories = $response->getResponse()){
            $this->failure('Failure get repositories');
            return false;
        }
        foreach($repositories as $repository){
            $result[] = array(
                'repository' => $this->prepareRow($repository->toArray()),
            );
        } 
        $this->params = array(
            'type'  => 'array',
            'of'    => '1',
            'page'  => '1',
            'total' =>  count($repositories),
        );
         
        return $result;
    }
    
    /*public function getData__(){
        $url = $this->modx->getOption('site_url', null);
        $url  .= $this->modx->getOption('modxRepository.request_path', null).'package/';
         
        
        // Получаем ID TV-шек
        $TVsNames = array(
            'templated',
            'object_id'
        );
         
        if(!$result = $this->modx->getCollection('modTemplateVar', array(
            'name:IN'   => $TVsNames,
        ))){
            return $this->failure('Не были получены TV-параметры');
        }
         
        $TVs = array();
        
        foreach($result as $r){
            $TVs[$r->name] = $r->id;
        }
        
        $q = $this->modx->newQuery('modResource');
        $q->innerJoin('modTemplateVarResource', 'object_id', "object_id.contentid = modResource.id");
        $q->leftJoin('modTemplateVarResource', 'templated', 
            "templated.contentid = modResource.id AND templated.tmplvarid = ". $TVs['templated']);
        
        $q->select(array(
            'modResource.*',
            'templated.value as templated',
            'object_id.value as object_id',
        ));
        
        $where = array(
            'parent'    => $this->parent,
            'published' =>  1,
            'deleted'   => 0,
            'hidemenu'  => 0,
            'object_id.tmplvarid'  => $TVs['object_id'],
        );
        
        $q->where($where);
        
        if(!$repositories = $this->modx->getCollection('modResource', $q)){
            return $this->failure("Не были получены репозитории");
        }
        
        $result = array();
        foreach($repositories as $repository){
            $result[] = array(
                'repository' => $this->prepareRow($repository->toArray()),
            );
        } 
        
        $this->params = array(
            'type'  => 'array',
            'of'    => '1',
            'page'  => '1',
            'total' =>  count($repositories),
        );
         
        return $result;
    }*/
    
    function prepareRow($data){ 
        
        return array(
            'description'   => $data['description'],
            'templated'     => $data['templated'],
            'rank'          => $data['menuindex'],
            'packages'      => 3,
            'createdon'     => $data['createdon'],
            'name' =>   $data['pagetitle'],
            'id'    => $data['object_id'],
        );
    } 
}
return 'modxRepositoryRepository';
?>
