<?php
echo "<table border='1' cellpadding='8' cellspacing='0'>";
echo "<tr><th>Password</th><th>Hash</th></tr>";

for ($i = 1; $i <= 7; $i++) {
    $password = 'hash' . $i;
    $hash = password_hash($password, PASSWORD_BCRYPT);

    echo "<tr>";
    echo "<td>$password</td>";
    echo "<td style='font-family: monospace;'>$hash</td>";
    echo "</tr>";
}

echo "</table>";