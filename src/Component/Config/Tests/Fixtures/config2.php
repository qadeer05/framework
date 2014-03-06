<?php return array(
    'database' =>
        array(
            'connections' =>
                array(
                    'mysql' =>
                        array(
                            'host' => 'localhost',
                            'user' => 'root',
                            'password' => 'root',
                            'dbname' => 'pagekit_3',
                            'prefix' => 'pk_',
                        ),
                ),
        ),
    'app' =>
        array(
            'site_title' => 'Test',
            'locale' => '%replacement%',
            'key' => '63f20fb21acb3e8f85febfe0d0fa071e942eb7da',
            'site_description' => '',
            'timezone' => 'utc',
            'debug' => '0',
        ),
    'storage' => '',
    'mail' =>
        array(
            'from' =>
                array(
                    'address' => '',
                    'name' => '',
                ),
            'driver' => 'mail',
            'port' => '',
            'host' => '',
            'username' => 'admin',
            'password' => 'admin',
            'encryption' => '',
        ),
    'local_date_format' => '',
    'local_time_format' => '',
    'local_firstdayofweek' => '1',
    'cache' =>
        array(
            'storage' => 'auto',
        ),
    'profiler' =>
        array(
            'enabled' => '%repalcement2%'
        ),
    'maintenance' =>
        array(
            'enabled' => '1',
            'msg' => 'Offline',
        ),
    '%repalcement2%' => '0',
    'testval' => 1
);