<?php
session_start();
session_unset();
session_destroy();
header('Location: ../Index.html?logout=1');
exit();
