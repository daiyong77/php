<?php
require "vendor/autoload.php";

use Daiyong\Http;

$a = Http::curl('https://www.baidu.com');

print_r($a);
