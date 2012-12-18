<?php

/*
 * Нельзя подгружать основной класс response
 */

class modxRepositoryGetRepository extends modProcessor{
    
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
        
        $q = $this->modx->newQuery('modResource');
        $q->innerJoin('modTemplateVarResource', 'object_id', "object_id.contentid = modResource.id");
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
        ), $where);
        
        $q->where($where);
        
        $q->limit($limit);
         
        if(!$repositories = $this->modx->getCollection('modResource', $q)){
            $this->failure("Не были получены репозитории");
            return false;
        }
        
        return $repositories;
    }
}

return 'modxRepositoryGetRepository';

?>
