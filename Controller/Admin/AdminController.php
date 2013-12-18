<?php

namespace Hasheado\BlogBundle\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Hasheado\BlogBundle\Util\Paginator;

class AdminController extends Controller
{
	protected $entityName;

	protected $entityClass;

	protected $repository;

	protected $entityForm;

	protected $filterClass;

	protected $paginatorRoute;

	protected $listTemplate;

	protected $addTemplate;

	protected $editTemplate;

	protected $use_em = false;

	/**
     * List action
     */
    public function listAction($page = 1)
    {
        //Sorting
        list($orderBy, $sortMode, $sortModeReverse) = $this->sorting();
        //Filtering
        list($filterForm, $filtered) = $this->filtering();

        $em = $this->getDoctrine()->getManager();
        $query = $em->getRepository($this->repository)->getListQuery($filtered, array($orderBy => $sortMode));

        $paginator = Paginator::getInfo(
            $query,                                                  //Doctrine query
            $this->container->getParameter('admin_items_per_list'),  //Items per list
            $page,                                                   //Page number
            $this->paginatorRoute					                 //Route to paginate
        );

        $entities = $paginator['doctrine_paginator']->getQuery()
                    ->setFirstResult($paginator['offset'])
                    ->setMaxResults($paginator['per_page'])
                    ->getResult();

        return $this->render($this->listTemplate, array(
            'entities' => $entities,
            'paginator' => $paginator,
            'filter_form' => $filterForm->createView(),
            'orderBy' => $orderBy,
            'sort_mode_reverse' => $sortModeReverse,
            'filtered' => (count($filtered))? true : false,
        ));
    }

    /**
     * Filter action
     */
    public function filterAction($field, $mode)
    {
        $session = $this->get('session');
        $request = $this->getRequest();
        $em = $this->getDoctrine()->getManager();

        //Check Reset
        if (stristr($request->getQueryString(), '_reset')) {
            //Reset filters
            if ($session->has('filter')) {
                $filter = json_decode($session->get('filter'), true);
                if (isset($filter[$this->entityName]))
                    unset($filter[$this->entityName]);

                $session->set('filter', json_encode($filter));
            }
            //Reset sorting
            if ($session->has('sort')) {
                $sort = json_decode($session->get('sort'), true);
                if (isset($sort[$this->entityName]))
                    unset($sort[$this->entityName]);

                $session->set('sort', json_encode($sort));
            }
            
            return $this->redirect($this->generateUrl('hasheado_blog_admin_' . $this->entityName . '_list'));
        }

        if ($request->isMethod('POST')) {
            $em = ($this->use_em)? $this->getDoctrine()->getManager() : null;
            $filterForm = $this->createForm(new $this->filterClass(array(), $em));
            $filterForm->bind($request);

            $filtered = array();
            if ($filterForm->isValid()) {
                
                $data = $filterForm->getData();
                if (count($data)) {
                    foreach ($data as $key => $value) {
                        if (!is_null($value) && $value != '') {
                            $filtered[$key] = $value;
                        } elseif (is_integer($value) && $value == 0) {
                            $filtered[$key] = $value;
                        } elseif (isset($filtered[$key])) {
                            unset($filtered[$key]);
                        }
                    }
                }
            }

            if ($session->has('filter')) {
                $filter = json_decode($session->get('filter'), true);
                if (isset($filter[$this->entityName]))
                    unset($filter[$this->entityName]);
                    
                if (count($filtered))
                    $filter[$this->entityName] = $filtered;

                $session->set('filter', json_encode($filter));
            } elseif (count($filtered)) {
                $session->set('filter', json_encode(array($this->entityName => $filtered)));
            }
        }

        if ($session->has('sort'))
            $sort = json_decode($session->get('sort'), true);

        $sort[$this->entityName] = array(
                'field' => $field,
                'mode' => $mode
        );

        $session->set('sort', json_encode($sort));

        return $this->redirect($this->generateUrl('hasheado_blog_admin_' . $this->entityName . '_list'));
    }

