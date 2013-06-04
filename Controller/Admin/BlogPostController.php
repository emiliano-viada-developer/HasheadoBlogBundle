<?php

namespace Hasheado\BlogBundle\Controller\Admin;

class BlogPostController extends AdminController
{
    protected $entityName = 'post';

    protected $entityClass = 'Hasheado\BlogBundle\Entity\BlogPost';

    protected $repository = 'HasheadoBlogBundle:BlogPost';

    protected $entityForm = 'Hasheado\BlogBundle\Form\BlogPostType';

    protected $filterClass = 'Hasheado\BlogBundle\Form\Filter\BlogPostFilter';

    protected $paginatorRoute = 'hasheado_blog_admin_post_pagination';

    protected $listTemplate = 'HasheadoBlogBundle:Admin\BlogPost:list.html.twig';

    protected $addTemplate = 'HasheadoBlogBundle:Admin\BlogPost:add.html.twig';

    protected $editTemplate = 'HasheadoBlogBundle:Admin\BlogPost:edit.html.twig';

    protected $use_em = true;
}
