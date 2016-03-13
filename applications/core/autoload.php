<?php
function __autoload($class_name)
{
    if (preg_match('/^Model_/', $class_name)) {
        // class is a model 
        $filename = preg_replace('/^Model_/', '', $class_name).EXT;
        $filepath = JAMBURA_MODS.$filename;
        if(file_exists($filepath)) {
            require_once($filepath);
            return;
        }
        throw new Exception ("class $class_name not found!");
    } elseif (preg_match('/^Controller_/', $class_name)) {
        // class is a controller
        $filename = preg_replace('/^Controller_/', '', $class_name).EXT;
        $filepath = JAMBURA_CONTROLLERS.$filename;

        if (file_exists($filepath)) {
            require_once($filepath);
            return;
        }
    } else {
        $filename = strtolower($class_name).EXT;
        $filepath = JAMBURA_CLASSES.$filename;

        if (file_exists($filepath)) {
            require_once($filepath);
            return;
        }
    }
    throw new Exception("class $class_name not found!");
}
