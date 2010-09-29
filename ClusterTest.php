<?php

/**
 * Copyright (c) 2010 Jason Ardell (http://github.com/ardell)
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 * 
 * @category   Clusterer
 * @package    Clusterer
 * @copyright  Copyright (c) 2010 Jason Ardell (http://github.com/ardell)
 * @license    http://www.opensource.org/licenses/mit-license.php MIT License
 * @author     Jason Ardell
 */

require_once 'Clusterer.php';

class ExposePrivatesClusterer extends Clusterer
{
    public function __construct()
    {
        return parent::__construct(array(), 'strcmp');
    }
    public function testPairClusterer($pairs)
    {
        return $this->doCluster($pairs);
    }
}

class ClusterTest extends PHPUnit_Framework_TestCase
{

    public function testClusterReturnsEmptyWithNoPairs()
    {
        $c = new ExposePrivatesClusterer;
        $this->assertEquals(array(), $c->testPairClusterer(array()));
    }

    /**
     * @dataProvider _clustersDataProvider
     */
    public function testClustererClustersItemsCorrectly($pairs, $expected)
    {
        $c = new ExposePrivatesClusterer;
        $actual = $c->testPairClusterer($pairs);
        $this->assertEquals(
            $expected,
            $actual,
            'Expected: ' . print_r($expected, true) . 'Got: ' . print_r($actual, true)
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

        $test = array(
            array(
                array(1, 2),
                array(3, 4),
                array(2, 3),
            ),
            array(
                array(1, 2, 3, 4)
            )
        );
        array_push($retVal, $test);

        return $retVal;
    }

    public function testClustererDetectsDuplicates()
    {
        $pairs = array(
            array(1,    2),
            array(2,    3),
            array(3,    1),
        );
        $expected = array( array(1, 2, 3) );

        $c = new ExposePrivatesClusterer;
        $actual = $c->testPairClusterer($pairs);
        $this->assertEquals(
            $expected,
            $actual,
            'Expected: ' . print_r($expected, true) . 'Got: ' . print_r($actual, true)
        );
    }

    public function testClustererDetectsDuplicateItems()
    {
        $pairs = array(
            array(1, 1),
        );
        $expected = array( array(1) );

        $c = new ExposePrivatesClusterer;
        $actual = $c->testPairClusterer($pairs);
        $this->assertEquals(
            $expected,
            $actual,
            'Expected: ' . print_r($expected, true) . 'Got: ' . print_r($actual, true)
        );
    }

    /**
     * Need a test case with lots of items so
     * we can push recursion to its limits.
     * Maybe we'll need to convert to iteration?
     *
     * @dataProvider _scalabilityDataProvider
     */
    public function testClustererScales($itemsInCluster = 20)
    {
        $pairs = array();
        $expected = array();
        for ($i = 1; $i <= $itemsInCluster - 1; $i++)
        {
            array_push($pairs, array($i, $i + 1));
            array_push($expected, $i);
            array_push($expected, $i + 1);
        };
        $expected = array(array_values(array_unique($expected)));

        $c = new ExposePrivatesClusterer;
        $this->assertEquals(
            $expected,
            $c->testPairClusterer($pairs)
        );
    }

    public function _scalabilityDataProvider()
    {
        return array(
            array(10),
            array(20),
            array(30),
            array(40),
            array(50),
            array(60),
            array(70),
            array(80),
            array(90),
            array(100),
            array(200),
            array(500),
            array(1000),
            array(2000),
            array(5000),
        );
    }

}
