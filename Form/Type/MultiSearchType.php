<?php

namespace Petkopara\MultiSearchBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Filter type for strings.
 *
 * @author Petkov Petkov <petkopara@gmail.com>
 */
class MultiSearchType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
                ->setDefaults(array(
                    'search_fields' => array(),
                    'search_comparison_type' => 'wildcard',
                    'required' => false,
                    'data_extraction_method' => 'default',
                ))
                ->setAllowedValues('data_extraction_method', array('default'))
                ->setAllowedValues('search_comparison_type', array('wildcard', 'equals'))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return SearchType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'petkopara_multisearch';
    }

}
