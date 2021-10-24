<?php

// Reads inputs.yml and creates a new json matrix

$inputs = yaml_parse(file_get_contents('inputs.yml'));
$runTests = [];
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
        $test = str_replace('run_', '', $input);
        $runTests[$test] = $value;
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

// foreach ($defaultJobs as $arr) {
//     foreach (array_keys($arr) as $test) {
//         if ($test === 'php' || !isset($runTests[$test]) || !$runTests[$test]) {
//             continue;
//         }
//         $matrix['include'][] = $arr;
//     }
// }

echo "matrix ls: " . shell_exec('ls') . "\n";

if (file_exists('phpunit.xml') || file_exists('phpunit.xml.dist')) {
    $matrix['include'][] = ['php' => '7.3', 'phpunit' => true, 'composer_arg' => '--prefer-lowest'];
    $matrix['include'][] = ['php' => '7.4', 'phpunit' => true];
}
if (file_exists('phpcs.xml') || file_exists('phpcs.xml.dist')) {
    $matrix['include'][] = ['php' => '7.4', 'phplinting' => true];
}
// TODO: no codecov file in silverstripe-admin, so cannot feature detect. should probably run by default and allow disabling with 'no_phpcoverage'?
if (file_exists('behat.yml')) {
    $matrix['include'][] = ['php' => '7.3', 'endtoend' => true];
}
if (file_exists('package.json')) {
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
