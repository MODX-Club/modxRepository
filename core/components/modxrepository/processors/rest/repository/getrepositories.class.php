<?php

/*
 * Нельзя подгружать основной класс response
 */

class modxRepositoryGetRepositories extends modxRepositoryProcessor{
    
    var $TVs = array();
    
    function process(){
        
        /*
         * Получаем ID TV-шек
         */
        $this->getTVs();
        
        if($this->hasErrors()){
            return false;
        }
        
        $where = (array)$this->properties['where'];
        $limit = ($this->properties['limit'] ? $this->properties['limit'] : 0);
        return $this->getData($where, $limit);
    }
    
    
    function getTVs(){
        // Получаем ID TV-шек
        $TVsNames = array(
            'templated',
            'object_id'
        );
        
        // print '<pre>';
        if(!$result = $this->modx->getCollection('modTemplateVar', array(
            'name:IN'   => $TVsNames,
        ))){
            return $this->failure('Не были получены TV-параметры');
        }
        
        foreach($result as $r){
            $this->TVs[$r->name] = $r->id;
        }
        return $this->TVs;
    }
    
    function getData($where = array(), $limit = 0){
        
        /*
         * If root exists, get repositories parents
         */
        $parents = array();
        if($root = $this->getProperty('root' )){
            $q = $this->modx->newQuery('modResource');
            $q->select(array('modResource.id'));
            $q->where(array(
                'modResource.published' => true,
                'modResource.deleted' => false,
                'modResource.hidemenu' => false,
                'modResource.parent' => $root,
            ));
            if(!$q->prepare() OR !$q->stmt->execute() OR !$result = $q->stmt->FetchAll(PDO::FETCH_ASSOC)){
                $this->failure('Failure get repositories parents');
            }
            foreach($result as $r){
                $parents[] = $r['id'];
            }
        }
       
        
        /*
         * Get Repositories
         */
        $q = $this->modx->newQuery('modResource');
        $q->innerJoin('modTemplateVarResource', 'object_id', "object_id.contentid = modResource.id");
        $q->innerJoin('modTemplate', 'tpl', "tpl.id = modResource.template");
        $q->leftJoin('modTemplateVarResource', 'templated', "templated.contentid = modResource.id AND 'templated.tmplvarid'  = ". $this->TVs['templated']);
        
        $q->select(array(
            'modResource.*',
            'object_id.value as object_id',
            'templated.value as templated',
        ));
        
        $where = array_merge(array(
            'published' =>  1,
            'deleted'   => 0,
            'hidemenu'  => 0,
            'object_id.tmplvarid'  => $this->TVs['object_id'],
            'tpl.templatename'  => 'Repository',
        ), $where);
        
        if($parents){
            $where['parent:IN'] = $parents;
        }
        
        $q->where($where);
        
        $q->limit($limit);
         
        if(!$repositories = $this->modx->getCollection('modResource', $q)){
            $this->failure("Не были получены репозитории");
            return false;
        }
        
        return $repositories;
    }
}

return 'modxRepositoryGetRepositories';

?>
