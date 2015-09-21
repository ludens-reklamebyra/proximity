# Proximity
[![Build Status](https://travis-ci.org/ludens-reklamebyra/proximity.svg?branch=master)](https://travis-ci.org/ludens-reklamebyra/proximity)

Find "things" nearby, or as far away as possible, based on longitude and latitude.

## Install
```
$ composer require ludens-reklamebyra/proximity
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

### new Proximity([elements], [options])
#### elements [array]
Each element has to include:
```javascript
{
  longitude: float,
  latitude: float
}
```

#### options [array]
```php
array(
    'language' => 'en' // Language of returned Google Maps results
)
```
