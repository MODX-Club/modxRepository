<?php 

if ($object->xpdo) {
    $modx =& $object->xpdo;
    // $modelPath = $modx->getOption($pkgName.'.core_path',null,$modx->getOption('core_path').'components/'.$pkgName.'/').'model/';

    switch ($options[xPDOTransport::PACKAGE_ACTION]) {
        case xPDOTransport::ACTION_INSTALL:
        case xPDOTransport::ACTION_UPGRADE:
            
            if ($modx instanceof modX) {
                /*
                 * Linked modTemplates and modTemplatesVar
                 */

                    $templates = array(
                        'Repository',
                        'Release',
                        'Package',
                    );

                    $tvs = array(
                        'object_id' => array(
                                'Repository',
                                'Release',
                                'Package',
                        ),
                        'version_major' => array(
                            'Release', 
                        ),
                        'version_minor' => array(
                            'Release', 
                        ),
                        'version_patch' => array(
                            'Release', 
                        ),
                        'release' => array(
                            'Release', 
                        ),
                        'vrelease_index' => array(
                            'Release', 
                        ),
                        'file' => array(
                            'Release', 
                        ),
                        'changelog' => array(
                            'Release', 
                        ),
                        'instructions' => array(
                            'Release', 
                        ),
                        'r_description' => array(
                            'Release', 
                        ),
                        'templated' => array(
                                'Repository',
                        ),
                    );

                    foreach($templates as $t){
                        $$t = $modx->getObject('modTemplate', array(
                            'templatename' => $t,
                        ));
                    }

                    foreach($tvs as $t => $tpls){
                    $$t = $modx->getObject('modTemplateVar',  array(
                        'name' => $t,
                    ));
                        foreach($tpls as $tpl){
                            $tplvar = $modx->newObject('modTemplateVarTemplate');
                            $tplvar->addOne($$t); 
                            $tplvar->addOne($$tpl); 
                            $tplvar->save();
                        }
                    }
                    
                    /*
                     * Add Media link
                     */
                    
                    if($media = $modx->getObject('sources.modMediaSource', array(
                        'name' => 'Repository Packages',
                    )) AND $sl = $modx->newObject('sources.modMediaSourceElement')){
                        $sl->set('source', $media->id);
                        $sl->set('object', $file->id);
                        $sl->set('object_class', 'modTemplateVar');
                        $sl->save();
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