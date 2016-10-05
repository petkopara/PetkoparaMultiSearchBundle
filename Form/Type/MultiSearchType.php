<?php

namespace Petkopara\MultiSearchBundle\Form\Type;

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
                    'required' => false,
                    'data_extraction_method' => 'default',
                ))
                ->setAllowedValues('data_extraction_method', array('default'))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return TextType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'multi_search';
    }

}
