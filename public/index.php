<?php

use App\Kernel;

require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

return static function (array $context) {
    if ($_ENV['FORCE_HTTPS'] ?? false) {
        $_SERVER['REQUEST_SCHEME'] = 'https';
        $_SERVER['HTTPS'] = 'on';
    }
    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};
