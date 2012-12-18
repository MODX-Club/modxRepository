<?php

/*
 * Проверяем обновления пакета
 */

require_once  dirname(dirname(__FILE__)).'/response.class.php';
class modxRepositoryPackageUpdate extends modxRepositoryResponse{
    var $root = '<packages/>';
    
    function process(){
        if(!$signature = $this->properties['signature']){
            $this->failure('Не была получена подпись пакета');
            return;
        }
        
        $updates = array();
        
        
        // Получаем список всех более новых версий пакета
        if(!$current = $this->modx->getObject('modResource', array(
            'pagetitle' => $signature,
            'published' => true,
            'deleted'   => false,
        ))){
            $this->failure('Не был получен текущий пакет');
            return;
        }    
        
        /*
         * Получаем все более новые пакеты
         */ 
        
        $response = $this->runProcessor('package/getpackages', array(
            'where' => array(
                'r.parent' => $current->parent,
                'r.id:!=' => $current->id,
                'r.createdon:>' => $current->createdon,
            ),
            'sort'  => array('r.publishedon, DESC'),
        ));
        
        if($result = $response->getResponse()){
            foreach($result as $r){
                $updates[] = array(
                    'package' => $this->preparePackageRow($r),
                );
            }
        }
        return $this->toXML($updates, array(
            'total' => ($result?count($result):0),
        ));
    }
    
    
    function preparePackageRow($data){
        $response = $this->runProcessor('package/preparerow',  $data);
        return $response->getResponse();
    }
}

return  'modxRepositoryPackageUpdate';
?>
