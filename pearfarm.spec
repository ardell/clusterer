<?php

$spec = Pearfarm_PackageSpec::create(array(Pearfarm_PackageSpec::OPT_BASEDIR => dirname(__FILE__)))
             ->setName('Clusterer')
             ->setChannel('ardell.pearfarm.org')
             ->setSummary('A pair clustering algorithm in php.')
             ->setDescription('A pair clustering algorithm in php.')
             ->setReleaseVersion('0.0.3')
             ->setReleaseStability('alpha')
             ->setApiVersion('0.0.3')
             ->setApiStability('alpha')
             ->setLicense(Pearfarm_PackageSpec::LICENSE_MIT)
             ->setNotes('Converted from a recursive to an iterative algorithm.')
             ->addMaintainer('lead', 'Jason Ardell', 'ardell', 'ardell@gmail.com')
             ->addGitFiles();
