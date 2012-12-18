<?php
/*
 * Главная страница. Выводит список новых и лучших пакетов
 */
require_once  dirname(__FILE__).'/response.class.php';

class modxRepositoryHome extends modxRepositoryResponse{
    
    var $root = '<home/>';
    
    public function process(){
        // return $this->toArray(1);
        $data = $this->getData();
         
        return $this->toXML($data);
    }
    
    public function getData(){
        $result = array( );
        
        $url = $this->modx->getOption('site_url', null);
        // $url  .= $this->modx->getOption('modxRepository.request_path', null);
        $url  .= $this->modx->getOption('modxRepository.request_path', null).'package/';
        $result['url'] = $url;
        
        // Получаем новейшие пакеты
        $newest = $this->getNewest();
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
        
        // Получаем самые популярные
        $popular = $this->getPopular();
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
        
        $result['packages'] = count($newest);
        return $result;
    }
    
    function getNewest(){
        $response = $this->runProcessor('package/getpackages', array(
            'where' => array(
            ),
            'sort'  => array('r.publishedon, DESC'),
            'group' => array('package_id'),
            'limit' => 3,
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
    
    
    public function getData___(){
        $url = $this->modx->getOption('site_url', null);
        $url  .= $this->modx->getOption('modxRepository.request_path', null).'package/';
        
        $result = array(
            'packages' =>  0,
            'downloads' => 0,
            'url' => $url,
            array(
                'topdownloaded'  => array(
                array(
                    'id' => '4d556bc5b2b083396d0007e9',
                    'name' => 'sdfsdf',
                    'downloads' => 17,
                )),
            ),array(
                'topdownloaded'  => array(
                array(
                    'id' => 'sdfsdbc5b2b083396d0007e9',
                    'name' => 'sdfsdfdddd',
                    'downloads' => 26,
                )),
            ),
            array(
                'newest'  => array(
                array(
                    'id' => '4d556bc5b2b083396d0007e9',
                    'name' => 'EmptyAlias 1.0.0-beta1',
                    'package_name' => 'EmptyAlias',
                    'releasedon' => '2012-12-16 03:48:21 UTC',
                )),
            ),
            array(
                'newest'  => array(
                array(
                    'id' => 'asdasc5b2b083396d0007e9',
                    'name' => 'ClientConfig 1.0.0-beta1',
                    'package_name' => 'ClientConfig',
                    'releasedon' => '2012-12-16 03:48:21 UTC',
                )),
            ),
        );
        return $result;
    }
}
return 'modxRepositoryHome';
?>
