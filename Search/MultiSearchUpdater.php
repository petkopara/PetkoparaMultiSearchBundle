<?php

namespace Petkopara\MultiSearchBundle\Search;

use Petkopara\MultiSearchBundle\Search\Condition\ConditionBuilder;
use Symfony\Component\Form\Test\FormInterface;

/**
 * Description of MultiSearchUpdater
 *
 * @author Petkov Petkov
 */
class MultiSearchUpdater
{

    public function search($form, $queryBuilder)
    {
        $conditionBuilder = new ConditionBuilder($form, $queryBuilder);
        
        return $conditionBuilder->getQueryBuilderWithConditions();
    }

}
