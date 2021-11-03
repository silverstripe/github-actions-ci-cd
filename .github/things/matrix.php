<?php

// Reads inputs.yml and creates a new json matrix

$inputs = yaml_parse(file_get_contents('inputs.yml'));
$run = [];
$defaultJobs = [];
$extraJobs = [];
foreach ($inputs as $input => $value) {
    if (preg_match('#^run_#', $input)) {
        if ($value === 'true') {
            $value = true;
        }
        if ($value === 'false') {
            $value = false;
        }
        // e.g. run_phplinting => phplinting
        $type = str_replace('run_', '', $input);
        $run[$type] = $value;
    } else if ($input === 'default_jobs') {
        if ($value === 'none') {
            $value = [];
        }
        $defaultJobs = $value;
    } else if ($input === 'extra_jobs') {
        if ($value === 'none') {
            $value = [];
        }
        $extraJobs = $value;
    }
}

$matrix = ['include' => []];

if ((file_exists('phpunit.xml') || file_exists('phpunit.xml.dist')) && $run['phpunit']) {
    $fn = file_exists('phpunit.xml') ? 'phpunit.xml' : 'phpunit.xml.dist';
    $d = new DOMDocument();
    $d->preserveWhiteSpace = false;
    $d->load($fn);
    $x = new DOMXPath($d);
    $tss = $x->query('//testsuite');
    foreach ($tss as $ts) {
        if (!$ts->hasAttribute('name') || $ts->getAttribute('name') == 'Default') {
            continue;
        }
        $matrix['include'][] = ['php' => '7.4', 'phpunit' => true, 'phpunit_suite' => $ts->getAttribute('name')];
        if ($run['phpunit_php8']) {
            $matrix['include'][] = ['php' => '8.0', 'phpunit' => true, 'phpunit_suite' => $ts->getAttribute('name')];
        }
    }
    if (count($matrix) == 0 && $run['phpunit']) {
        $matrix['include'][] = ['php' => '7.4', 'phpunit' => true, 'phpunit_suite' => ''];
        if ($run['phpunit_php8']) {
            $matrix['include'][] = ['php' => '8.0', 'phpunit' => true, 'phpunit_suite' => ''];
        }
    }
}

if ((file_exists('phpcs.xml') || file_exists('phpcs.xml.dist')) && $run['phpcoverage']) {
    $matrix['include'][] = ['php' => '7.4', 'phplinting' => true];
}
// TODO: no codecov file in silverstripe-admin, so cannot feature detect. should probably run by default and allow disabling with 'no_phpcoverage'?
if (file_exists('behat.yml') && $run['endtoend']) {
    $matrix['include'][] = ['php' => '7.3', 'endtoend' => true];
}
if (file_exists('package.json') && $run['js']) {
    $matrix['include'][] = ['php' => '7.4', 'js' => true];
}

foreach ($extraJobs as $arr) {
    $matrix['include'][] = $arr;
}

foreach ($matrix['include'] as $arr) {
    $arr['composer_arg'] ??= ''; 
}

$json = json_encode($matrix);
$json = preg_replace("#\n +#", "\n", $json);
$json = str_replace("\n", '', $json);
echo trim($json);
