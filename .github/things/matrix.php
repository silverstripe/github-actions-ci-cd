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
foreach ($defaultJobs as $arr) {
    foreach (array_keys($arr) as $test) {
        if ($test === 'php' || !isset($runTests[$test]) || !$runTests[$test]) {
            continue;
        }
        $matrix['include'][] = $arr;
    }
}
foreach ($extraJobs as $arr) {
    $matrix['include'][] = $arr;
}

$json = json_encode($matrix);
$json = preg_replace("#\n +#", "\n", $json);
$json = str_replace("\n", '', $json);
echo trim($json);
