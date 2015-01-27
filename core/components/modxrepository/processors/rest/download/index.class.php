<?php

/*
 * Получаем информацию о пакете для скачивания
 */

require_once  dirname(dirname(__FILE__)).'/response.class.php';

class modxRepositoryDownload extends modxRepositoryResponse{
    function process(){
        
        if($this->properties['getUrl'] == true){
            return $this->getFileUrl($this->properties['id']);
        }
        return;
    }
    
    function getFileUrl($id){
        if(empty($id)){
            $this->failure('Не был получен ID пакета');
            return;
        }
        
        
        $response = $this->runProcessor('package/getpackages', array(
            'where' => array(
                'r_object_id.value' => $id,
            ),
            'limit' => 1,
            'group' => array('package_id'),
            'sort'  => array('r.publishedon, DESC'),
        ));
        
        if(!$result = $response->getResponse()){
            $this->failure("Не был получен пакет");
            return;
        }
        
        $package = current($result);
        
        # $this->modx->log(1, print_r($id, 1));
        # $this->modx->log(1, print_r($package, 1));
        
        $url = $this->modx->getOption('site_url', null);
        
        $q = $this->modx->newQuery('modTemplateVar');
        $q->innerJoin('modTemplateVarResource', 'v', 'v.tmplvarid = modTemplateVar.id');
        $q->where(array(
            'v.id'  => $package['file_id'],
        ));
        $tv = $this->modx->getObject('modTemplateVar', $q);
        $package_url = $tv->renderOutput($package['r_content_id']);
        $package_url = preg_replace('/^\//','',$package_url);
        $url .= $package_url;
        
        
        // Count downloads
        if(
            !empty($package['r_content_id'])
            AND $resource = $this->modx->getObject('modResource', $package['r_content_id'])
        ){
            $count = (int)$resource->getTVValue('downloads');
            $resource->setTVValue('downloads', $count + 1);
        }
        
        return $url;
    }
}

return 'modxRepositoryDownload';
?>
