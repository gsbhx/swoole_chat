<?php
function autoload($classname)
{
    $classname=str_replace("\\",'/',$classname);
    $classpath=__DIR__.'/'.$classname.'.php';
    if(file_exists($classpath)){
        require_once($classpath);
    }
    else{
        echo 'class file'.$classpath.'not found!';
    }
}
spl_autoload_register('autoload');