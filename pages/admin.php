<?php // Legacy route — the admin console now lives in the standalone app at /admin/.
// The new app owns the staff session (admin_user), so we hand over and let it
// decide between the login page and the console.
header('Location: admin/index.php?r=console');
exit;
