<?php
return array (
  'security' =>
  array (
    'mode' => 'strict',
    'force_https' => false,
    'session_lifespan' => 7200,
    'perform_session_fingerprinting' => true,
    'debug_fingerprint' => false,
  ),
  'debug_and_monitoring' =>
  array (
    'debug' => false,
    'log_stacktrace' => true,
    'stacktrace_length' => 25,
    'report_errors' => false,
  ),
  'info' =>
  array (
    'salt' => '1b534597044dc2ea9fd78c59c565e6fd',
    'instance_id' => '91feee75-aa40-4dc4-aa77-bfd1176dc9f8',
  ),
  'url' => 'http://192.168.122.79/',
  'admin_area_prefix' => '/admin',
  'update_branch' => 'preview',
  'maintenance_mode' =>
  array (
    'enabled' => false,
    'allowed_urls' =>
    array (
    ),
    'allowed_ips' =>
    array (
    ),
  ),
  'disable_auto_cron' => false,
  'i18n' =>
  array (
    'locale' => 'en_US',
    'timezone' => 'UTC',
    'date_format' => 'medium',
    'time_format' => 'short',
    'datetime_pattern' => '',
  ),
  'path_data' => '/var/www/html/data',
  'db' =>
  array (
    'type' => 'mysql',
    'host' => 'mysql',
    'port' => '3306',
    'name' => 'fossbilling',
    'user' => 'fossbilling',
    'password' => 'fossbilling',
  ),
  'twig' =>
  array (
    'debug' => false,
    'auto_reload' => true,
    'cache' => '/var/www/html/data/cache',
  ),
  'api' =>
  array (
    'require_referrer_header' => false,
    'allowed_ips' =>
    array (
    ),
    'rate_span' => 3600,
    'rate_limit' => 1000,
    'throttle_delay' => 2,
    'rate_span_login' => 60,
    'rate_limit_login' => 20,
    'CSRFPrevention' => true,
    'rate_limit_whitelist' =>
    array (
    ),
  ),
);
