<?php
/*
 * Проверка статуса сайта репозиториев
 */
require_once  dirname(__FILE__).'/response.class.php';


class modxRepositoryVerify extends modxRepositoryResponse{
    
    var $root = '<status/>';
    
    public function process(){
        // return $this->toArray(1);
        $result = array(
            'verified'  => 1,
        );
         
        return $this->toXML($result);
    }
}
return 'modxRepositoryVerify';
?>
