<?php
/**
 * Performs frequency analysis on a given set of ciphertexts. Finds all
 * character n-grams with a frequency greater than 1.
 *
 * (c) Jacob Chafik <jacob@achafik.com>
 */

// The directory containing text files to analyze
define('CONFIG_DIR', dirname(__FILE__) . '/files/');

// Display all n-grams (frequency > 1), or only the largest substring. See
// is_largest_substr() for more info.
define('CONFIG_LARGEST_ONLY', false);


// Analyze all files in the specified directory
$files = array(); $maxDepth = 0;
$handle = opendir( CONFIG_DIR );
while ($file = readdir($handle)) {
    if (!is_file(CONFIG_DIR . $file) || !is_readable(CONFIG_DIR . $file)) continue;
    
    // Retrieve and sanitize the text
    $text = file_get_contents( CONFIG_DIR . $file );
    $text = preg_replace("/[[:blank:]\r\n]/", '', $text);
    
    // Analyze frequency information for the text
    $results = array();
    for ($n = 1; $result = frequency($text, $n); $n++) {
        $results[$n] = $result;
    }
    
    // Add our findings to the result set
    $files[$file] = array($results, $text);
    $maxDepth = max($maxDepth, $n);
}
closedir( $handle );


// Display the results
?>

<table cellspacing="0" cellpadding="2" border="0">
<tr>
<?php foreach ($files as $file => $_): ?>
    <th align="left"><?php echo $file ?></th>
<?php endforeach ?>
</tr>
<?php for ($depth = 1; $depth < $maxDepth; $depth++): ?>
    <tr>
        <td colspan="<?php echo sizeof($files) ?>">
            Analysis of <strong><?php echo get_canonical( $depth ) ?></strong>
        </td>
    </tr>
    <tr>
    <?php foreach ($files as $_ => $data): ?>
    <?php list ($results, $text) = $data ?>
    <?php $result = isset($results[$depth]) ? $results[$depth] : array() ?>
        <td valign="top">
            <?php
            echo '<pre>';
            foreach ($result as $ngram => $freq) {
                $isLargest = is_largest_substr(@$results[$depth + 1], $ngram);
                
                if (!CONFIG_LARGEST_ONLY || $isLargest) {
                    echo str_pad($ngram, $depth) . '  ';
                    echo str_pad($freq, 3) . '  ';
                    echo number_format($freq * $depth / strlen($text), 3) . ' ';
                    echo $isLargest ? '*' : ' ';
                    echo str_repeat(' ', 4) . "\n";
                }
                
            }
            echo '</pre>';
            ?>
        </td>
    <?php endforeach ?>
    </tr>
    <tr><td colspan="<?php echo sizeof($files) ?>">&nbsp;</td></tr>
<?php endfor ?>
</table><br />
Complete.

<?php


/**
 * Generate a mapping of all n-grams size $n to their frequency in $text.
 *
 * @param string $text The text to analyze
 * @param int $n The size of n-grams to check
 * @return array A mapping of n-grams to frequencies
 */
function frequency( $text, $n = 1 ) {
    $result = array();
    
    // Build a frequency table for all possible n-grams
    for ($i = 0; $i < strlen($text) - $n + 1; $i++) {
        $ngram = substr($text, $i, $n);
        if (!isset($result[$ngram])) {
            $freq = substr_count($text, $ngram);
        
            // Discard n-grams with a frequency less than 1
            if ($freq <= 1 && $n != 1) continue;
        
            $result[$ngram] = $freq;
        }
    }
    
    // For $n = 1: Include 0 frequencies
    if ($n == 1) {
        for ($i = ord('A'); $i <= ord('Z'); $i++) {
            if (!isset($result[chr($i)])) {
                $result[chr($i)] = 0;
            }
        }
    }
    
    // Sort results in descending order by frequency, then alphabetically
    array_multisort(
        array_values($result), SORT_DESC,
        array_keys($result), SORT_ASC,
        $result
    );
    
    return $result;
}

/**
 * Check the result set to see if the string is the maximum length (if we're
 * checking an n-gram of size 13, make sure that the string is not contained in
 * any n-grams of size 14, with a frequency larger than 1.)
 *
 * @param array $haystack The next result set
 * @param string $needle The string to check
 * @return boolean Returns TRUE if the needle is contained in the next result
 *         set; FALSE otherwise.
 */
function is_largest_substr( $haystack, $needle ) {
    foreach ((array) $haystack as $check => $_) {
        if (stristr($check, $needle)) return false;
    }
    return true;
}

/**
 * Return the canonical name for the n-gram
 *
 * @param int $n the size of the n-gram
 * @return string the canonical name for the n-gram
 */
function get_canonical( $n ) {
    switch ($n) {
        case 1: return 'unigrams';
        case 2: return 'bigrams';
        case 3: return 'trigrams';
        case 4: return 'quadrigrams';
    }
    return "$n-grams";
}
