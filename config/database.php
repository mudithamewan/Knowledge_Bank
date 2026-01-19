<?php

use Illuminate\Support\Str;

return array(

    'default' => 'main',

    'connections' => array(


        # Our secondary database connection
        'main' => array(
            'driver'    => 'mysql',
            'host'      => 'localhost',
            'database'  => 'knowledge_bank',
            'username'  => 'root',
            'password'  => '',
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
            'logging' => true,
        ),



    ),
);
