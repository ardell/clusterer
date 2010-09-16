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

class Clusterer
{

    /**
     * The Clusterer!
     * @param pairs array of 2 value arrays, eg array( array(a, b), array(c, d) ...)
     * @return array An array of buckets: array( array(a, b, c), array(d, e) ...)
     */
    public static function cluster($pairs = array(), $buckets = array())
    {
        // If pairs is empty, return buckets
        if (empty($pairs))
        {
            return $buckets;
        }

        // Pop off the first item (changes pairs)
        $pair = array_shift($pairs);
        list($a, $b) = $pair;

        // If there is a bucket that contains a, then
        foreach ($buckets as $key => $bucket)
        {
            if (in_array($a, $bucket))
            {
                // Add b to that bucket
                $buckets[$key][] = $b;

                // return cluster($pairs, $buckets)
                return self::cluster($pairs, $buckets);
            }
        }

        // If there is a bucket that contains b, then
        foreach ($buckets as $key => $bucket)
        {
            if (in_array($b, $bucket))
            {
                // Add b to that bucket
                $buckets[$key][] = $a;

                // return cluster($pairs, $buckets)
                return self::cluster($pairs, $buckets);
            }
        }

        // Create a new bucket
        // Add a and b to the bucket
        array_push($buckets, array($a, $b));
        return self::cluster($pairs, $buckets);
    }

}
