<?php

namespace Petkopara\MultiSearchBundle\Tests;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\IndexedReader;
use Doctrine\Common\Cache\ArrayCache;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Doctrine\ORM\Tools\SchemaTool;
use Petkopara\MultiSearchBundle\Condition\EntityConditionBuilder;

/**
 * Test of EntityCondition class
 *
 * @author Petkov Petkov <petkopara@gmail.com>
 */
class EntityConditionTest extends \PHPUnit_Framework_TestCase
{

    protected static $em;

    /**
     * @return EntityManager
     */
    protected static function getEntityManager()
    {
        if (null === self::$em) {
            // 'path' is commented out but can be very helpful
            // if used instead of 'memory' for debugging
            $connection = array(
                'driver' => 'pdo_sqlite',
                'memory' => true,
//				'path' => 'database.sqlite',
            );

            $cache = new ArrayCache;
            $config = new Configuration();
            $config->setMetadataCacheImpl($cache);
            $config->setQueryCacheImpl($cache);
            $config->setResultCacheImpl($cache);
            $config->setProxyDir(sys_get_temp_dir());
            $config->setProxyNamespace('DoctrineProxies');
            $config->setAutoGenerateProxyClasses(true);
            $config->setMetadataDriverImpl(new AnnotationDriver(
                    new IndexedReader(new AnnotationReader()), __DIR__
            ));

//			$config->setSQLLogger(new EchoSQLLogger());


            self::$em = EntityManager::create($connection, $config);

            $schemaTool = new SchemaTool(self::$em);

            $cmf = self::$em->getMetadataFactory();
            $classes = $cmf->getAllMetadata();

            $schemaTool->createSchema($classes);
        }

        return static::$em;
    }

    public function testBuilder()
    {
        $queryBuilder = self::getEntityManager()->createQueryBuilder();
        $entityName = __NAMESPACE__ . '\\Post';

        $queryBuilder->from($entityName, 'p');
        $searchTerm = 'search';
        $searchFields = array('name');
        $comparisonType = 'wildcard';

        $conditionBuilder = new EntityConditionBuilder($queryBuilder, $entityName, $searchTerm, $searchFields, $comparisonType);


        $resultQuery = $conditionBuilder->getQueryBuilderWithConditions();

        $this->assertEquals(
                "SELECT p FROM $entityName p WHERE p.id IN(SELECT b.id FROM Petkopara\MultiSearchBundle\Tests\Post b WHERE b.name LIKE ?1)", $resultQuery->getDQL()
        );
    }

}
