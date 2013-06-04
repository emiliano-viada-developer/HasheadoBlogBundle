<?php

namespace Hasheado\BlogBundle\Controller\Admin;

class BlogCommentController extends AdminController
{
	protected $entityName = 'comment';

    protected $entityClass = 'Hasheado\BlogBundle\Entity\BlogComment';

    protected $repository = 'HasheadoBlogBundle:BlogComment';

    protected $entityForm = 'Hasheado\BlogBundle\Form\BlogCommentType';

    protected $filterClass = 'Hasheado\BlogBundle\Form\Filter\BlogCommentFilter';

    protected $paginatorRoute = 'hasheado_blog_admin_comment_pagination';

    protected $listTemplate = 'HasheadoBlogBundle:Admin\BlogComment:list.html.twig';

    protected $addTemplate = 'HasheadoBlogBundle:Admin\BlogComment:add.html.twig';

    protected $editTemplate = 'HasheadoBlogBundle:Admin\BlogComment:edit.html.twig';

    protected $use_em = true;
}