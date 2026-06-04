<?php
session_start();
session_unset();
session_destroy();

header("Location: BERANDA1.php");
exit();
?>