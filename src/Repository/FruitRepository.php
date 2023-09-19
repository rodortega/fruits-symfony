<?php

namespace App\Repository;

use App\Entity\Fruit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Fruit>
 *
 * @method Fruit|null find($id, $lockMode = null, $lockVersion = null)
 * @method Fruit|null findOneBy(array $criteria, array $orderBy = null)
 * @method Fruit[]    findAll()
 * @method Fruit[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method Fruit\null findAllFruitsQuery()
 */
class FruitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Fruit::class);
    }

    public function findAllFruitsQuery()
    {
        return $this->createQueryBuilder('f');
    }

    /**
     * Get the total number of favorite fruits.
     *
     * @return int
     */
    public function countFavoriteFruits(): int
    {
        return $this->createQueryBuilder('f')
            ->select('COUNT(f.id)')
            ->where('f.is_favorite = :isFavorite')
            ->setParameter('isFavorite', 1)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
