<?php // Legacy admin content API — superseded by the standalone admin app at admin/inc/api.php.
// Kept as a thin shim: it delegates to the new admin API, which enforces the
// separate admin_user session (staff must sign in at admin/index.php?r=login).
require __DIR__.'/../admin/inc/api.php';
