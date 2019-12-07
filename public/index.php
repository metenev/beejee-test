<?php

ini_set('display_errors', 1);

define('PATH_ROOT', dirname(__DIR__) . '/');
define('PATH_APP', dirname(__DIR__) . '/app/');
define('PATH_CORE', dirname(__DIR__) . '/core/');

function printError($errno, $errstr, $errfile, $errline, $errcontext) {
    http_response_code(500);

    ?>
        <style>
            body {
                background-color: #640822;
                color: #ffffff;
            }
            .pale {
                opacity: 0.65;
            }
        </style>
        <div style="padding: 16px 20px; margin-bottom: 8px; font: 14px monospace; background-color: #7c0023;">
            <h1 style="margin-top: 0;">Error #<?php echo $errno; ?>: <?php echo $errstr; ?></h1>
            <p><span class="pale">File:</span> <?php echo $errfile; ?> on line <strong><?php echo $errline; ?></strong></p>
            <hr class="pale" style="height: 1px; margin: 20px 0; border: none; background-color: #ffffff;">
            <h3>Context:</h3>
            <pre><?php echo json_encode($errcontext, JSON_PRETTY_PRINT); ?></pre>
        </div>
    <?php
}

function errorHandler($errno, $errstr, $errfile, $errline, $errcontext) {
    ob_end_clean();
    printError($errno, $errstr, $errfile, $errline, $errcontext);
}

function exceptionHandler($exception) {
    ob_end_clean();
    printError($exception->getCode(), $exception->getMessage(), $exception->getFile(), $exception->getLine(), $exception->getTrace());
}

set_error_handler('errorHandler', error_reporting());
set_exception_handler('exceptionHandler');

require_once PATH_ROOT . 'vendor/autoload.php';
require_once PATH_APP . 'bootstrap.php';
