<?php
/*
 * Главная страница. Выводит список новых и лучших пакетов
 */
require_once  dirname(__FILE__).'/response.class.php';

class modxRepositoryHome extends modxRepositoryResponse{
    
    var $root = '<packages/>';
    var $category_id = null;
    var $params = array();
    
/*
 * Array
(
    [q] => extras/package
    [api_key] => 
    [username] => 
    [uuid] => 694893a2-a7c4-4f38-8814-bd5b50423ef4
    [database] => mysql
    [revolution_version] => Revolution-2.2.6-pl
    [http_host] => modxstore.ru
    [page] => 0
    [supports] => Revolution-2.2.6-pl
    [sorter] => 0
    [tag] => 42237237b2b08320b3004457
)
 */     
    
    public function process(){
        
        
        // return $this->toArray(1);
        if($this->category_id = $this->properties['tag']){
            $result = $this->getPackagesByTag();
        }
        else if($signature = $this->properties['signature']){
            $this->root = '<package/>';
            $result = $this->getPackageBySignature($signature);
        }
        else if($query = $this->properties['query']){
            $result = $this->getPackagesByQuery($query);
        }
         
        
        return $this->toXML($result, $this->params);
    }
    
    /*
     * Поиск в репозитории
     */
    
    function getPackagesByTag(){
        // $description = "Desc sdfsdfsd fsdf sdf sdfs";
         
        
        $scriptProperties = array_merge($this->properties, array(
            'where' => array(
                'object_id.value'   => $this->properties['tag'],
            ),
            'limit' => 0,
        ));
        $response = $this->runProcessor('repository/getrepository', $scriptProperties);
        
          
        if(!$repository = current($response->getResponse())){
            $this->failure("Не были получены данные репозитория");
            return false;
        }
        
        $repository = $repository->toArray();
        
        
        $resultObject = $this->prepareRepositoryRow($repository);
        
        // Получаем все пакеты от этого репозитория
        $scriptProperties = array_merge($this->properties, array(
            'where' => array(
                'modResource.parent'   => $repository['id'],
            ),
            'limit' => 0,
            'group' => array('package_id'),
            'sort'  => array('r.publishedon, DESC'),
        ));
        $response = $this->runProcessor('package/getpackages', $scriptProperties);
         
        if($packagesArray = $response->getResponse()){
            $this->fetchPackages($resultObject, $packagesArray);
        }
        
        
        
        return $resultObject;
    }
    
    /*
     * Поиск в репозитории по запросу
     */
    
