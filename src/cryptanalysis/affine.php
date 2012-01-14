<?php
/**
 * Determine a key and decrypt an affine cipher based on two know character
 * mappings, determined using frequency analysis.
 *
 * (c) Jacob Chafik <jacob@achafik.com>
 */
Header('Content-Type: text/plain');

// The file that we want to analyze
define('CONFIG_FILE', dirname(__FILE__) . '/input/2.txt');

// The modulo we're working in
define('MODULO', 26);

// Map plaintext to ciphertext. Only two mappings are needed to determine a key.
$map = array(
    'e' => 'U',         // Single, known value
    'x' => 'D,J,R,Y'    // Comma-delimited list of possible mappings
);

// Retrieve and sanitize the text
$ciphertext = @file_get_contents( CONFIG_FILE ) or die('File does not exist.');
$ciphertext = preg_replace("/[[:blank:]\r\n]/", '', $ciphertext);

// Iterate over the comma-delimited list in our map
foreach (explode(',', end($map)) as $char) {
    $map[array_search(end($map), $map)] = $char;

    // Find the key using our character mapping
    $key = find_key( $map );

    // a and m must be coprime for the key to be valid.
    list( $gcd ) = gcd( $key[0], MODULO );

    // Display Results
    $textMap = ''; foreach ($map as $k => $v) $textMap .= "$k=$v, ";

    echo str_pad('Characters', 15) . str_pad('Key', 15) . str_pad('Valid Key', 15);
    echo "\n";
    echo str_pad(preg_replace('/\, $/', '', $textMap), 15);
    echo str_pad('(' . implode(',', $key) . ')', 15);
    echo ($gcd == 1 ? 'YES' : 'NO') . " GCD=$gcd\n\n";

    // Only display the plaintext if the key is valid
    if ($gcd == 1) {
        $plaintext = decrypt( $ciphertext, $key );
        echo chunk_split( $plaintext, 45 ) . "\n\n";
    }
    
}


/**
 * Determine the key by solving a simultaneous equation for a character mapping
 * Adapted from:
 * http://practicalcryptography.com/ciphers/affine-cipher/#cryptanalysis
 *
 * @param array $map the known character mappings
 * @return array the pair of keys for the given mapping
 */
function find_key( $map ) {
    list ($p, $q) = array_keys( $map ); $p = num($p); $q = num($q);
    list ($r, $s) = array_values( $map ); $r = num($r); $s = num($s);

    $d = tmod($p - $q, MODULO);
    $dp = mod_inverse($d, MODULO);

    $key = array(
        tmod($dp * ($r - $s), MODULO),
        tmod($dp * ($p * $s - $q * $r), MODULO)
    );
    
    return $key;
}

/**
 * Encrypt a character using the affine cipher
 *
 * @param string $plaintext the original text to encrypt
 * @param array $key the encryption key
 * @result string the encrypted character
 */
function encrypt( $plaintext, $key ) {

    $ciphertext = '';
    for ($i = 0; $i < strlen($plaintext); $i++) {
        $num = tmod($key[0] * num($plaintext[$i]) + $key[1], MODULO);
        $ciphertext .= chr($num + ord('A'));
    }
    
    return $ciphertext;
}

/**
 * Decrypt a character using the affine cipher
 *
 * @param string $text the ciphertext to decrypt
 * @param array $key the original encryption key
 * @result string the decrypted character
 */
function decrypt( $ciphertext, $key ) {
    $a = mod_inverse($key[0], MODULO);
    
    $plaintext = '';
    for ($i = 0; $i < strlen($ciphertext); $i++) {
        $num = tmod($a * (num($ciphertext[$i]) - $key[1]), MODULO);
        $plaintext .= chr($num + ord('a'));
    }
    
    return $plaintext;
}

/**
 * Calculate the multiplicative inverse modulo m
 *
 * @param int $x integer to calculate inverse for
 * @param int $m modulo
 * @return int the multiplicative inverse modulo m
 */
function mod_inverse($x, $m = MODULO) {
    list(, $x) = gcd($x, $m);
    return tmod($x, $m);
}

/**
 * Implementation of extended euclidian algorithm. Adapted from:
 * Cryptography - pieces from work by Gordon Royle.
 *
 * @param int $a first integer
 * @param int $b second integer
 * @return array a triple (d, x, y) such that d = gcd(a, b) = xa + yb.
 */
function gcd($a, $b) {
    if ($b == 0) return array($a, 1, 0);
    
    list ($d, $xp, $yp) = gcd($b, $a % $b);
    list ($d, $x, $y) = array($d, $yp, $xp - $yp * floor($a / $b));
    
    return array($d, $x, $y);
}

/**
 * Get the numeric value of a character
 *
 * @param string $char the character to get the numeric value for
 * @return int the character's numeric value (0 - 25)
 */
function num( $char ) {
    return ord(strtoupper($char)) - ord('A');
}

/**
 * Represent negative remainders by their positive equivalence classes.
 * By default, PHP will interpret -3 (mod 7) as -3 opposed to 4.
 *
 * @param int $a integer to calculate for
 * @param int $m modulo
 */
function tmod($a, $m = MODULO) {
    return (($a % $m) + $m) % $m;
}
