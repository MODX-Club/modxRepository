<?php 

if ($object->xpdo) {
    $modx =& $object->xpdo;
    // $modelPath = $modx->getOption($pkgName.'.core_path',null,$modx->getOption('core_path').'components/'.$pkgName.'/').'model/';

    switch ($options[xPDOTransport::PACKAGE_ACTION]) {
        case xPDOTransport::ACTION_INSTALL:
        case xPDOTransport::ACTION_UPGRADE:
            
            if ($modx instanceof modX) {
                if(!$provider = $modx->getObject('transport.modTransportProvider', array(
                    'name'  => 'modx.com',
                ))){
                    return $modx->log(modX::LOG_LEVEL_ERROR, "Could not get transport provider");
                }
                
                if(!$modx->loadClass('transport.modTransportPackage')){
                    return $modx->log(modX::LOG_LEVEL_ERROR, "Could not load modTransportPackage class");
                }
                
                /*
                 * Packages need to install
                 */
                $packages = array(
                    'phpTemplates',
                );
                
                $modx->log(modX::LOG_LEVEL_INFO, "Trying to install packages: ". implode(", ", $packages). "<br />\n
                    Be patient. it`s take a several moments.");
                
                $modx->setLogLevel(modX::LOG_LEVEL_INFO);
                $LogTarget = $modx->getLogTarget();  
                $LogTopic = $LogTarget->subscriptions[0];
                $messages = array();

                
                 
                foreach($packages as $packageName){
                    if(!$packageName)  continue;
                    
                    if($packageExists =  $modx->getObject('modTransportPackage', array(
                        'package_name' => $packageName,
                    ))){
                        $messages[] = array(
                            'level' => modX::LOG_LEVEL_INFO,
                            'msg'   => "Package '{$packageName}' allready exists" ,
                        );
                            
                        /*
                         * Check is installed
                         */
                        if(!$packageExists->get('installed')){
                            $messages[] = array(
                                'level' => modX::LOG_LEVEL_ERROR,
                                'msg'   => "Package '{$packageName}' exists, but not installed. Please, install it manually" ,
                            );
                        }
                        continue;
                    }
                    // $modx->log(modX::LOG_LEVEL_INFO, "Try to get '{$packageName}' package.");
                    
                    $response = $modx->runProcessor('workspace/packages/rest/getlist', array(
                        'provider' => $provider->get('id'),
                        'query'  => $packageName,
                    ));
                    
                    if($response->isError()){
                        $messages[] = array(
                            'level' => modX::LOG_LEVEL_ERROR,
                            'msg'   => $response->getMessage(),
                        );
                        continue;
                    }
                    
                    if(!$result = json_decode($response->getResponse())){ 
                        $messages[] = array(
                            'level' => modX::LOG_LEVEL_ERROR,
                            'msg'   => "Error while reading results" ,
                        );
                        continue;
                    }
                    
                    /*
                     * Try to find package info
                     */
                    foreach($result->results as $r){
                        $package = (array)$r;
                        if($package['name'] == $packageName){
                            goto downloadPackage;
                        }
                    }
                    
                    /*
                     * Info was not found
                     */
                    $messages[] = array(
                        'level' => modX::LOG_LEVEL_ERROR,
                        'msg'   => "Package '{$packageName}' was not found. Please try to install it manually" ,
                    );
                    continue;
                    
                    
                    /*
                     * Download Package
                     */
                    downloadPackage: 
                    $messages[] = array(
                            'level' => modX::LOG_LEVEL_INFO,
                            'msg'   => "Trying to download '{$packageName}' package" ,
                    );
                            
                    $response = $modx->runProcessor('workspace/packages/rest/download', array(
                        'provider' => $provider->get('id'),
                        'info'  =>  "{$package['location']}::{$package['signature']}",
                    ));
                    
                    if($response->isError()){
                        $messages[] = array(
                            'level' => modX::LOG_LEVEL_ERROR,
                            'msg'   => "Failed download '{$packageName}'. ".$response->getMessage(),
                        );
                        continue;
                    }
                    $result = $response->getResponse();
                    if($result['success'] == 1){
                        $messages[] = array(
                            'level' => modX::LOG_LEVEL_INFO,
                            'msg'   => "Package '{$packageName}' downloaded success" ,
                        );
                        goto installPackage;
                    }
                    $messages[] = array(
                        'level' => modX::LOG_LEVEL_ERROR,
                        'msg'   => "Failed download '{$packageName}'.",
                    );
                    
                    
                    /*
                     * Installing Package
                     */
                    installPackage:
                    $messages[] = array(
                            'level' => modX::LOG_LEVEL_INFO,
                            'msg'   => "Trying to install '{$packageName}' package" ,
                    );
                    
                            
                    $response = $modx->runProcessor('workspace/packages/install', array(
                        'signature' => $package['signature'], 
                    ));
                    
                    if($response->isError()){
                        $messages[] = array(
                            'level' => modX::LOG_LEVEL_ERROR,
                            'msg'   => "Failed install '{$packageName}'. ". $response->getMessage(),
                        );
                        continue;
                    }
                    $messages[] = array(
                        'level' => modX::LOG_LEVEL_INFO,
                        'msg'   => $response->getMessage() ,
                    );
                }
                 
                $modx->registry->setLogging($LogTarget, $LogTopic);
                // $modx->log(xPDO::LOG_LEVEL_ERROR, '$err');
                // print_r($messages);
                $modx->setLogLevel(modX::LOG_LEVEL_INFO);
                foreach($messages as $msg){
                    $modx->log($msg['level'], $msg['msg']);
                }
            }
            
            

            break;

        case xPDOTransport::ACTION_UNINSTALL:
            if ($modx instanceof modX) {
                // $modx->removeExtensionPackage($pkgName);
            }
            break;
    }
}
return true;