<?php

namespace Petkopara\MultiSearchBundle\Condition;

use Doctrine\ORM\QueryBuilder;

abstract class ConditionBuilder
{

    protected $queryBuilder;
    protected $searchColumns = array();
    protected $searchTerm;
    protected $searchComparisonType;
    protected $entityName;
    protected $idName;

    const COMPARISION_TYPE_WILDCARD = 'wildcard';
    const COMPARISION_TYPE_EQUALS = 'equals';

    /**
     * Search into the entity 
     * @return QueryBuilder
     */
    public function getQueryBuilderWithConditions()
    {
        $alias = $this->queryBuilder->getRootAlias();
        $query = $this->queryBuilder
                ->select($alias);

        if ($this->searchTerm == '') {
            return $query;
        }

        $searchQueryParts = explode(' ', $this->getSearchTerm());

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

            $query->setParameter($paramPosistion, $this->getSearchQueryPart($searchQueryPart));
        }

        $query->where(
                $query->expr()->in(
                        $alias . '.' . $this->idName, $subquery->getQuery()->getDql()
                )
        );

        return $query;
    }

    /**
     * Whether to use wildcard or equals search
     * @param type $searchQueryPart
     * @return String
     */
    private function getSearchQueryPart($searchQueryPart)
    {
        if ($this->searchComparisonType == self::COMPARISION_TYPE_WILDCARD) {
            return '%' . $searchQueryPart . '%';
        }
        return $searchQueryPart;
    }

    private function getSearchTerm()
    {
        if ($this->searchComparisonType == self::COMPARISION_TYPE_WILDCARD) {
            return $this->searchTerm;
        }
        return str_replace('*', '%', $this->searchTerm);
    }

}
