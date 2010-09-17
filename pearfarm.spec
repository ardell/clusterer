<?php

$spec = Pearfarm_PackageSpec::create(array(Pearfarm_PackageSpec::OPT_BASEDIR => dirname(__FILE__)))
             ->setName('Clusterer')
             ->setChannel('ardell.pearfarm.org')
             ->setSummary('A pair clustering algorithm in php.')
             ->setDescription('A pair clustering algorithm in php.')
             ->setReleaseVersion('0.0.1')
             ->setReleaseStability('alpha')
             ->setApiVersion('0.0.1')
             ->setApiStability('alpha')
             ->setLicense(Pearfarm_PackageSpec::LICENSE_MIT)
             ->setNotes('Initial release.')
             ->addMaintainer('lead', 'Jason Ardell', 'ardell', 'foo@bar.com')
             ->addGitFiles();