<?php
session_start();
session_destroy();
header("Location: /charity-management-system/index.php");
exit();
?>
