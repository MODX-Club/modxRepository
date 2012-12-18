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
        
        //  $package = current($result)->toArray();
        $package = current($result);
         
        $url = $this->modx->getOption('modxRepository.packages_path_url', null).$package['file'];
        return $url;
    }
}

return 'modxRepositoryDownload';
?>
