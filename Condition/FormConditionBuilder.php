<?php

namespace Petkopara\MultiSearchBundle\Condition;

use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Form\FormInterface;

/**
 * Description of FormConditionBuilder
 *
 * @author Petko Petkov <petkopara@gmail.com>
 */
class FormConditionBuilder extends ConditionBuilder
{

    public function __construct(QueryBuilder $queryBuilder, FormInterface $form)
    {

        $this->queryBuilder = $queryBuilder;
        $this->entityManager = $queryBuilder->getEntityManager();

        $this->searchTerm = $form->getData();
        $this->searchComparisonType = $form->getConfig()->getOption('search_comparison_type');
        $this->entityName = $form->getConfig()->getOption('class');


        /** @var $metadata \Doctrine\ORM\Mapping\ClassMetadata */
        $metadata = $this->entityManager->getClassMetadata($this->entityName);

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

}
