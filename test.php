<?php
$hash = '$2y$10$8ZybOx5Sg7ScZfa6Sc9lu.1DcwZ1BxquKpbKaLEAv0gTX5n3waHbC'; // from DB
$plain = 'admin123';
var_dump(password_verify($plain, $hash));
echo "$plain\n$hash\n";
?>