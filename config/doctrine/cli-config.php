<?php
use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Doctrine\ORM\Tools\Setup;

$isDevMode = true;
//$config = Setup::createXMLMetadataConfiguration(array(__DIR__."/xml"), $isDevMode);
//$config = Setup::createAnnotationMetadataConfiguration(array(__DIR__."/entities"), $isDevMode);
$config = Setup::createYAMLMetadataConfiguration(array(__DIR__."/yaml"), $isDevMode);

// database configuration parameters
$conn = array(
    'driver' => 'pdo_mysql',
    'host' => '192.168.1.240',
    'dbname' => 'cl_portal',
    'user' => 'cailang',
    'password' => '123456'
);


// obtaining the entity manager
$entityManager = \Doctrine\ORM\EntityManager::create($conn, $config);

return ConsoleRunner::createHelperSet($entityManager);
