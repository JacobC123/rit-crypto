<?php
/**
 * Given an S-box, create a linear approximation table
 *
 * (c) Jacob Chafik <jacob@achafik.com>
 */

// Define the S-box
$sBox = array(
    // Book (S1_0)
    'E', '4', 'D', '1', '2', 'F', 'B', '8',
    '3', 'A', '6', 'C', '5', '9', '0', '7' //*/
    
    /*/ Problem 5
    '8', '4', '2', '1', 'C', '6', '3', 'D',
    'A', '5', 'E', '7', 'F', 'B', '9', '0' //*/
);

// Display S-box
?>
<style>
table { border-collapse: collapse }
td { padding: 0px 4px; text-align: center; }
.heading, .heading td { background-color: #eee; }
</style>

S-box:<br />
<table cellpadding="0" cellspacing="0" border="1">
    <tr class="heading">
        <?php foreach (array_keys($sBox) as $v): ?>
            <td><?php echo $v ?></td>
        <?php endforeach ?>
    </tr>
    <tr>
        <td><?php echo implode('</td><td>', $sBox) ?></td>
    </tr>
</table>
<br />
Random variables defined by S-box:<br />
<table cellpadding="0" cellspacing="0" border="1">
    <tr class="heading">
        <td>X1</td><td>X2</td><td>X3</td><td>X4</td>
        <td>&nbsp;</td>
        <td>Y1</td><td>Y2</td><td>Y3</td><td>Y4</td>
        <td>Y2 XOR Y3</td>
    </tr>
    <?php
    for ($i = 0; $i < sizeof($sBox); $i++):
        $x = str_split(str_pad(decbin($i), 4, 0, STR_PAD_LEFT));
        $y = str_split(str_pad(decbin(hexdec($sBox[$i])), 4, 0, STR_PAD_LEFT));
        ?>
        <tr>
            <td><?php echo implode($x, '</td><td>') ?></td>
            <td>&nbsp;</td>
            <td><?php echo implode($y, '</td><td>') ?></td>
            <td><?php echo (int) ($y[1] xor $y[2] == 1) ?></td>
        </tr>
    <?php endfor ?>
</table>
<br />
Linear approximation table:<br />
<table cellpadding="0" cellspacing="0" border="1">
    <tr class="heading">
        <td>&nbsp;</td>
        <?php foreach (array_keys($sBox) as $v): ?>
            <td><?php echo $v ?></td>
        <?php endforeach ?>
    </tr>
    <?php for ($a = 0; $a < sizeof($sBox); $a++): ?>
        <tr>
            <td class="heading"><?php echo $a ?></td>
            <?php for ($b = 0; $b < sizeof($sBox); $b++): ?>
                <td><?php echo n_L($a, $b) ?></td>
            <?php endfor ?>
        </tr>
    <?php endfor ?>
</table>
<?php

function n_L($a, $b) {
    global $sBox;
    $result = 0;
    
    // Determine which variables are flagged
    $is = str_pad(decbin($a), 4, 0, STR_PAD_LEFT);
    $os = str_pad(decbin($b), 4, 0, STR_PAD_LEFT);
    
    // Iterate over the S-box
    for ($i = 0; $i < 16; $i++) {
        $x = str_pad(decbin($i), 4, 0, STR_PAD_LEFT);
        $y = str_pad(decbin(hexdec($sBox[$i])), 4, 0, STR_PAD_LEFT);
        
        // xor all flagged variables
        $xor = 0;
        for ($p = 0; $p < strlen($x); $p++) $xor ^= ($is[$p] & $x[$p]);
        for ($p = 0; $p < strlen($y); $p++) $xor ^= ($os[$p] & $y[$p]);
        
        // Increment if result is zero
        $result += $xor == 0;
    }
    
    return $result;
}
