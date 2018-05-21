<?php

namespace App\Repository;

use App\Entity\Group;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @method Group|null find($id, $lockMode = null, $lockVersion = null)
 * @method Group|null findOneBy(array $criteria, array $orderBy = null)
 * @method Group[]    findAll()
 * @method Group[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GroupRepository extends ServiceEntityRepository
{
    const NUM_ITEMS = 5;

    use Paginator;

    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Group::class);
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
                ->orWhere("searched.name like '%$searchText%'");
        }

        $query = $query->orderBy('searched.id', 'desc');

        $result = $query->getQuery();

        $paginator = new Pagerfanta(new DoctrineORMAdapter($result, false));
        $paginator->setMaxPerPage(self::NUM_ITEMS);
        $paginator->setCurrentPage($page);

        return $paginator;
    }
//    /**
//     * @return Group[] Returns an array of Group objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('g.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Group
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
