<?php
/**
 * Determine a key and decrypt a Vigenère cipher. The key length is determined
 * by looking at the index of coincidence for possible key lengths. The correct
 * key length should have an index of coincidence that is similar to the
 * plaintext's language.
 *
 * (c) Jacob Chafik <jacob@achafik.com>
 */
Header('Content-Type: text/plain');

// The file that we want to analyze
define('CONFIG_FILE', dirname(__FILE__) . '/files/4.txt');
define('KEY_LENGTH', 7);
define('KEY', 'ESPANOL');

// The modulo we're working in
define('MODULO', 26);

// Retrieve and sanitize the text
$ciphertext = @file_get_contents( CONFIG_FILE ) or die('File does not exist.');
$ciphertext = preg_replace("/[[:blank:]\r\n]/", '', $ciphertext);


// Step 1: Find the key length by calculating coincidence indices
echo "Testing IC for possible key lengths:\n";
for ($m = 1; $m <= 10; $m++) {
    echo "m=$m: ";
    
    // Calculate the IC for each possible substring (one for each key character)
    for ($offset = 0; $offset < $m; $offset++) {
        $text = get_substr($ciphertext, $m, $offset);
        echo calc_ic($text) . ', ';
    }
    
    echo "\n";
}
echo "\n";


// Step 2: Find the key. Knowing the key length, we can split the ciphertext
// into multiple ceaser ciphers. The IC of these ciphers can then be calculated
// for possible key values. The correct key value will have an IC that closely
// matches that of the language.

// The standard language probabilities of the language we're testing against
$p = array(
    'A' => .082, 'B' => .015, 'C' => .028,
    'D' => .043, 'E' => .127, 'F' => .022,
    'G' => .020, 'H' => .061, 'I' => .070,
    'J' => .002, 'K' => .008, 'L' => .040,
    'M' => .024, 'N' => .067, 'O' => .075,
    'P' => .019, 'Q' => .001, 'R' => .060,
    'S' => .063, 'T' => .091, 'U' => .028,
    'V' => .010, 'W' => .023, 'X' => .001,
    'Y' => .020, 'Z' => .001
);

echo "Testing possible key values (m=" . KEY_LENGTH . "):\n";
echo str_pad('i', 3);
echo "A,J,S    B,K,T    C,L,U    D,M,V    ";
echo "E,N,W    F,O,X    G,P,Y    H,Q,Z    I,R\n";
for ($offset = 0; $offset < KEY_LENGTH; $offset++) {
    echo str_pad($offset, 3);
    for ($k = 0; $k < 26; $k++) {
        // Get the shift cipher for this key position
        $ceaser = get_substr($ciphertext, KEY_LENGTH, $offset);
        
        // Calculate the IC for $k as the key value.
        $ic = calc_ic_modified(decrypt($ceaser, chr($k + ord('A'))));
        
        echo str_pad($ic, 9);
        
        if (($k + 1) % 9 == 0) echo "\n" . str_pad(' ', 3);
    }
    echo "\n\n";
}

// Step 3: Decrypt the text (once a key is determined)
if (strlen(KEY) > 0) {
    echo "Plaintext:\n";
    $plaintext = decrypt($ciphertext, KEY);
    echo chunk_split($plaintext, 45);
}

/**
 * Calculate the index of coincidence for a string of text. Add up the
 * probability of each character being chosen twice in a row (n choose 2).
 * Text that matches a language will have a higher index than random text (as
 * there are certain characters in languages that appear more often than others)
 *
 * @param string $text the text to calculate the IC for
 * @return int the calculated IC
 */
function calc_ic( $text ) {
    $index = 0;
    $length = strlen($text);
    
    foreach (count_chars($text, 1) as $freq) {
        $index += ($freq / $length) * (($freq - 1) / ($length - 1));
    }
    
    return number_format($index, 3);
}

/**
 * Calculate the index of coincidence. This is a modified version of the
 * original IC. The probability of the character occurring in the text is
 * multiplied by the probability of the character occurring in the language.
 *
 * @param string $text the text to calculate the IC for
 * @return int the calculated IC
 */
function calc_ic_modified( $text ) {
    global $p;
    $index = 0;
    $length = strlen($text);
    
    foreach (count_chars($text, 1) as $char => $freq) {
        $index += $p[strtoupper(chr($char))] * ($freq / $length);
    }
    
    return number_format($index, 3);
}

/**
 * Get a substring from the Vigenère cipher. If m is correct, the result will be
 * a shift cipher that can then be analyzed to determine the key value.
 *
 * @param string $text the ciphertext
 * @param int $m the key length
 * @param int $o the offset
 */
function get_substr($text, $m, $offset = 0) {
    $result = '';
    
    for ($i = $offset; $i < strlen($text); $i += $m) {
        $result .= $text[$i];
    }
    
    return $result;
}

/**
 * Encrypt a character using the Vigenère cipher
 *
 * @param string $plaintext the original text to encrypt
 * @param string $key the encryption key
 * @result string the encrypted character
 */
function encrypt( $plaintext, $key ) {
    $ciphertext = '';
    
    for ($i = 0; $i < strlen($plaintext); $i++) {
        $num = tmod(num($plaintext[$i]) + num($key[$i % strlen($key)]), MODULO);
        $ciphertext .= chr($num + ord('A'));
    }
    
    return $ciphertext;
}

/**
 * Decrypt a character using the Vigenère cipher
 *
 * @param string $text the ciphertext to decrypt
 * @param string $key the encryption key
 * @result string the decrypted character
 */
function decrypt( $ciphertext, $key ) {
    $plaintext = '';
    
    for ($i = 0; $i < strlen($ciphertext); $i++) {
        $num = tmod(num($ciphertext[$i]) - num($key[$i%strlen($key)]), MODULO);
        $plaintext .= chr($num + ord('a'));
    }
    
    return $plaintext;
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
