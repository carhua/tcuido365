<?php

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Repository;

use App\Entity\Parametro;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;

/**
 * @method Parametro|null find($id, $lockMode = null, $lockVersion = null)
 * @method Parametro|null findOneBy(array $criteria, array $orderBy = null)
 * @method Parametro[]    findAll()
 * @method Parametro[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ParametroRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Parametro::class);
    }

    public function findLatest(int $page): Pagerfanta
    {
        $qb = $this->createQueryBuilder('p')
            ->addSelect('padre')
            ->leftJoin('p.padre', 'padre')
            // ->where('p.activo = :activo')
            ->orderBy('p.id', 'ASC')
            // ->setParameter('activo', true)
        ;

        return $this->createPaginator($qb->getQuery(), $page);
    }

    private function createPaginator(Query $query, int $page): Pagerfanta
    {
        $paginator = new Pagerfanta(new DoctrineORMAdapter($query));
        $paginator->setMaxPerPage(Parametro::NUM_ITEMS);
        $paginator->setCurrentPage($page);

        return $paginator;
    }

    /**
     * @return Parametro[] Returns an array of Acopio objects
     */
    public function findData(int $length = null, int $start = null, $search = null, $columns = null, $order = null)
    {
        $qb = $this->createQueryBuilder('parametro');

        $query = $qb
            ->select(['parametro', 'padre'])
            ->leftJoin('parametro.padre', 'padre')
            ->where('parametro.activo = true')
            // ->setParameter('val', $value)
            // ->orderBy('a.id', 'ASC')
        ;

        if (null !== $length) {
            $query = $query->setMaxResults($length);
        }

        if (null !== $start) {
            $query = $query->setFirstResult($start);
        }

        if (null !== $search && '' !== $search['value']) {
            $orX = $qb->expr()->orX();
            $orX->add($qb->expr()->like('parametro.nombre', $qb->expr()->literal('%'.$search['value'].'%')));
            $orX->add($qb->expr()->like('padre.nombre', $qb->expr()->literal('%'.$search['value'].'%')));
            $query = $query->andWhere($orX);
        }

        if (null !== $order && \is_array($order) && \is_array($order[0])) {
            if ('4' !== $order[0]['column']) {
                $query = $query->addOrderBy('parametro.'.$columns[(int) $order[0]['column']]['data'], $order[0]['dir']);
            } else {
                $query = $query->addOrderBy('padre.nombre', $order[0]['dir']);
            }
        }

        if (null !== $columns && \is_array($columns)) {
            $andX = $qb->expr()->andX();
            foreach ($columns as $column) {
                if ('' !== $column['search']['value']) {
                    $value = $qb->expr()->literal($column['search']['value']);
                    switch ($column['data']) {
                        case 'id':
                            $andX->add($qb->expr()->eq('parametro.id', $value));
                            break;
                        case 'nombre':
                            $andX->add($qb->expr()->like('parametro.nombre', $qb->expr()->literal('%'.$column['search']['value'].'%')));
                            break;
                        case 'padre':
                            $andX->add($qb->expr()->eq('padre.id', $value));
                            break;
                        case 'activo':
                            $andX->add($qb->expr()->eq('parametro.activo', $value));
                            break;
                    }
                }
            }
            $query = $query->andWhere($andX);
        }

        return $query->getQuery()->getArrayResult();
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findOneByPadreAndAlias(string $padre_alias, string $alias): ?Parametro
    {
        // try{
        return $this->createQueryBuilder('parametro')
                ->join('parametro.padre', 'padre')
                ->where('parametro.activo = TRUE')
                ->andWhere('parametro.alias = :alias')
                ->andWhere('padre.alias = :padre_alias')
                ->setParameter('alias', $alias)
                ->setParameter('padre_alias', $padre_alias)
                ->getQuery()
                ->getOneOrNullResult()
        ;
        /*}
        catch (NonUniqueResultException $e){
            return null;
        }*/
    }

    /**
     * @return Parametro[]|array
     */
    public function findByAliasPadre(string $padreAlias): array
    {
        return $this->createQueryBuilder('parametro')
            ->select('parametro')
            ->join('parametro.padre', 'padre')
            ->where('parametro.activo = TRUE')
            ->andWhere('padre.alias = :alias')
            ->setParameter('alias', $padreAlias)
            ->getQuery()
            ->getResult()
        ;
    }
}
