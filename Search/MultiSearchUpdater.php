<?php

namespace Petkopara\MultiSearchBundle\Search;

use Petkopara\MultiSearchBundle\Event\MultiSearchEvent;
use Symfony\Component\Form\Test\FormInterface;

/**
 * Description of MultiSearchUpdater
 *
 * @author Petkov Petkov
 */
class MultiSearchUpdater
{

    public function search(FormInterface $form, $queryBuilder)
    {
        // and add filters condition for each node
        $this->dispatcher->dispatch(MultiSearchEvent::NAME, new MultiSearchEvent($queryBuilder));

        return $queryBuilder;
    }

}
