<?php

namespace Petkopara\MultiSearchBundle\Service;

use Doctrine\ORM\QueryBuilder;
use Petkopara\MultiSearchBundle\Condition\ConditionBuilder;
use Petkopara\MultiSearchBundle\Condition\EntityConditionBuilder;
use Petkopara\MultiSearchBundle\Condition\FormConditionBuilder;
use RuntimeException;
use Symfony\Component\Form\FormInterface;

/**
 * Description of MultiSearchUpdater
 *
 * @author Petkov Petkov
 */
class MultiSearchBuilderService
{

    /**
     * 
     * @param QueryBuilder $queryBuilder
     * @param FormInterface $form
     * @return QueryBuilder
     */
    public function searchForm(QueryBuilder $queryBuilder, FormInterface $form)
    {
        $conditionBuilder = new FormConditionBuilder($queryBuilder, $form);

        return $conditionBuilder->getQueryBuilderWithConditions();
    }

    /**
     * 
     * @param QueryBuilder $queryBuilder
     * @param type $entityName
     * @param type $searchTerm
     * @param array $searchFields
     * @param type $comparisonType
     * @return QueryBuilder
     * @throws RuntimeException
     */
    public function searchEntity(QueryBuilder $queryBuilder, $entityName, $searchTerm, array $searchFields = array(), $comparisonType = ConditionBuilder::COMPARISION_TYPE_WILDCARD)
    {
        if (!in_array($comparisonType, array(ConditionBuilder::COMPARISION_TYPE_WILDCARD, ConditionBuilder::COMPARISION_TYPE_EQUALS))) {
            throw new RuntimeException("The condition type should be wildcard or equals");
        }

        $conditionBuilder = new EntityConditionBuilder($queryBuilder, $entityName, $searchTerm, $searchFields, $comparisonType);

        return $conditionBuilder->getQueryBuilderWithConditions();
    }

}