    function getPackagesByQuery($query){
        $scriptProperties = array_merge($this->properties, array(
            'where' => array(
                'modResource.pagetitle:like'   => "%{$query}%",
            ),
            'limit' => 0,
        ));
        
        $response = $this->runProcessor('package/getpackages', $scriptProperties);
         
        if($packagesArray = $response->getResponse()){
            $this->fetchPackages($resultObject, $packagesArray);
        }
        
        return $resultObject;
    }
    
    
    function ___(){
        $packages = array(array(
            'id'        => '42237237b2b08320b31545',    // ID конкретного пакета
            'package'   => '4d556c11b2b08339623443',    // ID самого пакета (без учета версии)
            'display_name' => 'quip-2.3.2-pl',
            'name'  => 'Quip',
            'version' =>  '2.3.2',
            'version_major' => 2,
            'version_minor' => 3,
            'version_patch' => 2,
            'release' => 'pl',
            'vrelease' => 'pl',
            'vrelease_index' => '',
            'author'    => '',
            'description'   => "<![CDATA[dfgdfgdfgdfg]]>",
            'instructions'  => "<![CDATA[dfgdfgdfgdfg]]>",
            'changelog'  => "<![CDATA[dfgdfgdfgdfg]]>",
            'createdon'    => '2012-08-23 14:42:30',
            'createdby'    => 'splittingred',
            'editedon'    => '2012-12-17 00:26:19',
            'releasedon'    => '2012-08-23 14:42:30',
            'downloads'    => 28659,
            'approved'    => true,
            'audited'    => true,
            'featured'    => true,
            'deprecated'    => false,
            'license'    => '',         //GPLv2
            'smf_url'    => '',
            'repository'    => '4d4c3fa6b2b0830da9000001',      // ???
            'supports'    => '2.2',
            'location'    => 'http://modx.com/extras/download/?id=506a5a7cf2455450a9000044',
            'signature'    => 'quip-2.3.2-pl',
            'supports_db'    => 'mysql',
            'minimum_supports'    => '2.2',
            'breaks_at'    => '10000000.0',
            'screenshot'    => '',
        ),);
        
        $result = array(
            'id'            => $this->category_id,
            'name'          => 'dsfsdf',
            'description'   => "<![CDATA[{$description}]]>",
            'createdon'     => '2011-02-04 18:05:07 UTC',
            'rank'          => 0,
            'packages'      => 4,
            'templated' =>  0,
        );
        
        foreach($packages as $package){
            $result[] = array(
                'package'   => $package,
            );
        }
        
        $this->params = array(
            'total' => 2,
            'of'    => 1,
            'page'  => 1,
        );
        return $result;
    }
    
    
    function fetchPackages(& $resultObject, & $packagesArray){
        foreach($packagesArray as $p){
            $resultObject[] = array(
                // 'package' => $this->preparePackageRow($p->toArray()),
                'package' => $this->preparePackageRow($p),
            );
        }
    }
    
    function preparePackageRow($data){
        $response = $this->runProcessor('package/preparerow',  $data);
        return $response->getResponse();
    }
    
    /*
     * function preparePackageRow($data){
        
        
        $varsArray = array();
        $vers = array(
            'version_major',
            'version_minor',
            'version_patch'
        );
        foreach($vers as $v){
            if(isset($data[$v]))$varsArray[] = $data[$v];
        }
        $version = implode(".", $varsArray);
        
        $vrelease  = $data['release']. ($data['vrelease_index']  ? "-{$data['vrelease_index']}" :  "");
        
        
        return array(
            'id'    => $data['release_id'],
            'package'    => $data['object_id'],
            'display_name'    => $data['release_name'],
            'name'    => $data['pagetitle'],
            'version'    => $version,
            'version_major'    => $varsArray[0],
            'version_minor'    => $varsArray[1],
            'version_patch'    => $varsArray[2],
            'release'    => $data['release'],
            'vrelease'    => $vrelease,
            'vrelease_index'    => $data['vrelease_index'],
            'author'    => $data['author'],
            'description'    => "<![CDATA[{$data['release_description']}]]>",
            'instructions'    => "<![CDATA[{$data['instructions']}]]>",
            'changelog'    => "<![CDATA[{$data['changelog']}]]>",
            'createdon'    => date('Y-m-d H:i:s', $data['release_createdon']),
            'createdby'    => $data['author'],
            'editedon'    => date('Y-m-d H:i:s', $data['release_editedon']),
            'approved'    => 1,
            'audited'    => 1,
            'featured'    => 1,
            'deprecated'    => '',
            'license'    => '',
            'smf_url'    => '',
            'repository'    => '',
            'supports'    => '',
            'supports'    => '2.0',
            'location'    => $this->modx->getOption('site_url'). $this->modx->getOption('modxRepository.request_path')."download/?id={$data['release_id']}",
            'signature'    => $data['release_name'],
            'supports_db'    => 'mysql',        
            'minimum_supports'    => '2.0', 
            'breaks_at'    => 10000000.0,          
            'screenshot'    => $data['screenshot'],
        );
    }
     */
    
    
    function prepareRepositoryRow($data){
        // print_r($data);
        
        return array(
            'description'   => "<![CDATA[{$data['description']}]]>",
            'templated'     => !empty($data['templated']) ? $data['templated'] : 0,
            'rank'          => $data['menuindex'],
            'packages'      => 3,
            'createdon'     => $data['createdon'],
            'name' =>   $data['pagetitle'],
            'id'    => $data['object_id'],
        );
    }
    
