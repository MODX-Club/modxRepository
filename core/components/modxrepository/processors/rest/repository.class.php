<?php
/*
 * Выводим данные по всем репозиториям для дерева разделов
 */
require_once  dirname(__FILE__).'/response.class.php';

/*
 * <repository>
    <description>&lt;img src="http://modxcms.com/assets/images/icons/ico_tools.png" alt="Add-ons" class="left" /&gt; &lt;h3&gt;Want something not included in the MODx core install?&lt;/h3&gt;&lt;p&gt;Menu builders to image galleries, helper utilities to podcasting and everything in between. These add-ons can help you build a site to &lt;em&gt;exactly&lt;/em&gt; match your vision.&lt;/p&gt;</description>
    <templated type="integer">0</templated>
    <rank type="integer">0</rank>
    <packages type="integer">366</packages>
    <createdon type="datetime">2011-02-04T18:05:07Z</createdon>
    <name>Extras</name>
    <id>4d4c3fa6b2b0830da9000001</id>
  </repository>
  <repository>
    <description>&lt;img src="http://modxcms.com/assets/images/icons/ico_templates_frontend.png" alt="Front end templates" class="left" /&gt; &lt;h3&gt;Need a new look for your MODx site?&lt;/h3&gt;&lt;p&gt;These MODx templates should help you get started fast with a site that's just right for you. Keep them as-is or customize them to make them uniquely yours.&lt;/p&gt;</description>
    <templated type="integer">1</templated>
    <rank type="integer">1</rank>
    <packages type="integer">27</packages>
    <createdon type="datetime">2011-02-09T14:36:08Z</createdon>
    <name>Front End Templates</name>
    <id>4d52a654b2b083055d000004</id>
  </repository>
  <repository>
    <description>&lt;img src="http://modxcms.com/assets/images/icons/ico_screen_manager.png" alt="Manager Templates" class="right" /&gt;&lt;h3&gt;Looking for a different look for your CMS back-end manager?&lt;/h3&gt;&lt;p&gt;Front-end templates aren't the only ones laying around. Find a variety of manager skins and themes.&lt;/p&gt;</description>
    <templated type="integer">1</templated>
    <rank type="integer">2</rank>
    <packages type="integer">0</packages>
    <createdon type="datetime">2011-02-09T14:37:16Z</createdon>
    <name>Manager Templates</name>
    <id>4d52a684b2b083055d000005</id>
  </repository>
 */

/*
 * repositories type="array" total="3" of="1" page="1"
 */
class modxRepositoryRepository extends modxRepositoryResponse{
    var $params = array();
    var $root = '<repositories/>';
    var $parent = null;
    
    
    public function process(){
        
        if(!$this->parent = $this->modx->getOption('modxRepository.handler_doc_id', null, false)){
            return $this->failure('Не был получен ID раздела');
        }
        
        // return $this->toArray(1);
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
        
        // print '<pre>';
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
        
        
        // exit;
        return $result;
    }
    
    function prepareRow($data){
        // print_r($data);
        
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
    
    /*
     * public function getData(){
        $url = $this->modx->getOption('site_url', null);
        $url  .= $this->modx->getOption('modxRepository.request_path', null).'package/';
        
        $params = array(
            'type'  => 'array',
            'of'    => '1',
            'page'  => '1',
            'total' =>  count($data),
        );
        
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
        
        $result = array();
        foreach($repositories as $repository){
            $result[] = array(
                'repository' => $repository,
            );
        }
        
        return $result;
    }
     */
}
return 'modxRepositoryRepository';
?>
