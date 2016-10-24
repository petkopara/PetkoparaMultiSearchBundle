# MultiSearchBundle

This bundle provides basic form type and service for multi search in doctrine. 

[![Build Status](https://scrutinizer-ci.com/g/petkopara/PetkoparaMultiSearchBundle/badges/build.png?b=master)](https://scrutinizer-ci.com/g/petkopara/PetkoparaMultiSearchBundle/build-status/master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/petkopara/PetkoparaMultiSearchBundle/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/petkopara/PetkoparaMultiSearchBundle/?branch=master)
[![Latest Stable](https://img.shields.io/packagist/v/petkopara/multi-search-bundle.svg?maxAge=2592000?style=flat-square)](https://packagist.org/packages/petkopara/multi-search-bundle)
[![Total Downloads](https://img.shields.io/packagist/dt/petkopara/multi-search-bundle.svg?maxAge=2592000?style=flat-square)](https://packagist.org/packages/petkopara/multi-search-bundle)

## Installation 


### Using composer

    composer require petkopara/multi-search-bundle

Add it to the `AppKernel.php` class:

    new Petkopara\MultiSearchBundle\PetkoparaMultiSearchBundle(),


##Usage

### Form

Create your form type and include the multiSearchType in the buildForm function: 

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('search', MultiSearchType::class, array(
                    'class' => 'AppBundle:Post', //required
                    'search_fields' => array( //optional, if it's empty it will search in the all entity columns
                        'name',
                        'content'
                     ), 
                     'comparison_type' = > 'wildcard' //optional, what type of comparison to applied ('wildcard','starts_with', 'ends_with', 'equals')
                     
                ))
        ;
    }

In the controller add call to the multi search service: 

    public function indexAction(Request $request)
    {
        $search = $request->get('search');
        $em = $this->getDoctrine()->getManager();
        $queryBuilder = $em->getRepository('AppBundle:Post')->createQueryBuilder('e');
        $filterForm = $this->createForm('AppBundle\Form\PostFilterType');

        // Bind values from the request
        $filterForm->handleRequest($request);

        if ($filterForm->isValid()) {
            // Build the query from the given form object
            $queryBuilder = $this->get('petkopara_multi_search.builder')->searchForm($queryBuilder, $filterForm->get('search'));
        }
        
        ..
    }

Render your form in the view 

    {{ form_rest(filterForm) }}


### Without form 

    $qb = $em->getRepository('AppBundle:Post')->createQueryBuilder('e');
    $qb = $this->get('petkopara_multi_search.builder')->searchEntity($qb, 'AppBundle:Post', $search);
    


## Available Options

The provided type has 2 options:

* `search_fields` - array of the entity columns that will be added in the search. If it's not set then will search in all columns
* `search_comparison_type` -  how to compare the search term.   

  * `wildcard` - it's equivalent to the %search% like search.

  * `equals` - like operator without wildcards. Wildcards still can be used with `equals` if the search term contains *.

  * `starts_with` - it's equivalent to the %search like search.
  
  * `ends_with` - it's equivalent to the search% like search.

## Author

Petko Petkov - petkopara@gmail.com


## License

MultiSearchBundle is licensed under the MIT License.



