<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 20/05/18
 * Time: 09:41 PM
 */

namespace App\Repository;

use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;

trait Paginator
{
    public function findByPage($page = 1): Pagerfanta
    {
        $query = $this->findAll();

        return $this->createPaginatorFromArray($query, $page);
    }

    private function createPaginator(Query $query, int $page): Pagerfanta
    {
        $paginator = new Pagerfanta(new DoctrineORMAdapter($query));
        $paginator->setMaxPerPage(self::NUM_ITEMS);
        $paginator->setCurrentPage($page);

        return $paginator;
    }
    private function createPaginatorFromArray(Array $query, int $page): Pagerfanta
    {
        $paginator = new Pagerfanta(new ArrayAdapter($query));
        $paginator->setMaxPerPage(self::NUM_ITEMS);
        $paginator->setCurrentPage($page);

        return $paginator;
    }
}