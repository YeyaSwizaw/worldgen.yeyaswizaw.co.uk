<?php

$seed = $_GET['seed'];
$chunkx = $_GET['x'];
$chunky = $_GET['y'];

$w = 256;
$h = 256;

mt_srand(crc32($seed));

$world = world_new();
world_set_chunksize($world, $w, $h);

$nm1 = world_add_rand_nm($world);
rand_nm_set_seed($nm1, "" . mt_rand());
rand_nm_set_gridsize($nm1, 0.005, 0.005);

$nm2 = world_add_rand_nm($world);
rand_nm_set_seed($nm2, "" . mt_rand());
rand_nm_set_gridsize($nm2, 0.002, 0.002);

$nm = world_add_comb_nm($world);
comb_nm_add_rand_nm($nm, $nm1, 10);
comb_nm_add_rand_nm($nm, $nm2, 20);

$td1 = world_add_tiledef($world);
$td2 = world_add_tiledef($world);

tiledef_add_comb_nm_constraint($td1, $nm, CONSTRAINT_LT, 0);

world_generate($world, $chunkx, $chunky);

$im = imagecreatetruecolor($w, $h);
$green = imagecolorallocate($im, 0, 255, 0);
$blue = imagecolorallocate($im, 0, 0, 255);

for($y = 0; $y < $h; $y++) {
    for($x = 0; $x < $w; $x++) {
        if(world_get_tile($world, $x, $y) == tiledef_get_id($td1)) {
            imagesetpixel($im, $x, $y, $blue);
        } else {
            imagesetpixel($im, $x, $y, $green);
        }
    }
}

world_delete($world);

header('Content-Type: image/png');
imagepng($im);

?>
