<?php
// path to database
$config['database'] = 'db/resizer.sqlite';
// MD5 of adminer password
$config['adminer_password'] = md5('heslo');
// secret key (throw something from random.org here)
$config['secret'] = '123456789';
$config['show_debug'] = false;
$config['kyselo_url'] = 'https://beta.kyselo.eu/';
$config['self_url'] = 'https://resizer.kyselo.eu';
return $config;
