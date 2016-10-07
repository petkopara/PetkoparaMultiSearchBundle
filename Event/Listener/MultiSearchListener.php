<?php

namespace Petkopara\MultiSearchBundle\Event;

use Lexik\Bundle\FormFilterBundle\Event\ApplyFilterConditionEvent;
use Petkopara\TritonCrudBundle\MultiSearch\QueryBuilder;

/**
 * Description of MultiSearchListener
 *
 * @author Petkov Petkov
 */
class MultiSearchListener
{

    /**
     * @param ApplyFilterConditionEvent $event
     */
    public function onApplyFilterCondition(MultiSearchEvent $event)
    {
        
    }

}
