<?php

/*
 * Proximity
 * Find "things" nearby, or as far away as possible,based on longitude
 * and latitude.
 *
 * (c) Stian Bakkane <stian@ludensreklame.no>
 * https://github.com/ludens-reklamebyra/proximity
 */

namespace Proximity;

use Proximity\Tools\Distances;
use GuzzleHttp;

/**
 * Proximity
 *
 * It takes a set of long/lat coordinates and sorts them by proximity to
 * a Google Maps API searchable string
 *
 * @author Stian Bakkane <stian@ludensreklame.no>
 */
class Proximity {
    /**
     * The $elements that will be sorted.
     * Each element has to include: id, longitude, latitude
     */
    private $elements = array();

    /**
     * Options
     * language: The language of the returned Google Maps results
     */
    private $opts = array(
        'language' => 'no'
    );

    /**
     * The location data of the search() query
     */
    public $location = array(
        'longitude' => 0,
        'latitude' => 0,
        'address' => 'No found'
    );

    /**
     * @param array $elements The elements that will be sorted
     * @param array $opts     Optional parameters
     */
    public function __construct(array $elements, array $opts = array()) {
        if (isset($opts['language'])) {
            $this->opts['language'] = $opts['language'];
        }

        $this->elements = $elements;
    }

    /**
     * Takes a string (post code, address, etc), and runs the query() method,
     * and returns the $sortedElements sorted by distance
     *
     * @param string   $query                        The query string that will be sent to the Google Maps API
     * @param constant $order[SORT_ASC || SORT_DESC] The order of the returned results. By distance
     * @return $elements sorted by distance
     */
    public function search($query, $order = SORT_ASC) {
        $this->location = $this->query($query);

        if (isset($this->location['latitude'])) {
            $this->elements = $this->findElementDistances();
            return $this->getElements($order);
        } else {
            return $this->location;
        }        
    }

    /**
     * Returns the $sortedElements sorted by distance
     *
     * @param constant $order[SORT_ASC || SORT_DESC] The order of the returned results. By distance
     * @return $elements sorted by distance
     */
    public function getElements($order = SORT_ASC) {
        $elements = $this->elements;
        $distance = array();

        foreach ($elements as $k => $v) {
            $distance[$k] = $v['distance'];
        }

        array_multisort($distance, $order, $elements);
        return $elements;
    }

    /**
     * Takes a string (post code, address, etc), queries the Google Maps API,
     * and returns the $sortedElements sorted by distance
     *
     * @param string $query The query string that will be sent to the Google Maps API
     * @return $location data of the query string (longlat and address)
     */
    private function query($query) {
        $apiClient = new GuzzleHttp\Client();
        $res = $apiClient->request('GET',
            'https://maps.googleapis.com/maps/api/geocode/json?address='
                .$query
                .',norway&language='
                .$this->opts['language']);
        $data = json_decode($res->getBody());

        // Return a list of addresses if there is more than one HITS
        if (count($data->results) > 1) {
            $matches = array();

            foreach ($data->results as $result) {
                $matches[] = array(
                    'address' => $result->formatted_address,
                    'longitude' => $result->geometry->location->lng,
                    'latitude' => $result->geometry->location->lat
                );
            }

            return array(
                'returnMessage' => "Multiple matches found",
                'matches' => $matches
            );
        } else if (!$data->results) {
            return array(
                'returnMessage' => 'NO HITS'
            );
        }

        $location = $data->results[0]->geometry->location;

        // Return the location of the search query
        return array(
            "longitude" => $location->lng,
            "latitude" => $location->lat,
            "address" => $data->results[0]->formatted_address
        );
    }

    /**
     * Takes the $elements and calculates their distance from the search() query
     *
     * @return $elements with distance data
     */
    private function findElementDistances() {
        $elements = array();

        foreach ($this->elements as $element) {
            $distance = Distances::calculate($element['latitude'],
                $element['longitude'],
                $this->location['latitude'],
                $this->location['longitude'], 'K');

    		$element['distance'] = $distance;
            $elements[] = $element;
        }

        return $elements;
    }
}
