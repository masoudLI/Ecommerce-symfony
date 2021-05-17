<?php

namespace App\Repository;

use App\Entity\Produit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Produit|null find($id, $lockMode = null, $lockVersion = null)
 * @method Produit|null findOneBy(array $criteria, array $orderBy = null)
 * @method Produit[]    findAll()
 * @method Produit[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProduitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Produit::class);
    }

    // /**
    //  * @return Produit[] Returns an array of Produit objects
    //  */

    /**
     * @var Produit[]
     */
    public function findAllVisibleProduit(): array
    {
        return $this->createQueryBuilder('p')
            ->Where('p.disponible = true')
            ->getQuery()
            ->getResult();
    }


    /**
     * recupere les produits en question 
     * @return array
     */
    public function findArray($produits)
    {

        return $this->createQueryBuilder('p')
            ->select('p')
            ->where('p.id IN (:produits)')
            ->setParameter('produits', $produits)
            ->getQuery()
            ->getResult();;
    }


    /*
    public function findOneBySomeField($value): ?Produit
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
