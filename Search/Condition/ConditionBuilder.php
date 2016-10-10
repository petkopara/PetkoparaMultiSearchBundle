<?php

namespace Petkopara\MultiSearchBundle\Search\Condition;

use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Form\FormInterface;

class ConditionBuilder
{

    protected $queryBuilder;
    protected $searchColumns = array();
    protected $searchTerm;
    protected $searchComparisonType;
    protected $form;
    protected $entityName;
    protected $idName;

    public function __construct(FormInterface $form, QueryBuilder $queryBuilder, $className)
    {
        $this->searchTerm = $form->getData();
        $this->searchComparisonType = $form->getConfig()->getOption('search_comparison_type');
        $this->queryBuilder = $queryBuilder;
        $this->entityName = $className;
        $this->entityManager = $queryBuilder->getEntityManager();

        /** @var $metadata \Doctrine\ORM\Mapping\ClassMetadata */
        $metadata = $this->entityManager->getClassMetadata($className);

        $this->idName = $metadata->getSingleIdentifierFieldName();

        $searchFields = $form->getConfig()->getOption('search_fields');
        if (count($searchFields) > 0) {
            $this->searchColumns = $searchFields;
        } else {
            foreach ($metadata->fieldMappings as $field) {
                $this->searchColumns[] = $field['fieldName'];
            }
        }
    }

    public function search()
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

    private function getSearchQueryPart($searchQueryPart)
    {
        if ($this->searchComparisonType == 'wildcard') {
            return '%' . $searchQueryPart . '%';
        }
        return $searchQueryPart;
    }

    private function getSearchTerm()
    {
        if ($this->searchComparisonType == 'wildcard') {
            return $this->searchTerm;
        }
        return str_replace('*', '%', $this->searchTerm);
    }

}
