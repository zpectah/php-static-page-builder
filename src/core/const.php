<?php
$CONFIG_WEB = json_decode(file_get_contents(PATH_ROOT . 'config/web.json'), true);
$CONFIG_ENV = json_decode(file_get_contents(PATH_ROOT . 'config/environmental.json'), true);
$DATA_JSON = json_decode(file_get_contents(PATH_ROOT . 'public/data.json'), true);

// Constants definitions
const APP_DEBUG = true;
const ENV = BUILD['env'];
const TIMESTAMP = BUILD['timestamp'];
const TEMPLATE_ROOT_PATH = PATH_ROOT . 'core/views';

define("CFG_WEB", $CONFIG_WEB);
define("CFG_ENV", $CONFIG_ENV[ENV]);

define("DATA_JSON", $DATA_JSON);
