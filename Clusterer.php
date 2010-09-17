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
    protected $inputData;
    protected $clusterCompareF;
    protected $clusters;
    protected $hashClusters = false;
    protected $clusterMetadata = array();

    const OPT_ID_GENERATOR_METHOD       = 'idGeneratorMethod';

    /**
     * Instantiate a Clusterer for the specified data.
     *
     * @param array An array of objects run the clustering algorithm on.
     * @param callback A callback function of prototype (bool) areAandBinSameCluster($a, $b)
     * @param array Options:
     *              OPT_ID_GENERATOR_METHOD => the method to call on the callback to get the unique ID for the object.
     *                                         NOTE: this is required for hashClusters
     *                                         NOTE: you should supply this anyway, as it reduces the algorithm's execution time by 50%.
     */
    public function __construct($inputData, $clusterCompareF, $options = array())
    {
        if (!(is_array($inputData) or $inputData instanceof ArrayObject)) throw new Exception("Array or ArrayObject data required.");
        if (!is_callable($clusterCompareF)) throw new Exception("Valid cluster comparator callback.");

        $this->inputData = $inputData;
        $this->clusterCompareF = $clusterCompareF;
        $options = array_merge(array(
                                                        self::OPT_ID_GENERATOR_METHOD   => NULL,
        ), $options);
        // unroll options
        $this->idGeneratorMethod = $options[self::OPT_ID_GENERATOR_METHOD];

        $this->clusters = array();
    }

    /**
     * Fluent constructor.
     *
     * @return object Clusterer
     */
    public static function create($inputData, $clusterCompareF, $options = array())
    {
        return new Clusterer($inputData, $clusterCompareF, $options);;
    }

    // PAIRS GENERATION
    /**
     * Given an array of data, generate "pairs" data of all items that are in the same cluster.
     *
     * Typically this information is then 
     *
     * @return array An array of pairs of objects which satisfy the "clusterCompareF" criteria.
     *               array(
     *                  array(a, b)
     *                  array(b, c)
     *                  array(d, e)
     *               )
     */
    public function generatePairs()
    {
        $clusterPairs = array();
        $checkedPairs = array();
        $i = 0;
        foreach ($this->inputData as $a) {
            foreach ($this->inputData as $b) {
                // optimization (saves n)
                if ($a === $b) continue;
                // optimization (saves 50%), if a+b are in cluster, then b+a are as well (or not) by definition
                if ($this->idGeneratorMethod)
                {
                    $hash = $this->normalizedPairHashKey($a, $b, $this->idGeneratorMethod);
                    if (isset($checkedPairs[$hash])) continue;
                    $checkedPairs[$hash] = true;
                }

                // see if a & b are in same cluster
                $areInSameCluster = call_user_func($this->clusterCompareF, $a, $b);
                if ($areInSameCluster)
                {
                    $clusterPairs[] = array($a, $b);
                }
                $i++;
            }
        }
        return $clusterPairs;
    }

    /**
     * Generate a hash key for pair(a, b) which will return the same whether or not it's called with (a,b) or (b,a).
     *
     * @param object A
     * @param object B
     * @return string A hash key representing the a,b pair.
     */
    private function normalizedPairHashKey($a, $b)
    {
        $f = $this->idGeneratorMethod;
        return min($a->$f(), $b->$f()) . "-" . max($a->$f(), $b->$f());
    }
    // END PAIRS GENERATION

    // CLUSTERING ALGORITHM
    /**
     * Run the clustering algorithm.
     *
     * Internally this will generate the pairs and then combine all pairs into appropriate buckets.
     *
     * @return array An array of arrays, with each inner array containing the objects that belong to the same cluster.
     */
    public function cluster()
    {
        $pairs = $this->generatePairs($this->inputData);
        $this->clusters = $this->recursiveCluster($pairs);
        return $this->clusters;
    }

    /**
     * Converts sets of cluster pairs into clusters with all objects in matching pairs.
     * @param pairs array of 2 value arrays, eg array( array(a, b), array(c, d) ...)
     * @return array An array of buckets: array( array(a, b, c), array(d, e) ...)
     */
    protected function recursiveCluster($pairs = array(), $buckets = array())
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
                if (!in_array($b, $bucket))
                {
                    // Add b to that bucket
                    $buckets[$key][] = $b;
                }

                // return cluster($pairs, $buckets)
                return $this->recursiveCluster($pairs, $buckets);
            }
        }

        // If there is a bucket that contains b, then
        foreach ($buckets as $key => $bucket)
        {
            if (in_array($b, $bucket))
            {
                // Add b to that bucket
                if (!in_array($a, $bucket))
                {
                    $buckets[$key][] = $a;
                }

                // return cluster($pairs, $buckets)
                return $this->recursiveCluster($pairs, $buckets);
            }
        }

        // Create a new bucket
        // Add a and b to the bucket
        if ($a == $b)
        {
            array_push($buckets, array($a));
        } else {
            array_push($buckets, array($a, $b));
        }
        return $this->recursiveCluster($pairs, $buckets);
    }
    // END CLUSTERING ALGORITHM

    // METADATA GENERATOR
    /**
     * Generate metadata for each cluster.
     *
     * @param string The name of the metadata field as you'd like it to appear in the cluster metadata.
     * @param string The name of a method that can be called on your objects that will generate the data to be used by the metadata generator.
     * @param callback A callback function that will be run on the metadata generated for all objects in the cluster.
     *                 NOTE: You can pass in your own callback, or any method name from the {@link object ClustererAggregator} class.
     */
    public function generateClusterMetadata($metadataName, $objectAccessorF, $aggregatorF)
    {
        // look for built-ins
        switch ($aggregatorF) {
            case 'first':
                $aggregatorF = array('ClustererAggregator', 'first');
                break;
            case 'average':
                $aggregatorF = array('ClustererAggregator', 'average');
                break;
            case 'top':
                $aggregatorF = array('ClustererAggregator', 'top');
                break;
        }
        // assemble data
        for ($i = 0; $i < count($this->clusters); $i++) {
            $metadata = array();
            $cluster = $this->clusters[$i];
            foreach ($cluster as $obj) {
                if ($objectAccessorF)
                {
                    $obj = $obj->$objectAccessorF();
                }
                $metadata[] = $obj;
            }
            if ($aggregatorF)
            {
                $metadata = call_user_func($aggregatorF, $metadata);
            }
            $this->clusterMetadata[$i][$metadataName] = $metadata;
        }
    }

    /**
     * This will hash the cluster data by cluster ID, rather than just having an array of arrays (as returned by {@link Clusterer::cluster() cluster()}.
     *
     * @param string The method to call on the object to generate the ID of the cluster.
     *               Defaults to "first", which will just use the ID of the first object of each cluster.
     * @throws object Exception if you haven't supplied OPT_ID_GENERATOR_METHOD in the constructor.
     */
    public function hashClusters($idGeneratorF = 'first')
    {
        if (!$this->idGeneratorMethod) throw new Exception("hashClusters requires OPT_ID_GENERATOR_METHOD to be set.");

        $this->hashClusters = true;
        $this->generateClusterMetadata('id', $this->idGeneratorMethod, $idGeneratorF);
    }

    /**
     * Returns the clusters, augmented with metadata.
     *
     * @return array
     *         1. If you didn't call hashClusters() then this will return an array of hashes with ('items' => array(clustered objects), 'metadata' => array(metadata hash))
     *         2. If you did call hashClusters(), then this will return array( clusterId => array(metadata hash)
     *            NOTE: by default the cluster objects are *not* included in clustersWithMetadata(). If you want them, call generateClusterMetadata('objects', NULL, NULL)
     */
    public function clustersWithMetadata()
    {
        $combined = array();
        for ($i = 0; $i < count($this->clusters); $i++) {
            if ($this->hashClusters)
            {
                $combined[$this->clusterMetadata[$i]['id']] = $this->clusterMetadata[$i];
            }
            else
            {
                $combined[] = array(
                    'items'         => $this->clusters[$i],
                    'metadata'      => $this->clusterMetadata[$i]
                );
            }
        }
        return $combined;
    }
    // END METADATA GENERATOR
}

class ClustererAggregator
{
    public static function first($data)
    {
        return $data[0];
    }
    public static function average($data)
    {
        return array_sum($data) / count($data);
    }
    public static function top($data)
    {
        $histogram = array();
        foreach ($data as $d) {
            if (!isset($histogram[$d]))
            {
                $histogram[$d] = 1;
            }
            else
            {
                $histogram[$d]++;
            }
        }
        arsort($histogram);
        return key($histogram);
    }
}
