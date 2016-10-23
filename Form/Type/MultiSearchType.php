<?php

namespace Petkopara\MultiSearchBundle\Form\Type;

use Petkopara\MultiSearchBundle\Condition\ConditionBuilder;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Exception\InvalidConfigurationException;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\FormBuilderInterface;
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
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (empty($options['class'])) {
            throw new InvalidConfigurationException('Option "class" must be set.');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
                ->setDefaults(array(
                    'class' => '',
                    'search_fields' => array(),
                    'search_comparison_type' => 'wildcard',
                    'required' => false,
                    'data_extraction_method' => 'default',
                ))
                ->setAllowedValues('data_extraction_method', array('default'))
                ->setAllowedValues('search_comparison_type', array(
                    ConditionBuilder::COMPARISION_TYPE_WILDCARD,
                    ConditionBuilder::COMPARISION_TYPE_EQUALS,
                    ConditionBuilder::COMPARISION_TYPE_STARTS_WITH,
                    ConditionBuilder::COMPARISION_TYPE_ENDS_WITH,
                    ))
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
