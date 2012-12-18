<?php
$resource = & $modx->resource;

switch($resource->action){
    case 'verify':
        print 1;
        break;
    default:;
}

exit;