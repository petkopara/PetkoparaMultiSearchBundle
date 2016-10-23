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
    const COMPARISION_TYPE_STARTS_WITH = 'starts_with';
    const COMPARISION_TYPE_ENDS_WITH = 'ends_with';
    const COMPARISION_TYPE_EQUALS = 'equals';

    /**
     * Search into the entity 
     * @return QueryBuilder
     */
    public function getQueryBuilderWithConditions()
    {
        $alias = $this->getQueryBuilder()->getRootAlias();
        $query = $this->getQueryBuilder()
                ->select($alias);

        if ($this->searchTerm == '') {
            return $query;
        }

        $searchQueryParts = explode(' ', $this->searchTerm);

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
        switch ($this->searchComparisonType) {
            case self::COMPARISION_TYPE_WILDCARD:
                return '%' . $searchQueryPart . '%';
            case self::COMPARISION_TYPE_STARTS_WITH:
                return '%' . $searchQueryPart;
            case self::COMPARISION_TYPE_ENDS_WITH:
                return $searchQueryPart . '%';
            default: //equals comparison type
                return str_replace('*', '%', $searchQueryPart);
        }
    }

    /**
     * 
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getQueryBuilder()
    {
        return $this->queryBuilder;
    }

}
