<?php
require __DIR__ . '/../vendor/autoload.php';

use Proximity\Proximity;

$dummyData = array(
    [
        'id' => 2,
        'latitude' => 59.878592,
        'longitude' => 10.807647
    ],
    [
        'id' => 4,
        'latitude' => 59.122701,
        'longitude' => 11.388684
    ],
    [
        'id' => 3,
        'latitude' => 62.596278,
        'longitude' => 6.443401
    ],
    [
        'id' => 5,
        'latitude' => 70.636660,
        'longitude' => 29.724778
    ],
);

$proximity = new Proximity($dummyData);
var_dump($proximity->search('risør', SORT_DESC));
