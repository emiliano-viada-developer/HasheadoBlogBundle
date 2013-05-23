<?php

namespace Hasheado\BlogBundle\Util;

use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;

/*
 * Paginator class
 * @desc Add Pagination logic
 */
class Paginator
{
    /*
     * getInfo() static function
     * @desc Get an array with pagination info
     * @params Doctrine_Query $query, int $perPage, int $page, string $route
     * @return array $paginator
     */
    static public function getInfo($query, $perPage, $page, $route)
    {
        $doctrinePaginator = new DoctrinePaginator($query);

        $paginator['doctrine_paginator'] = $doctrinePaginator;
        $paginator['total'] = count($doctrinePaginator);
        $paginator['route'] = $route;
        $paginator['per_page'] = $perPage;
        $paginator['page'] = $page;
        $paginator['left'] = $page - 1;
        $paginator['right'] = $page + 1;
        $paginator['num_pages'] = ceil($paginator['total'] / $perPage);
        $paginator['offset'] = (($perPage * $page) - $perPage);
        $paginator['until'] = $perPage * $page;
        $paginator['have_to_paginate'] = ($paginator['num_pages'] > 1)? 1 : 0;
        if ($paginator['num_pages'] > 5) {
            $paginator['pages_to_show']['init'] = ($page >= 4)? $page - 2 : 1;
            $paginator['pages_to_show']['end'] = ($page >= 3)? $page + 2 : 5;

            if($paginator['pages_to_show']['end'] > $paginator['num_pages']) {
                $paginator['pages_to_show']['end'] = $paginator['num_pages'];
                $diff = $paginator['page'] - $paginator['num_pages'] + 2;
                $paginator['pages_to_show']['init'] = $paginator['pages_to_show']['init'] - $diff;
            }

        } else {
            $paginator['pages_to_show']['init'] = 1;
            $paginator['pages_to_show']['end'] = $paginator['num_pages'];
        }

        return $paginator;
    }
}