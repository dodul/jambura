<?php
#
# include configuration: include configuration file, containing 
# database access info and all.
include('configurations.php');
include('Jambura.php');

Jambura::app()->setConfig($config);

include('autoload.php');
# Register autoloader
spl_autoload_register('jamburaAutoload');
#
# define Paths
define('JAMBURA_CORE', 'vendor/dodul/jambura-core');
define('JAMBURA_VENDORS', 'applications/vendors/');
define('JAMBURA_MOD', 'PORD');
#
# include all required php libraries.
# include third party libraries
include(JAMBURA_VENDORS.'idiorm/idiorm.php');
# include core libraries
foreach (glob(JAMBURA_CORE.'/*'.EXT) as $filename) {
    include $filename;
}

#
# configure ORM
ORM::configure('mysql:host='.DB_SERVER.';dbname='.DB_NAME);
ORM::configure('username', DB_SERVER_USERNAME);
ORM::configure('password', DB_SERVER_PASSWORD);
#
# check controller and actions: render default page if controller
# or action is not found.
#
try {
    Jambura::app()->routeRequest()->respond();
} catch (Exception $e) {
    if (JAMBURA_MOD == 'DEV') {
        echo $e->getMessage().'<br>';
        echo $e->getTraceAsString().'<br>';
    } else {
        include('404.html');
    }
}
