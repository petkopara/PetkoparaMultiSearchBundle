<?php

namespace Petkopara\MultiSearchBundle\Event;

use Symfony\Component\EventDispatcher\Event;

class MultiSearchEvent extends Event
{

    const NAME = 'petkopara.muti_search';

    /**
     * @var \Doctrine\ORM\QueryBuilder 
     */
    private $queryBuilder;

    public function __construct($queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
    }

    /**
     * @return \Doctrine\ORM\QueryBuilder
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
