<?php 
/**
  * Kebab's database access configuration file.
  *
  * You may want to have one file like this - with different info - for each environment/machine.
  *
  * @author Miguel pragier <miguelpragier@gmail.com>  
*/

namespace Kebab;

class KebabConfig
{
    const ENVIRONMENT = 'dev';
    # const DB_USER = '';
    # const DB_TARGET_DATABASE = '';
    # const DB_PASSWORD = '';
    const DB_CONNECTION_STRING = '';
    const DB_DRIVER = 'pgsql';  /* Or mysql */
}