    /**
     * Add action
     */
    public function addAction()
    {
        $request = $this->getRequest();
        $session = $this->get('session');
        $em = $this->getDoctrine()->getManager();
        $entity = new $this->entityClass();
        $formOptions = $this->getFormOptions($request, $em);
        $form = $this->createForm(new $this->entityForm(), $entity, $formOptions);

        if ($request->isMethod('POST')) {
            $form->bind($request);

            if ($form->isValid()) {
                $em->persist($entity);
                $em->flush();

                $session->getFlashBag()->add('success', ucfirst($this->entityName) . ' was saved!.');
                
                return $this->redirect($this->generateUrl('hasheado_blog_admin_' . $this->entityName . '_edit', array('id' => $entity->getId())));

            } else {
                $session->getFlashBag()->add('error', 'There were errors with the form.');
            }
        }

        return $this->render($this->addTemplate, array(
        	'form' => $form->createView(),
    	));
    }

    /**
     * Edit action
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $request = $this->getRequest();
        $session = $this->get('session');
        $entity = $em->getRepository($this->repository)->find($id);

        if (!$entity) {
            throw $this->createNotFoundException(
                'No ' . $this->entityName . ' found for id '.$id
            );
        }
        
        $formOptions = $this->getFormOptions($request, $em);
        $form = $this->createForm(new $this->entityForm(), $entity, $formOptions);

        if ($request->isMethod('POST')) {
            $form->bind($request);
            
            if ($form->isValid()) {
                $em->persist($entity);
                $em->flush();

                $session->getFlashBag()->add('success', ucfirst($this->entityName) . ' was saved!.');
                
                return $this->redirect($this->generateUrl('hasheado_blog_admin_' . $this->entityName . '_edit', array('id' => $entity->getId())));

            } else {
                $session->getFlashBag()->add('error', 'There were errors with the form.');
            }
        }

        return $this->render($this->editTemplate, array(
            'form' => $form->createView(),
            'entity_id' => $id
        ));
    }

    /**
     * getFormOptions() method
     * Returns an array with options for Form
     * @param $request, $em
     */
    protected function getFormOptions($request, $em)
    {
        $options = array();

        //Check if the form needs the entityManager
        if ($this->use_em)
            $options['em'] = $em;

        //Add extra choices to a choice field type in the form
        if ($this->entityName == 'post' && $request->isMethod('POST')) {
            $post = $request->request->get('post');
            $tags = (isset($post['tags']))? $post['tags'] : null;
            $extra = array();
            if (count($tags)) {
                foreach ($tags as $i => $val) {
                    if (is_numeric($val))
                        unset($tags[$i]);
                    else
                        $extra[$val] = $val;
                }
            }
            $options['extra_choices'] = $extra;
        }

        return $options;
    }

    /**
     * Delete action
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $entity = $em->getRepository($this->repository)->find($id);
        if ($entity) {
            $em->remove($entity);
            $em->flush();

            $this->get('session')->getFlashBag()->add('success', 'The ' . $this->entityName . ' was deleted successfully.');

        } else {
            throw $this->createNotFoundException('Unable to find ' . $this->entityName . '.');
        }

        return $this->redirect($this->generateUrl('hasheado_blog_admin_' . $this->entityName . '_list'));
    }

	/**
     * sorting method
     */
    protected function sorting()
    {
        $session = $this->get('session');

        $orderBy = "id";
        $sortMode = "ASC";
        $sortModeReverse = "DESC";
        if ($session->has('sort')) {
            $sort = json_decode($session->get('sort'), true);
            $orderBy = (isset($sort[$this->entityName]))? $sort[$this->entityName]['field'] : $orderBy;
            $sortMode = (isset($sort[$this->entityName]))? $sort[$this->entityName]['mode'] : $sortMode;
            $sortModeReverse = ($sortMode == "ASC")? "DESC" : "ASC";
        }

        return array($orderBy, $sortMode, $sortModeReverse);
    }

    /**
     * filtering method
     */
    protected function filtering()
    {
        $em = ($this->use_em)? $this->getDoctrine()->getManager() : null;
        $session = $this->get('session');
        $filtered = array();

        if ($session->has('filter')) {
            $filter = json_decode($session->get('filter'), true);
            if(isset($filter[$this->entityName]))
                $filtered = $filter[$this->entityName];
        }

        $filterForm = $this->createForm(new $this->filterClass($filtered, $em));

        return array($filterForm, $filtered);
    }
}