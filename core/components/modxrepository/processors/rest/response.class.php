<?php
/*
 * Абстрактный класс для вывода результатов запросов в XML
 */
abstract class modxRepositoryResponse extends modProcessor{
    var $root = '<root/>';
    
    var $processorsParams = array();
    
    
    function __construct(modX &$modx, array $properties = array()) {
        // Получаем путь до процессоров
        if(!$ns = $modx->getObject('modNamespace', 'modxrepository')){
            $err = "Не было получено пространство имен modxrepository";
            $modx->log(xPDO::LOG_LEVEL_ERROR, $err);
            $this->failure($err);
            return; 
        }  
        
        $processors_path = $ns->getCorePath().'processors/';
        
        $this->processorsParams  = array(
            'processors_path'   => $processors_path,
            'location'          => 'rest',
        );
        
        parent::__construct($modx, $properties);
    }
    

    public function runProcessor($action, $scriptProperties = array()){
        
        
        if(!$this->processorsParams){
            $this->failure("Не были получены данные процессоров");
            return false;
        }
        return $this->modx->runProcessor($action, $scriptProperties, $this->processorsParams);
    }

    public function toXML($response, $rootParams =  array()){
        header('Content-type:text/xml', 1);
        $response = (array)$response; 
        $xml = new SimpleXMLElement($this->root);
        foreach($rootParams as  $k => $v){
            $xml->addAttribute($k, $v);
        }
        $this->array_to_xml($response, $xml);  
        return $xml->asXML();
    }
    
    function array_to_xml($arr, & $xml)
    {
        foreach ($arr as $k => $v) {
            if(is_numeric($k)){
                $this->array_to_xml($v,  $xml);
            }
            else if(is_array($v)){
                $this->array_to_xml($v,  $xml->addChild($k));
            }
            else{
                $node = $xml->addChild($k, $v);
                if(is_string($v)){
                    $type = 'string';
                }
                else if(is_float($v)){
                    $type = 'float';
                }
                else if(is_numeric($v)){
                    $type = 'integer';
                }
                else{
                    $type = false;
                }
                if($type) $node->addAttribute('type', $type);
            } 
        }
        return $xml;
    }
}

return 'modxRepositoryResponse';
?>
