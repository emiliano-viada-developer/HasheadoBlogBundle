<?php

namespace Hasheado\BlogBundle\Controller\Admin;

class BlogCategoryController extends AdminController
{
	protected $entityName = 'category';

    protected $entityClass = 'Hasheado\BlogBundle\Entity\BlogCategory';

    protected $repository = 'HasheadoBlogBundle:BlogCategory';

    protected $entityForm = 'Hasheado\BlogBundle\Form\BlogCategoryType';

    protected $filterClass = 'Hasheado\BlogBundle\Form\Filter\BlogCategoryFilter';

    protected $paginatorRoute = 'hasheado_blog_admin_category_pagination';

    protected $listTemplate = 'HasheadoBlogBundle:Admin\BlogCategory:list.html.twig';

    protected $addTemplate = 'HasheadoBlogBundle:Admin\BlogCategory:add.html.twig';

    protected $editTemplate = 'HasheadoBlogBundle:Admin\BlogCategory:edit.html.twig';
}