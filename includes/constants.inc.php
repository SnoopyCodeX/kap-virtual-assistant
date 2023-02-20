<?php  
// Read the settings.inc.json file and decode the json
$jsonConstants = FileUtil::readFile(__DIR__ . '/settings.inc.json', 1024);
$constants = json_decode($jsonConstants, true);

// Automatically define them as constant variables
foreach ($constants as $constant) {
    foreach($constant as $key => $value)
        define($key, $value);
}

?>