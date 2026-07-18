<?php
$password = 'admin';
$hash = password_hash($password, PASSWORD_DEFAULT);
echo "Hash: " . $hash . "<br>";
echo "Verify: " . (password_verify($password, $hash) ? "OK" : "FAIL");
?>