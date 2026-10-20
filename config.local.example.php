<?php
// EduBridge — local DB credentials. COPY THIS FILE to config.local.php and fill
// in your own values, then commit only the .example version.
//
// Resolution order used by includes/db.php:
//   1. config.local.php  (this file)
//   2. EDU_DB_* environment variables
//   3. built-in defaults (no password)
//
// Linux/Windows XAMPP usually listens on TCP 3306. Leave DB_SOCK empty unless
// you specifically want the unix socket (e.g. macOS XAMPP).

return [
  'DB_HOST' => '127.0.0.1',
  'DB_PORT' => '3306',
  'DB_SOCK' => '',
  'DB_NAME' => 'edubridge',
  'DB_USER' => 'root',
  'DB_PASS' => '',
];
