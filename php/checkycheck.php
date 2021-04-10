<?php

$cpuLoad = getServerLoad();
if (is_null($cpuLoad)) {
    echo "CPU load not estimateable (maybe too old Windows or missing rights at Linux or Windows)";
} else {
    echo "CPU: " . $cpuLoad . "%" . PHP_EOL;
}

$memory = getSystemMemInfo();

$data = array();
$data['cpu'] = $cpuLoad;
$data['user_id'] = $argv[1];
$data['load_1'] = sys_getloadavg()[0];
$data['load_5'] = sys_getloadavg()[1];
$data['load_15'] = sys_getloadavg()[2];
$data['ram_used'] = ($memory['MemTotal'] * 1000) - ($memory['MemAvailable'] * 1000); // RAM info is in kB -> Convert to bytes
$data['ram_free'] = $memory['MemAvailable'] * 1000;
$data['storage_used'] = disk_total_space('/') - disk_free_space('/');
$data['storage_free'] = disk_free_space('/');

$options = array(
    'http' => array(
        'method'  => 'POST',
        'content' => json_encode($data),
        'header' =>  "Content-Type: application/json\r\n" .
            "Accept: application/json\r\n"
    )
);

$url = 'https://checkycheck.io/api/server/info';
$context  = stream_context_create($options);
$result = file_get_contents($url, false, $context);
$response = json_decode($result);

// CPU (https://www.php.net/manual/de/function.sys-getloadavg.php#118673)
//--------------------------

function _getServerLoadLinuxData()
{
    if (is_readable("/proc/stat")) {
        $stats = @file_get_contents("/proc/stat");

        if ($stats !== false) {
            // Remove double spaces to make it easier to extract values with explode()
            $stats = preg_replace("/[[:blank:]]+/", " ", $stats);

            // Separate lines
            $stats = str_replace(array("\r\n", "\n\r", "\r"), "\n", $stats);
            $stats = explode("\n", $stats);

            // Separate values and find line for main CPU load
            foreach ($stats as $statLine) {
                $statLineData = explode(" ", trim($statLine));

                // Found!
                if (
                    (count($statLineData) >= 5) &&
                    ($statLineData[0] == "cpu")
                ) {
                    return array(
                        $statLineData[1],
                        $statLineData[2],
                        $statLineData[3],
                        $statLineData[4],
                    );
                }
            }
        }
    }

    return null;
}

// Returns server load in percent (just number, without percent sign)
function getServerLoad()
{
    $load = null;


    // Collect 2 samples - each with 1 second period
    // See: https://de.wikipedia.org/wiki/Load#Der_Load_Average_auf_Unix-Systemen
    $statData1 = _getServerLoadLinuxData();
    sleep(1);
    $statData2 = _getServerLoadLinuxData();

    if (
        (!is_null($statData1)) &&
        (!is_null($statData2))
    ) {
        // Get difference
        $statData2[0] -= $statData1[0];
        $statData2[1] -= $statData1[1];
        $statData2[2] -= $statData1[2];
        $statData2[3] -= $statData1[3];

        // Sum up the 4 values for User, Nice, System and Idle and calculate
        // the percentage of idle time (which is part of the 4 values!)
        $cpuTime = $statData2[0] + $statData2[1] + $statData2[2] + $statData2[3];

        // Invert percentage to get CPU time, not idle time
        $load = 100 - ($statData2[3] * 100 / $cpuTime);
    }

    return $load;
}

//----------------------------

// RAM
// ---------------------------

function getSystemMemInfo()
{
    $data = explode("\n", file_get_contents("/proc/meminfo"));
    $meminfo = array();
    foreach ($data as $line) {
        @list($key, $val) = explode(":", $line);
        $val = trim($val);
        $meminfo[$key] = str_replace(' kB', '', $val);
    }
    return $meminfo;
}
