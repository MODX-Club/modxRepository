<?php
 
/*
 * @package modxRepository
 * @subpackage build
 * @author Fi1osof
 * http://community.modx-cms.ru/profile/Fi1osof/
 * http://modxstore.ru
 */
require_once dirname(dirname(dirname(dirname(__FILE__)))).'/core/config/config.inc.php';
  
$pkg_name = 'modxRepository';
    
/* define package */
define('PKG_NAME', $pkg_name);
define('PKG_NAME_LOWER',strtolower(PKG_NAME));
define('NAMESPACE_NAME', PKG_NAME_LOWER);

define('PKG_PATH', PKG_NAME_LOWER);
define('PKG_CATEGORY', PKG_NAME);

$pkg_version = '1.2.0';
$pkg_release = 'beta';
define('PKG_VERSION', $pkg_version); 
define('PKG_RELEASE', $pkg_release); 

/* define sources */
$root = dirname(dirname(__FILE__)).'/';


/*
 * Константы
 */


global $sources;
$sources = array(
    'root' => $root,
    'build' => $root . '_build/',
    'data' => $root . '_build/data/',
    'resolvers' => $root . '_build/resolvers/',
    'chunks' => $root.'core/components/'.PKG_PATH.'/elements/chunks/',
    'snippets' => $root.'core/components/'.PKG_PATH.'/elements/snippets/',
    'plugins' => $root.'core/components/'.PKG_PATH.'/elements/plugins/',
    'lexicon' => $root . 'core/components/'.PKG_PATH.'/lexicon/',
    'docs' => $root.'core/components/'.PKG_PATH.'/docs/',
    'pages' => $root.'core/components/'.PKG_PATH.'/elements/pages/',
    'source_assets' => $root.'assets/components/'.PKG_PATH,
    'source_core' => $root.'core/components/'.PKG_PATH,
    'templates' => $root.'core/components/'.PKG_PATH.'/elements/templates/',
    'model' => $root.'core/components/'.PKG_PATH.'/model/',
);
unset($root);
 

require_once MODX_CORE_PATH . 'model/modx/modx.class.php';
require_once $sources['build'] . '/includes/functions.php';

$modx= new modX();
$modx->initialize('mgr'); 