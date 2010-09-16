<?php

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
