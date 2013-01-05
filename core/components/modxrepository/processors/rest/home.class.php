<?php
/*
 * Главная страница. Выводит список новых и лучших пакетов
 */
require_once  dirname(__FILE__).'/response.class.php';

class modxRepositoryHome extends modxRepositoryResponse{
    
    var $root = '<home/>';
    
    public function process(){  
        $data = $this->getData();
         
        return $this->toXML($data);
    }
    
    public function getData(){
        $result = array( );
        
        $url = $this->modx->getOption('site_url', null); 
        $url  .= $this->modx->getOption('modxRepository.request_path', null).'package/';
        $result['url'] = $url;
        
        // Получаем новейшие пакеты
        if($newest = $this->getNewest()){
            foreach($newest as $n){
                $package = $this->preparePackageRow($n);
                $result[] = array(
                    'newest' => array(
                        'id' => $package['id'],
                        'name' => "{$package['name']} {$package['version']}-{$package['vrelease']} ",
                        'package_name' => $package['name'],
                        'releasedon' => $package['releasedon'],
                    ),
                );
            }
        }
        
        
        // Получаем самые популярные
        if($popular = $this->getPopular()){
            foreach($popular as $n){
                $package = $this->preparePackageRow($n);
                $result[] = array(
                    'topdownloaded' => array(
                        'id' => $package['id'],
                        'name' => $package['name'],
                        'downloads' => $package['downloads'],
                    ),
                );
            }
        }
        
        
        if(!empty($newest)) $result['packages'] = count($newest);
        return $result;
    }
    
    function getNewest(){ 
        $response = $this->runProcessor('package/getpackages', array(
            'where' => array(
            ),
            'sort'  => array('releasedon, DESC'),
            'group' => array('package_id'),
            'limit' => 10,
            'root'  => $this->getProperty('handler_doc_id'),
        ));
        
        if($result = $response->getResponse()){
            foreach($result as $r){
                $updates[] = array(
                    'package' => $this->preparePackageRow($r),
                );
            }
        }
        
        return $result;
    }
    
    function getPopular(){
        return $this->getNewest();
    }
    
    function preparePackageRow($data){
        $response = $this->runProcessor('package/preparerow',  $data);
        return $response->getResponse();
    }
}
return 'modxRepositoryHome';
?>
