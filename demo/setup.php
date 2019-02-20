<?php

use MiladRahimi\Jwt\Authentication\Authentication;
use MiladRahimi\Jwt\Authentication\Subdomains;

require_once __DIR__.'/../autoload.php';

$auth        = new Authentication();
Subdomains::create()->set_domain($_SERVER['HTTP_HOST']);