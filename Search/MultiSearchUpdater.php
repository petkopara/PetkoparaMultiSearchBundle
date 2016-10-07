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
        $conditionBuilder = new ConditionBuilder($queryBuilder, $className);
        $searchTerm = $form->get('search')->getData();
        $queryBuilder = $conditionBuilder->search($searchTerm);
//        $this->dispatcher->dispatch(MultiSearchEvent::NAME, new MultiSearchEvent($form, $queryBuilder));
        return $queryBuilder;
    }

}
