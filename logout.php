<?php
require "config.php";

unset($_SESSION['user_id']);
header('Location: /');

?>