<?php

$w = 256;
$h = 256;

$chunkx = $_GET['x'];
$chunky = $_GET['y'];
$json = $_GET['json'];

$data = json_decode(utf8_encode($json), true);

$world = world_new();
world_set_chunksize($world, $w, $h);

$nms = array();
for($i = 0; $i < sizeof($data['noisemaps']); $i++) {
    $nmdata = $data['noisemaps'][$i];
    if($nmdata['gridw'] != "") {
        $nm = world_add_rand_nm($world);
        rand_nm_set_seed($nm, "" . md5($nmdata['seed']));
        rand_nm_set_gridsize($nm, $nmdata['gridw'], $nmdata['gridh']);
        $nms[$i] = $nm;
    } else {
        $nm = world_add_comb_nm($world);
        for($j = 0; $j < sizeof($nmdata['combinations']); $j++) {
            if($data['noisemaps'][$nmdata['combinations'][$j]]['gridw'] != "") {
                comb_nm_add_rand_nm($nm, $nms[$nmdata['combinations'][$j]], $nmdata['factors'][$j]);
            } else {
                comb_nm_add_comb_nm($nm, $nms[$nmdata['combinations'][$j]], $nmdata['factors'][$j]);
            }
        }
        $nms[$i] = $nm;
    }
}

$im = imagecreatetruecolor($w, $h);

$cols = array();
$tdefs = array();
for($i = 0; $i < sizeof($data['tiledefs']); $i++) {
    $tddata = $data['tiledefs'][$i];
    $td = world_add_tiledef($world);
    for($j = 0; $j < sizeof($tddata['constraintmaps']); $j++) {
        if($data['noisemaps'][$tddata['constraintmaps'][$j]]['gridw'] != "") {
            tiledef_add_rand_nm_constraint($td, $nms[$tddata['constraintmaps'][$j]], $tddata['constrainttypes'][$j], $tddata['constraintvals'][$j]);
        } else {
            tiledef_add_comb_nm_constraint($td, $nms[$tddata['constraintmaps'][$j]], $tddata['constrainttypes'][$j], $tddata['constraintvals'][$j]);
        }
    }

    $cols[tiledef_get_id($td)] = imagecolorallocate($im, $tddata['red'], $tddata['green'], $tddata['blue']);

    $tdefs[$i] = $td;
}

world_generate($world, $chunkx, $chunky);

for($y = 0; $y < $h; $y++) {
    for($x = 0; $x < $w; $x++) {
        imagesetpixel($im, $x, $y, $cols[world_get_tile($world, $x, $y)]);
    }
}

world_delete($world);

header('Content-Type: image/png');
imagepng($im);

?>
