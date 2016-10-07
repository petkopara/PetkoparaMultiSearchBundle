<?php

namespace Petkopara\MultiSearchBundle\Event;

use Doctrine\ORM\QueryBuilder;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Form\FormInterface;

class MultiSearchEvent extends Event
{

    const NAME = 'petkopara.muti_search';

    /**
     * @var QueryBuilder 
     */
    private $queryBuilder;
    private $form;

    public function __construct(FormInterface $form, $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
        $this->form = $form;
    }

    public function getForm()
    {
        return $this->form;
    }

    public function setForm($form)
    {
        $this->form = $form;
        return $this;
    }

    /**
     * @return QueryBuilder
     */
    public function getQueryBuilder()
    {
        return $this->queryBuilder;
    }

    public function setQueryBuilder($queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
        return $this;
    }

}
