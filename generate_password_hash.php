<?php
/**
 * Generate Password Hash
 * Usage: Run this file in browser to generate password hash
 */

$password = 'karyawan123';
$hash = password_hash($password, PASSWORD_DEFAULT);

echo "Password: {$password}\n";
echo "Hash: {$hash}\n";
echo "\nCopy the hash above and update migration_add_role_to_users.sql";
