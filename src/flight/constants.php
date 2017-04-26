<?php

defined('PHISH_PATH')                 OR define('PHISH_PATH', '/var/www/data/phishy.json');
defined('SOMETHING_SMELLS_PHISHY')    OR define('SOMETHING_SMELLS_PHISHY', 'phish_on_the_line');

//Keys 
defined('KEYS_SUCCESS')               OR define('KEYS_SUCCESS', 'success');
defined('KEYS_LOGGED')                OR define('KEYS_LOGGED', 'logged');
defined('KEYS_MESSAGE')               OR define('KEYS_MESSAGE', 'message');
defined('KEYS_URL')                   OR define('KEYS_URL', 'url');
defined('KEYS_ALIAS')                 OR define('KEYS_ALIAS', 'alias');
defined('KEYS_ERROR')                 OR define('KEYS_ERROR', 'error');
defined('KEYS_EXISTING')              OR define('KEYS_EXISTING', 'existing');
defined('KEYS_IS_VALID')              OR define('KEYS_IS_VALID', 'existing');

//Messages
defined('MESSAGE_SUCCESSFUL')         OR define('MESSAGE_SUCCESSFUL', 'Successful');
defined('MESSAGE_SHORT_URL_CREATED')  OR define('MESSAGE_SHORT_URL_CREATED', 'Short URL Created.');

//Errors
defined('ERROR_REPLACE_STRING')       OR define('ERROR_REPLACE_STRING', 'var_replace');
defined('ERROR_REDIRECT_LOG_FAIL')    OR define('ERROR_REDIRECT_LOG_FAIL', 'Could not log redirect...');
defined('ERROR_NOT_FOUND')            OR define('ERROR_NOT_FOUND', 'Not Found...');
defined('ERROR_START_HTTP')           OR define('ERROR_START_HTTP', 'Url does not start with \'http\'...');
defined('ERROR_EMPTY_URL')            OR define('ERROR_EMPTY_URL', 'Empty Url...');
defined('ERROR_INVALID_URL')          OR define('ERROR_INVALID_URL', 'Invalid Url...');

