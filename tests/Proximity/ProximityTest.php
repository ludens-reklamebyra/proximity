<?php

namespace Proximity;

use Proximity\Tools\Distances;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7;

class ProximityTest extends \PHPUnit_Framework_TestCase {
    private $dummyData = array(
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

    public function testSearch() {
        $proximity = new Proximity($this->dummyData);

        /**
         * Set up guzzle mocking
         */
        $mockData = array(
            Psr7\stream_for(fopen('./tests/Proximity/mock/oslo.json', 'r+')),
            Psr7\stream_for(fopen('./tests/Proximity/mock/rogneveien-21.json', 'r+')),
            Psr7\stream_for(fopen('./tests/Proximity/mock/zero-results.json', 'r+'))
        );

        $mock = new MockHandler(array(
            new Psr7\Response(200, ['Content-Type' => 'application/json'], $mockData[0]),
            new Psr7\Response(200, ['Content-Type' => 'application/json'], $mockData[1]),
            new Psr7\Response(200, ['Content-Type' => 'application/json'], $mockData[2])
        ));

        $handler = HandlerStack::create($mock);
        $proximity->http = new Client(array('handler' => $handler));
        /**
         * Finished setting up guzzle mocking
         */

        $proximitySearch = $proximity->search('oslo');
        $this->assertEquals(2, $proximitySearch[0]['id']);
        $proximitySearch = $proximity->search('rogneveien 21');
        $this->assertEquals(
            'Multiple matches found',
            $proximitySearch['returnMessage']
        );
        $this->assertEquals(
            'Rogneveien 21, 3158 Andebu, Norge',
            $proximitySearch['matches'][2]['address']
        );
        $proximitySearch = $proximity->search('hoytaballon');
        $this->assertEquals('NO HITS', $proximitySearch['returnMessage']);
    }

    public function testGetElements() {
        $proximity = new Proximity($this->dummyData);

        /**
         * Set up guzzle mocking
         */
        $mockData = array(
            Psr7\stream_for(fopen('./tests/Proximity/mock/oslo.json', 'r+'))
        );

        $mock = new MockHandler(array(
            new Psr7\Response(200, ['Content-Type' => 'application/json'], $mockData[0])
        ));

        $handler = HandlerStack::create($mock);
        $proximity->http = new Client(array('handler' => $handler));
        /**
         * Finished setting up guzzle mocking
         */

        $proximity->search('oslo');
        $elements = $proximity->getElements();
        $this->assertEquals(4, count($elements));
        $elements = $proximity->getElements();
        $this->assertEquals(2, $elements[0]['id']);
        $elements = $proximity->getElements(SORT_DESC);
        $this->assertEquals(5, $elements[0]['id']);
    }

    public function testLocation() {
        $proximity = new Proximity($this->dummyData);

        /**
         * Set up guzzle mocking
         */
        $mockData = array(
            Psr7\stream_for(fopen('./tests/Proximity/mock/rogneveien-21-risor.json', 'r+'))
        );

        $mock = new MockHandler(array(
            new Psr7\Response(200, ['Content-Type' => 'application/json'], $mockData[0])
        ));

        $handler = HandlerStack::create($mock);
        $proximity->http = new Client(array('handler' => $handler));
        /**
         * Finished setting up guzzle mocking
         */

        $proximity->search('rogneveien 21 risør');
        $this->assertEquals(9.227468, $proximity->location['longitude']);
        $this->assertEquals(58.7150673, $proximity->location['latitude']);
        $this->assertEquals(
            'Rogneveien 21, 4950 Risør, Norge',
            $proximity->location['address']
        );
    }

    public function testOptions() {
        $proximity = new Proximity($this->dummyData);

        $options = $proximity->getOptions();
        $this->assertEquals('no', $options['language']);

        $proximity = new Proximity($this->dummyData, array(
            'language' => 'en'
        ));

        $options = $proximity->getOptions();
        $this->assertEquals('en', $options['language']);
    }
}
