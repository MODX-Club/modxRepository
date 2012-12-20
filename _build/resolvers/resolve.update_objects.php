<?php 
/*
 * @package modxRepository
 * @subpackage build
 * @author Fi1osof
 * http://community.modx-cms.ru/profile/Fi1osof/
 * http://modxstore.ru
 */

if ($object->xpdo) {
    $modx =& $object->xpdo;
  
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
            if ($modx instanceof modX) {}
            break;
    }
}
return true;