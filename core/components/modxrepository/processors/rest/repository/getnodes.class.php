<?php
/*
 * Главная страница. Выводит список новых и лучших пакетов
 */
require_once  dirname(dirname(__FILE__)).'/response.class.php';

class modxRepositoryHome extends modxRepositoryResponse{
    
    var $root = '<repository/>';
    var $repository_id = null;
    var $TVs = array();
    
    
    
    /*
     * Array
(
    [q] => extras/repository/4d52a654b2b083055d000004
    [api_key] => 
    [username] => 
    [uuid] => 694893a2-a7c4-4f38-8814-bd5b50423ef4
    [database] => mysql
    [revolution_version] => Revolution-2.2.6-pl
    [http_host] => modxstore.ru
    [supports] => Revolution-2.2.6-pl
    [repository_id] => 4d52a654b2b083055d000004
)

     */


    function process(){
        
        //  print '<pre>';
        /*
         * Получаем ID
         */
        if(!$this->repository_id = $this->properties['repository_id']){
            return $this->failure('Не был получен ID репозитория');
        }
        
        
        
        
        /*
         * Получаем текущий репозиторий
         */
        if(!$repositories = $this->getRepositories(array(
            'object_id.value' => $this->repository_id,
        ))){
            return $this->failure('Не был получен репозиторий');
        }
        
        if($this->hasErrors()){
            return false;
        }
        
        
        $repositoryObject = current($repositories);
        $repository = $this->prepareRepositoryRow( $repositoryObject->toArray());
        //print_r();
        
        /*
         * Собираем дочерние репы
         */
        $repositories = $this->getRepositories(array(
            "modResource.parent"    => $repositoryObject->id,
        ));
        if(!$this->hasErrors()){
            foreach($repositories as $r){
                $repository[] = array(
                    'tag'   => $this->prepareTagRow($r->toArray()),
                );
            } 
        }
        
        
        // print_r($repository);
        
        return $this->toXML($repository);
    }
    
    
    function prepareTagRow($data){
        /*
         * $data
         */
        return array(
            'id'    => $data['object_id'],
            'name'    => $data['pagetitle'],
            'packages'    => 0,
            'templated'    => !empty($data['templated']) ? $data['templated'] : 0,
        );
    }
    
    
    
    
    /*
     * Получаем информацию по репозиторию
     */
    function getRepositories($where = array(), $limit = 0){
        //print '<pre>';
        // print "sdfsd "; 
        $scriptProperties = array_merge($this->properties, array(
            'where' => $where,
            'limit' => $limit,
        ));
        
        $result = $this->runProcessor('repository/getrepository', $scriptProperties);
        
        return $result->getResponse();
    }
    
    
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
    
    
    public function process__(){
        
        // return $this->toArray(1);
        if(!$this->repository_id = $this->properties['repository_id']){
            return false;
        }
        // $data = $this->getData();
        
        $description = "Desc sdfsdfsd fsdf sdf sdfs";
        
        $tags = array(array(
            'id'        => '42237237b2b08320b3000025',
            'name'      => 'Admin',
            'packages'  => 23,
            'templated' =>  0,
        ),array(
            'id'        => '42237237b2b08320b3004457',
            'name'      => 'Blogging',
            'packages'  => 14,
            'templated' =>  1,
        ),);
        
        $result = array(
            'id'            => $this->repository_id,
            'name'          => 'dsfsdf',
            'description'   => "<![CDATA[{$description}]]>",
            'createdon'     => '2011-02-04 18:05:07 UTC',
            'rank'          => 0,
            'packages'      => 4,
            'templated' =>  0,
        );
        
        foreach($tags as $tag){
            $result[] = array(
                'tag'   => $tag,
            );
        }
            
        return $this->toXML($result);
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
