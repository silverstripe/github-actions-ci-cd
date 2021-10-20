<?php

// use .github/things/behat.yml as the main behat.yml file,
// though use 'suites' from the module behat.yml
$a = file_get_contents('behat-headless.yml');
$b = file_get_contents('behat.yml');
preg_match("#(?s)  suites:(.+?)\n  [a-z]#", $b, $m);
if (!$m) {
    preg_match("#(?s)  suites: (.+?)$#", $b, $m);
}
if (!$m) {
    echo "Could not match suites in behat.yml, cannot run behat\n\n";
    die;
}
$c = str_replace('suites: []', 'suites: ' . $m[1], $a);
file_put_contents('behat.yml', $c);
# echo "$c\n\n";
