<?php
/**
 * Display ciphertext and known plaintext together for a general substitution
 * cipher.
 *
 * (c) Jacob Chafik <jacob@achafik.com>
 */
Header('Content-Type: text/plain');

// The file that we want to analyze
define('CONFIG_FILE', dirname(__FILE__) . '/input/3.txt');

$map = array(
    'A' => 't', 'T' => 'e', 'S' => 'h', 'I' => 'w', 'J' => 'i', 'M' => 'r',
    // 'P' => 'a', 'U' => 'o', 'R' => 'f', 'Y' => 's', 'G' => 'm', 'H' => 'c',
    // 'D' => 'u', 'E' => 'l', 'B' => 'd', 'C' => 'n', 'X' => 'g', 'V' => 'v',
    // 'W' => 'b', 'K' => 'y', 'F' => 'p', 'O' => 'k', 'N' => 'x',
);

// Retrieve and sanitize the text
$text = file_get_contents( CONFIG_FILE );
$text = preg_replace("/[[:blank:]\r]/", '', $text);

foreach (explode("\n", $text) as $line) {
    for ($i = 0; $i < strlen($line); $i++) {
        $char = $line[$i];
        echo isset($map[$char]) ? $map[$char] : '-';
    }
    echo "\n";
    echo $line . "\n\n";
}
