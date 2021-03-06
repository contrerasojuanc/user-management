<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\Request;

/**
 * This custom Doctrine repository is empty because so far we don't need any custom
 * method to query for application user information. But it's always a good practice
 * to define a custom repository that will be used when the application grows.
 *
 * See https://symfony.com/doc/current/doctrine/repository.html
 *
 * @author Ryan Weaver <weaverryan@gmail.com>
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 */
class UserRepository extends ServiceEntityRepository
{
    const NUM_ITEMS = 5;

    use Paginator;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * @param int $page
     *
     * @return Pagerfanta
     */
    public function findBySearch(Request $request, $page = 1)
    {
        $searchText = $request->get('search_text', null);

        $query = $this->createQueryBuilder("searched");

        if ($searchText) {
            $query = $query->andWhere("searched.id like '%$searchText%'")
                ->orWhere("searched.fullName like '%$searchText%'")
                ->orWhere("searched.email like '%$searchText%'")
                ->orWhere("searched.username like '%$searchText%'");
        }

        $query = $query->orderBy('searched.id', 'desc');

        $result = $query->getQuery();

        $paginator = new Pagerfanta(new DoctrineORMAdapter($result, false));
        $paginator->setMaxPerPage(self::NUM_ITEMS);
        $paginator->setCurrentPage($page);

        return $paginator;
    }

}
