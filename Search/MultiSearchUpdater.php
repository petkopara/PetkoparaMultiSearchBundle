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

    protected $dispatcher;

    public function __construct($dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    public function search($form, $queryBuilder, $className)
    {
        $conditionBuilder = new ConditionBuilder($form, $queryBuilder, $className);
        $queryBuilder = $conditionBuilder->search();
//        $this->dispatcher->dispatch(MultiSearchEvent::NAME, new MultiSearchEvent($form, $queryBuilder));
        return $queryBuilder;
    }

}
