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
