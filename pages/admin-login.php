<?php // Legacy route — staff sign-in now lives in the standalone admin app at /admin/.
header('Location: admin/index.php?r=login');
exit;
