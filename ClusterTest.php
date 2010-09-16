<?php

require_once 'Clusterer.php';

class ClusterTest extends PHPUnit_Framework_TestCase
{

    public function testClusterReturnsEmptyWithNoPairs()
    {
        $pairs = array();
        $this->assertEquals(array(), Clusterer::cluster($pairs));
    }

    /**
     * @dataProvider _clustersDataProvider
     */
    public function testClustererClustersItemsCorrectly($pairs, $expected)
    {
        $pairs = array(
            array(1,    2),
            array(2,    3),
            array(4,    5),
        );
        $this->assertEquals(
            array( array(1, 2, 3), array(4, 5) ),
            Clusterer::cluster($pairs)
        );
    }

    public function _clustersDataProvider()
    {
        $retVal = array();

        $test = array(
            array(
                array(1, 2),
                array(2, 3),
                array(4, 5),
            ),
            array(
                array(1, 2, 3),
                array(4, 5)
            )
        );
        array_push($retVal, $test);

        $test = array(
            array(
                array(1, 2),
                array(1, 3),
                array(1, 4),
                array(5, 6),
                array(7, 6),
                array(8, 9),
            ),
            array(
                array(1, 2, 3, 4),
                array(5, 6, 7),
                array(8, 9),
            )
        );
        array_push($retVal, $test);

        return $retVal;
    }

}