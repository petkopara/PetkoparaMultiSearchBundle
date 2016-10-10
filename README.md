# MultiSearchBundle

This bundle provides basic form type and service for multi search with one input in doctrine entity. 

##Usage

Create your form type and include the multiSearchType in the buildForm function: 

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('search', MultiSearchType::class, array(
                    'search_fields' => array( //optional, if it's empty it will search in the all entity columns
                        'name',
                        'content'
            )))
        ;
    }

In the controller add call to the multi search service: 

    public function indexAction(Request $request)
    {
        $search = $request->get('search');
        $em = $this->getDoctrine()->getManager();
        $queryBuilder = $em->getRepository('AppBundle:Post')->createQueryBuilder('e');

        list($filterForm, $queryBuilder) = $this->filter($queryBuilder, $request);

        return $this->render('post/index.html.twig', array(
                'posts' =>  $queryBuilder->getQuery()->getResult()
                'pagerHtml' => $pagerHtml,
                'filterForm' => $filterForm->createView(),
        ));
    }

    /**
     * Create filter form and process filter request.
     *
     */
    protected function filter($queryBuilder, $request)
    {
        $filterForm = $this->createForm('AppBundle\Form\PostFilterType');

        // Filter action
        if ($request->get('filter_action') == 'filter') {
            // Bind values from the request
            $filterForm->submit($request->query->get($filterForm->getName()));

            if ($filterForm->isValid()) {
                // Build the query from the given form object
                $this->get('petkopara_multi_search.updater')->search($filterForm->get('search'), $queryBuilder, 'AppBundle:Post');
            }
        } 

        return array($filterForm, $queryBuilder);
    }

Render your form in the view 

    {{ form_rest(filterForm) }}

### MultiSearchType Available Options

The provided type has 2 options:

* `search_fields` - array of the entity columns that will be added in the search. If it's not set then will search in all columns
* `search_comparison_type` - how to compare the search term. `wildcard` - it's equivalent to the %search% like search. `equals` - like operator without wildcards. Wildcards still can be used if the search term contains *. 

## Author

Petko Petkov - petkopara@gmail.com


## License

MultiSearchBundle is licensed under the MIT License.



