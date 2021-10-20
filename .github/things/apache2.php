<?php
$pwd = rtrim(trim(shell_exec('pwd')), '/');
$s = file_get_contents('000-default.conf');
#$s = file_get_contents('/etc/apache2/sites-enabled/000-default.conf');
$s = str_replace('$pwd', $pwd, $s);
file_put_contents('000-default.conf', $s);
