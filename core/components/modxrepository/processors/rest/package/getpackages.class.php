<?php
/*
 * Получаем массив всех пакетов
 */
class modxRepositoryGetPackagesClass  extends modxRepositoryProcessor{
    
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
        $group = (array)$this->properties['group'];
        $sort = (array)$this->properties['sort'];
        return $this->getData($where, $limit, $group, $sort);
    }
    
    
    function getTVs(){
        // Получаем ID TV-шек
        $TVsNames = array(
            'object_id',
            'version_major',
            'version_minor',
            'version_patch',
            'release',
            'vrelease_index',
            'r_description',
            'instructions',
            'changelog',
            'file',
        );
        
        
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
    
    function getData($where = array(), $limit = 0, $group = array(), $sort = array()){
        /*
         * Get repositories IDs
         */
        $parents = array();
        
        if($this->getProperty('root')){
            $response = $this->runProcessor('repository/getrepositories', $this->getProperties());
            if(!$repositories = $response->getResponse()){
                $this->failure('Failure get repositories');
                return false;
            }
            foreach($repositories  as $r){
                $parents[] = $r->id;
            }
        }
        
        $q = $this->modx->newQuery('modResource');
        $q->innerJoin('modTemplateVarResource', 'object_id', "object_id.contentid = modResource.id");
        $q->innerJoin('modResource', 'r', 'r.parent = modResource.id');
        $q->innerJoin('modTemplateVarResource', 'r_object_id', "r_object_id.contentid = r.id");
        $q->innerJoin('modTemplateVarResource', '`release`', "`release`.contentid = r.id");
        $q->innerJoin('modTemplateVarResource', '`file`', "`file`.contentid = r.id");
        $q->innerJoin('modUser', '`user`', "`user`.id = r.createdby");
        
        $q->leftJoin('modTemplateVarResource', 'vrelease_index', 
                "vrelease_index.contentid = r.id AND vrelease_index.tmplvarid  = ". $this->TVs['vrelease_index']);
        $q->leftJoin('modTemplateVarResource', 'r_description', 
                "r_description.contentid = r.id AND r_description.tmplvarid  = ". $this->TVs['r_description']);
        $q->leftJoin('modTemplateVarResource', 'instructions', 
                "instructions.contentid = r.id AND instructions.tmplvarid  = ". $this->TVs['instructions']);
        $q->leftJoin('modTemplateVarResource', 'changelog', 
                "changelog.contentid = r.id AND changelog.tmplvarid  = ". $this->TVs['changelog']);
        
        $q->leftJoin('modTemplateVarResource', 'version_major', 
                "version_major.contentid = r.id AND version_major.tmplvarid = ". $this->TVs['version_major']);
        $q->leftJoin('modTemplateVarResource', 'version_minor', 
                "version_minor.contentid = r.id AND version_minor.tmplvarid = ". $this->TVs['version_minor']);
        $q->leftJoin('modTemplateVarResource', 'version_patch', 
                "version_patch.contentid = r.id AND version_patch.tmplvarid = ". $this->TVs['version_patch']);
        
        $q->select(array(
            'modResource.*',
            'modResource.id as package_id',
            'object_id.value as object_id',
            'r_object_id.value as release_id',
            'r.id as r_content_id',
            'r.pagetitle as release_name',
            'r.createdon as release_createdon',
            'r.editedon as release_editedon',
            'r.publishedon as releasedon',
            'version_major.value as version_major',
            'version_minor.value as version_minor',
            'version_patch.value as version_patch',
            '`release`.value as `release`',
            'vrelease_index.value as vrelease_index',
            '`user`.username as `author`',
            '`user`.username as `r_createdby`',
            'r_description.value as release_description',
            'instructions.value as instructions',
            'changelog.value as changelog',
            'file.value as file',
            'file.id as file_id',
        ));
        
        
        $where = array_merge(array(
            'modResource.published' =>  1,
            'modResource.deleted'   => 0,
            'modResource.hidemenu'  => 0,
            'r.published' =>  1,
            'r.deleted'   => 0,
            'r.hidemenu'  => 0,
            'object_id.tmplvarid'  => $this->TVs['object_id'],
            'r_object_id.tmplvarid'  => $this->TVs['object_id'],
            '`release`.tmplvarid'  => $this->TVs['release'],
            'file.tmplvarid'  => $this->TVs['file'],
        ), $where );
        
        if($parents){
            $where['modResource.parent:IN'] =  $parents;
        }
        
        $q->where($where);
        $q->limit($limit);
        
        if($sort){
            foreach($sort as $s){
                $arr = explode(",",  $s);
                $by = trim($arr[0]);
                if(!$dir = trim($arr[1])){
                    $dir = 'ASC';
                }
                $q->sortby($by, $dir);
            }
        }
        
        $q->prepare();
        
        
        // Группируем результат
        if($group = (array)$group){
            $sql = $q->toSQL();
        
            $sql = "SELECT * from ({$sql}) AS t";
            $sql .= " group by ". implode(", ", $group);
            
            if($sort){
                $order_arr = array();
                foreach($sort as $s){
                    $arr = explode(",",  $s);
                    $by = trim($arr[0]);
                    if(!$dir = trim($arr[1])){
                        $dir = 'ASC';
                    }
                    // remove aliases
                    $by_arr = explode('.', $by);
                    if($by_arr[1]){
                        $by = $by_arr[1];
                    }
                    $order_arr[] = "t.{$by} {$dir}";
                }
                $sql .= " ORDER BY ". implode(", ", $order_arr);
            }
            
            $q->stmt = $this->modx->prepare($sql);
            //package_id
        }
        
        if(!$q->stmt->execute() OR !$result = $q->stmt->fetchAll(PDO::FETCH_ASSOC)){
            $this->failure("Не были получены пакеты");
            return false;
        }
        
        return $result;
    }
}

return 'modxRepositoryGetPackagesClass';
?>