    /*
     * Поиск пакета по подписи
     */
    
    function getPackageBySignature($signature){
        // print '<pre>';
        $scriptProperties = array_merge($this->properties, array(
            'where' => array(
                'r.pagetitle'   => $this->properties['signature'],
            ),
            'limit' => 1,
            'group' => array('package_id'),
            'sort'  => array('r.publishedon, DESC'),
        ));
        $response = $this->runProcessor('package/getpackages', $scriptProperties);
         
        /*if($packagesArray = $response->getResponse()){
            $this->fetchPackages($resultObject, $packagesArray);
        }*/
        
        if(!$result = current($response->getResponse())){
            $this->failure("Не был получен пакет");
            return;
        }
        
        //  $packArray = $result->toArray();
        $packArray = $result;
        $package = $this->preparePackageRow($packArray);
        
        //  print_r($r);
        
        // Получаем данные о файле
        // print_r($result->toArray());
        $url = $this->modx->getOption('site_url', null);
        $url  .= $this->modx->getOption('modxRepository.request_path', null).'download/?id=';
        
        $fileData =  array(
            'id'    => $packArray['file_id'],
            'version'    => $packArray['file_id'],
            'filename'    => $packArray['file'].".transport.zip",
            'downloads'    => 3,
            'lastip'    => '',
            'transport' =>  true,
            'location'  =>  $url.$packArray['file_id'],
        );
        
        $package[] = array(
            'file'  => $fileData,
        );
        
        return $package;
        exit;
        return array(
            'id'        => '42237237b2b08320b31545',    // ID конкретного пакета
            'package'   => '4d556c11b2b08339623443',    // ID самого пакета (без учета версии)
            'display_name' => 'quip-2.3.2-pl',
            'name'  => 'Quip',
            'version' =>  '2.3.2',
            'version_major' => 2,
            'version_minor' => 3,
            'version_patch' => 2,
            'release' => 'pl',
            'vrelease' => 'pl',
            'vrelease_index' => '',
            'author'    => 'splittingred',
            'description'   => "<![CDATA[dfgdfgdfgdfg]]>",
            'instructions'  => "<![CDATA[dfgdfgdfgdfg]]>",
            'changelog'  => "<![CDATA[dfgdfgdfgdfg]]>",
            'createdon'    => '2012-08-23 14:42:30',
            'createdby'    => 'splittingred',
            'editedon'    => '2012-12-17 00:26:19',
            'releasedon'    => '2012-08-23 14:42:30',
            'downloads'    => 28659,
            'approved'    => true,
            'audited'    => true,
            'featured'    => true,
            'deprecated'    => false,
            'license'    => '',         //GPLv2
            'smf_url'    => '',
            'repository'    => '4d4c3fa6b2b0830da9000001',      // ???
            'supports'    => '2.2',
            'location'    => 'http://modx.com/extras/download/?id=506a5a7cf2455450a9000044',
            'signature'    => 'quip-2.3.2-pl',
            'supports_db'    => 'mysql',
            'minimum_supports'    => '2.2',
            'breaks_at'    => '10000000.0',
            'screenshot'    => '',
            'file'      => array(
                'id'        => '4d923df7f245540cfb000049',
                'version'   => '4d923df5f245540cfb000047',
                'filename'  => 'quip-2.3.2-pl.transport.zip',
                'downloads' => 154,
                'lastip'    => '',
                'transport' => true,
                'location'  => 'http://modx.com/extras/download/?id=4d923df7f245540cfb000049',  // URL + id
                'package-signature' => $signature,
            ),
        );
    }
    
    public function getData(){
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
