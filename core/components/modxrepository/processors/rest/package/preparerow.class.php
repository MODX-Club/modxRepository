<?php
/*
 * Формируем строку пакета
 */

class modxRepositoryPackagePrepareRow extends modProcessor{
    function process(){
        return $this->preparePackageRow($this->properties);
    }
    
    function preparePackageRow($data){  
        $varsArray = array();
        $vers = array(
            'version_major',
            'version_minor',
            'version_patch'
        );
        foreach($vers as $v){
            if(isset($data[$v]))$varsArray[] = ($data[$v] ? $data[$v] : '0');
        }
        $version = implode(".", $varsArray);
        
        $vrelease  = $data['release']. ($data['vrelease_index']  ? "-{$data['vrelease_index']}" :  "");
         
        return array(
            'id'    => $data['release_id'],
            'r_content_id'    => $data['r_content_id'],
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
            'description'    => "{$data['release_description']}",
            'instructions'    => "{$data['instructions']}",
            'changelog'    => "{$data['changelog']}",
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
            'releasedon'    => date('Y-m-d H:i:s', $data['release_createdon']),
            'downloads'     => isset($data['downloads']) ? $data['downloads'] : 0,
        );
    }
}

return 'modxRepositoryPackagePrepareRow';
?>
