<?php
session_start();
session_unset();
session_destroy();
header('Location: ../LoginFrm.php?logout=1');
exit();
