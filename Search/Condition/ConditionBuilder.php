<?php

namespace Petkopara\MultiSearchBundle\Search\Condition;

use Doctrine\ORM\EntityManager;
use Pagerfanta\Pagerfanta;

class ConditionBuilder
{

    protected $queryBuilder;
    protected $searchColumns = array();

    /**
     * Entity name
     * @var string
     */
    protected $entityName;

    /**
     * @var string
     */
    protected $idName;

    public function __construct($queryBuilder, $className)
    {
        $this->queryBuilder = $queryBuilder;
        $this->entityName = $className;
        $this->entityManager = $queryBuilder->getEntityManager();

        /** @var $metadata \Doctrine\ORM\Mapping\ClassMetadata */
        $metadata = $this->entityManager->getClassMetadata($className);

        $this->idName = $metadata->getSingleIdentifierFieldName();
        foreach ($metadata->fieldMappings as $field) {
            $this->searchColumns[] = $field['fieldName'];
        }
    }

    /**
     * @param string $searchQuery
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function search($searchQuery)
    {
        $alias = $this->queryBuilder->getRootAlias();
        $query = $this->queryBuilder
                ->select($alias);

        if ($searchQuery == '') {
            return $query;
        }

        $searchQuery = str_replace('*', '%', $searchQuery);
        $searchQueryParts = explode(' ', $searchQuery);

        $subquery = null;
        $subst = 'a';

        foreach ($searchQueryParts as $i => $searchQueryPart) {
            $qbInner = $this->entityManager->createQueryBuilder();

            $paramPosistion = $i + 1;
            ++$subst;

            $whereQuery = $query->expr()->orX();

            foreach ($this->searchColumns as $column) {
                $whereQuery->add($query->expr()->like(
                                $subst . '.' . $column, '?' . $paramPosistion
                ));
            }

            $subqueryInner = $qbInner
                    ->select($subst . '.' . $this->idName)
                    ->from($this->entityName, $subst)
                    ->where($whereQuery);

            if ($subquery != null) {
                $subqueryInner->andWhere(
                        $query->expr()->in(
                                $subst . '.' . $this->idName, $subquery->getQuery()->getDql()
                        )
                );
            }

            $subquery = $subqueryInner;

            $query->setParameter($paramPosistion, '%' . $searchQueryPart . '%');
        }

        $query->where(
                $query->expr()->in(
                        $alias . '.' . $this->idName, $subquery->getQuery()->getDql()
                )
        );

        return $query;
    }

    /**
     * @param string $searchQuery
     * @return \Pagerfanta\Adapter\DoctrineORMAdapter
     */
    public function getPagerfantaAdapter($searchQuery)
    {
        return new PagerfantaAdapter(
                $this->createDoctrineQueryBuilder($searchQuery), $this->entityManager, $this->entityName
        );
    }

    /**
     * @param string $searchQuery
     * @return \Pagerfanta\Pagerfanta
     */
    public function getPagerfanta($searchQuery)
    {
        return new Pagerfanta($this->getPagerfantaAdapter($searchQuery));
    }

}
