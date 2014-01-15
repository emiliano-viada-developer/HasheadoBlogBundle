<?php

namespace Hasheado\BlogBundle\Controller\Admin;

use Symfony\Component\HttpFoundation\Response;

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

    public function uploadAction()
    {
        $request = $this->getRequest();
        $files = $request->files;
        $result = array('status' => 0, 'message' => '');
        $directory = $this->get('kernel')->getRootDir() . '/../web/uploads';

        try {
            if (count($files)) {
                foreach($request->files as $uploadedFile) {
                    $filename = sha1(uniqid(mt_rand(), true)) . '.' . $uploadedFile->getClientOriginalExtension();
                    $file = $uploadedFile->move($directory, $filename);
                    if ($file) {
                        $result['status'] = 1;
                        $result['message'] = '/uploads/' . $filename;
                    }
                }
            }
        } catch (\Exception $e) {
            $result['message'] = $e->getMessage();
        }

        $response = new Response(json_encode($result));
        $response->headers->set('Content-Type', 'application/json');
        
        return $response;
    }
}
