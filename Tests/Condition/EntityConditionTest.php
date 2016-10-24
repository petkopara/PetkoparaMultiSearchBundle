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
use Petkopara\MultiSearchBundle\Condition\FormConditionBuilder;
use PHPUnit_Framework_TestCase;

/**
 * Test of EntityCondition class
 *
 * @author Petkov Petkov <petkopara@gmail.com>
 */
class EntityConditionTest extends PHPUnit_Framework_TestCase
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

    public function testEntityBuilder()
    {
        $queryBuilder = self::getEntityManager()->createQueryBuilder();
        $entityName = __NAMESPACE__ . '\\Post';

        $queryBuilder->from($entityName, 'p');
        $searchTerm = 'search test';
        $searchFields = array('name');
        $comparisonType = 'wildcard';

        $conditionBuilder = new EntityConditionBuilder($queryBuilder, $entityName, $searchTerm, $searchFields, $comparisonType);


        $resultQuery = $conditionBuilder->getQueryBuilderWithConditions();

        $this->assertEquals(
                "SELECT p FROM $entityName p WHERE p.id IN(SELECT c.id FROM $entityName c WHERE c.name LIKE ?2 AND c.id IN(SELECT b.id FROM $entityName b WHERE b.name LIKE ?1))", $resultQuery->getDQL()
        );

        $this->assertEquals('%search%', $resultQuery->getParameter(1)->getValue());
        $this->assertEquals('%test%', $resultQuery->getParameter(2)->getValue());
    }

    public function testFormBuilder()
    {
        $queryBuilder = self::getEntityManager()->createQueryBuilder();
        $entityName = __NAMESPACE__ . '\\Post';

        $queryBuilder->from($entityName, 'p');

        $searchTerm = 'search test';
        $searchFields = array('name');
        $comparisonType = 'wildcard';

        $form = $this->getMockBuilder('Symfony\Component\Form\Form')
                ->disableOriginalConstructor()
                ->setMethods(array('getData', 'getConfig', 'getOption'))
                ->getMock();
        $form->expects($this->any())->method('getData')->will($this->returnValue($searchTerm));
        $form->expects($this->any())->method('getOption')
                ->with($this->logicalOr(
                                $this->equalTo('search_comparison_type'), $this->equalTo('class'), $this->equalTo('search_fields')
                ))
                ->will($this->returnCallback(function($param) use ($entityName, $comparisonType, $searchFields) {
                            if ($param === 'search_comparison_type') {
                                return $comparisonType;
                            }
                            if ($param === 'class') {
                                return $entityName;
                            }
                            if ($param === 'search_fields') {
                                return $searchFields;
                            }
                        }
        ));

        $form->expects($this->any())
                ->method($this->anything())  // all other calls return self
                ->will($this->returnSelf());


        $conditionBuilder = new FormConditionBuilder($queryBuilder, $form);

        $resultQuery = $conditionBuilder->getQueryBuilderWithConditions();

        $this->assertEquals(
                "SELECT p FROM $entityName p WHERE p.id IN(SELECT c.id FROM $entityName c WHERE c.name LIKE ?2 AND c.id IN(SELECT b.id FROM $entityName b WHERE b.name LIKE ?1))", $resultQuery->getDQL()
        );

        $this->assertEquals('%search%', $resultQuery->getParameter(1)->getValue());
        $this->assertEquals('%test%', $resultQuery->getParameter(2)->getValue());
    }

}
