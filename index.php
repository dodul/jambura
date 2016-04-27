<?php

# FIXME: Set environment: can be set as development or production.
# in production environment, no error will be displayed.
#

#
# include configuration: include configuration file, containing
# database access info and all.

include('configurations.php');
#

# define Paths

define('JAMBURA_CORE', 'applications/core/');
define('JAMBURA_MODS', 'applications/models/');
define('JAMBURA_CONTROLLERS', 'applications/controllers/');
define('JAMBURA_VIEWS', 'applications/views/');
define('JAMBURA_VENDORS', 'applications/vendors/');
define('JAMBURA_CLASSES', 'applications/classes/');
define('JAMBURA_TEMPLATES', 'templates/');
define('JAMBURA_MOD', 'PROD');
#

# include all required php libraries.
# include third party libraries

include(JAMBURA_VENDORS.'idiorm/idiorm.php');
#

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

try {
    (new jRouter())->route()->display();
} catch (Exception $e) {
    if (JAMBURA_MOD == 'DEV') {
        echo $e->getMessage().'<br>';
        echo $e->getTraceAsString().'<br>';
    } else {
        include('404.html');
    }
}
#
