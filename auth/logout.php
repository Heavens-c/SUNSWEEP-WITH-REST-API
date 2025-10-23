<?php
session_start();
session_destroy();
header('Location: /sunsweep/auth/login.php'); exit;
