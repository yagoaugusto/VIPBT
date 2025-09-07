$password = 'admin';
$hashed_password = password_hash($password, PASSWORD_DEFAULT);
echo "Senha original: " . $password . "<br>";
echo "Hash gerado: " . $hashed_password . "<br>";