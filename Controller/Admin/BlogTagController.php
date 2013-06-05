<?php

namespace Hasheado\BlogBundle\Controller\Admin;

class BlogTagController extends AdminController
{
    protected $entityName = 'tag';

    protected $entityClass = 'Hasheado\BlogBundle\Entity\BlogTag';

    protected $repository = 'HasheadoBlogBundle:BlogTag';

    protected $entityForm = 'Hasheado\BlogBundle\Form\BlogTagType';

    protected $filterClass = 'Hasheado\BlogBundle\Form\Filter\BlogTagFilter';

    protected $paginatorRoute = 'hasheado_blog_admin_tag_pagination';

    protected $listTemplate = 'HasheadoBlogBundle:Admin\BlogTag:list.html.twig';

    protected $addTemplate = 'HasheadoBlogBundle:Admin\BlogTag:add.html.twig';

    protected $editTemplate = 'HasheadoBlogBundle:Admin\BlogTag:edit.html.twig';
}
