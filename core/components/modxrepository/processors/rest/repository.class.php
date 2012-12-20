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
        
        
        if(!$data = $this->getData()){
            exit;
            return false;
        }
        
        return $this->toXML($data, $this->params);
    }
    
    public function getData(){
        $url = $this->modx->getOption('site_url', null);
        $url  .= $this->modx->getOption('modxRepository.request_path', null).'package/';
        
        
        
        $repositories = array(
            array(
                'description'   => 'Desc',
                'templated'     => 0,
                'rank'          => 1,
                'packages'      => 3,
                'createdon'     => '2011-02-09T14:36:08Z',
                'name' =>   'Front End Templates',
                'id'    => '4d52a654b2b083055d000004',
            ),
            array(
                'description'   => 'Desc2',
                'templated'     => 1,
                'rank'          => 1,
                'packages'      => 3,
                'createdon'     => '2011-02-09T14:36:08Z',
                'name' =>   'Front 2',
                'id'    => '4d52a654b2b083055d011454',
            ),
        ); 
        
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
    }
    
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
