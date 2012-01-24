<?php
/**
 * Given an S-box, create a linear approximation table
 *
 * (c) Jacob Chafik <jacob@achafik.com>
 */

// Define the S-box
$sBoxes = array(
    /*/ Problem 5
    'P5' => array(
        array('8', '4', '2', '1', 'C', '6', '3', 'D', 'A', '5', 'E', '7', 'F', 'B', '9', '0')
    ), //*/
    
    // S-box 1
    'S1' => array(
        array('E', '4', 'D', '1', '2', 'F', 'B', '8', '3', 'A', '6', 'C', '5', '9', '0', '7'),
        array('0', 'F', '7', '4', 'E', '2', 'D', '1', 'A', '6', 'C', 'B', '9', '5', '3', '8'),
        array('4', '1', 'E', '8', 'D', '6', '2', 'B', 'F', 'C', '9', '7', '3', 'A', '5', '0'),
        array('F', 'C', '8', '2', '4', '9', '1', '7', '5', 'B', '3', 'E', 'A', '0', '6', 'D')
    ),

    // S-box 2
    'S2' => array(
        array('F', '1', '8', 'E', '6', 'B', '3', '4', '9', '7', '2', 'D', 'C', '0', '5', 'A'),
        array('3', 'D', '4', '7', 'F', '2', '8', 'E', 'C', '0', '1', 'A', '6', '9', 'B', '5'),
        array('0', 'E', '7', 'B', 'A', '4', 'D', '1', '5', '8', 'C', '6', '9', '3', '2', 'F'),
        array('D', '8', 'A', '1', '3', 'F', '4', '2', 'B', '6', '7', 'C', '0', '5', 'E', '9')
    ),

    // S-box 3
    'S3' => array(
        array('A', '0', '9', 'E', '6', '3', 'F', '5', '1', 'D', 'C', '7', 'B', '4', '2', '8'),
        array('D', '7', '0', '9', '3', '4', '6', 'A', '2', '8', '5', 'E', 'C', 'B', 'F', '1'),
        array('D', '6', '4', '9', '8', 'F', '3', '0', 'B', '1', '2', 'C', '5', 'A', 'E', '7'),
        array('1', 'A', 'D', '0', '6', '9', '8', '7', '4', 'F', 'E', '3', 'B', '5', '2', 'C')
    ),

    // S-box 4
    'S4' => array(
        array('7', 'D', 'E', '3', '0', '6', '9', 'A', '1', '2', '8', '5', 'B', 'C', '4', 'F'),
        array('D', '8', 'B', '5', '6', 'F', '0', '3', '4', '7', '2', 'C', '1', 'A', 'E', '9'),
        array('A', '6', '9', '0', 'C', 'B', '7', 'D', 'F', '1', '3', 'E', '5', '2', '8', '4'),
        array('3', 'F', '0', '6', 'A', '1', 'D', '8', '9', '4', '5', 'B', 'C', '7', '2', 'E')
    ),

    // S-box 5
    'S5' => array(
        array('2', 'C', '4', '1', '7', 'A', 'B', '6', '8', '5', '3', 'F', 'D', '0', 'E', '9'),
        array('E', 'B', '2', 'C', '4', '7', 'D', '1', '5', '0', 'F', 'A', '3', '9', '8', '6'),
        array('4', '2', '1', 'B', 'A', 'D', '7', '8', 'F', '9', 'C', '5', '6', '3', '0', 'E'),
        array('B', '8', 'C', '7', '1', 'E', '2', 'D', '6', 'F', '0', '9', 'A', '4', '5', '3')
    ),

    // S-box 6
    'S6' => array(
        array('C', '1', 'A', 'F', '9', '2', '6', '8', '0', 'D', '3', '4', 'E', '7', '5', 'B'),
        array('A', 'F', '4', '2', '7', 'C', '9', '5', '6', '1', 'D', 'E', '0', 'B', '3', '8'),
        array('9', 'E', 'F', '5', '2', '8', 'C', '3', '7', '0', '4', 'A', '1', 'D', 'B', '6'),
        array('4', '3', '2', 'C', '9', '5', 'F', 'A', 'B', 'E', '1', '7', '6', '0', '8', 'D')
    ),

    // S-box 7
    'S7' => array(
        array('4', 'B', '2', 'E', 'F', '0', '8', 'D', '3', 'C', '9', '7', '5', 'A', '6', '1'),
        array('D', '0', 'B', '7', '4', '9', '1', 'A', 'E', '3', '5', 'C', '2', 'F', '8', '6'),
        array('1', '4', 'B', 'D', 'C', '3', '7', 'E', 'A', 'F', '6', '8', '0', '5', '9', '2'),
        array('6', 'B', 'D', '8', '1', '4', 'A', '7', '9', '5', '0', 'F', 'E', '2', '3', 'C')
    ),

    // S-box 8
    'S8' => array(
        array('D', '2', '8', '4', '6', 'F', 'B', '1', 'A', '9', '3', 'E', '5', '0', 'C', '7'),
        array('1', 'F', 'D', '8', 'A', '3', '7', '4', 'C', '5', '6', 'B', '0', 'E', '9', '2'),
        array('7', 'B', '4', '1', '9', 'C', 'E', '2', '0', '6', 'A', 'D', 'F', '3', '5', '8'),
        array('2', '1', 'E', '7', '4', 'A', '8', 'D', 'F', 'C', '9', '0', '3', '5', '6', 'B')
    )
);

?>
Compute bias of DES S-boxes:<br />
<table cellpadding="0" cellspacing="0" border="1">
    <tr class="heading">
        <td>&nbsp;</td>
        <td>1</td><td>2</td><td>3</td><td>4</td>
        <td><strong>Total</strong>
    </tr>
    <?php foreach ($sBoxes as $title => $v): $total = 0 ?>
    <tr>
        <td><?php echo $title ?></td>
        <?php foreach ($v as $k => $sBox): ?>
            <td><?php echo $total += n_L(4, 9) - 8 ?></td>
        <?php endforeach ?>
        <td><strong><?php echo $total . '/' . 64 ?></strong></td>
    </tr>
    <?php endforeach ?>
</table><br /><br />
<?php

// Select an S-box
$sBox = $sBoxes['S1'][0];

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
        <td>X2 xor Y1 xor Y4</td>
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
            <td><?php echo (int) ($x[1] xor $y[0] xor $y[3]) ?></td>
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
