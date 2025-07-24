<?php
$password = "S1gerrr";
$password_hash = password_hash($password, PASSWORD_DEFAULT);
echo($password_hash);
?>