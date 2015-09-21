# Proximity
Find "things" nearby, or as far away as possible, based on longitude and latitude.

## Install
```javascript
{
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/ludens-reklamebyra/proximity"
        }
    ],
    "require": {
        "ludens-reklamebyra/proximity": "v1.0.0"
    }
}
```
```
$ composer install
```

## Usage
```php
<?php
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
$proximity->search('risør', SORT_DESC);
```
