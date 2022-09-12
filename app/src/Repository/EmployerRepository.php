<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Employer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @method Employer|null find($id, $lockMode = null, $lockVersion = null)
 * @method Employer|null findOneBy(array $criteria, array $orderBy = null)
 * @method Employer[]    findAll()
 * @method Employer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EmployerRepository extends ServiceEntityRepository
{
    private PaginatorInterface $paginator;

    public function __construct(ManagerRegistry $registry, PaginatorInterface $paginator)
    {
        parent::__construct($registry, Employer::class);
        $this->paginator = $paginator;
    }

    public function getListOfEmployers(?string $id, int $page, int $perPage): PaginationInterface
    {
        $countSql = "SELECT COUNT(*) FROM empl";
        $conn = $this->getEntityManager()->getConnection();
        $counterQuery = $conn->executeQuery($countSql);
        $counterResult = (int)$counterQuery->fetchOne();

        $filterByIdWhere = '';
        $sqlParams = [];
        if (null !== $id) {
            $filterByIdWhere .= 'AND id = :id';
            $sqlParams['id'] = [$id];
        }

        $sql = "SELECT id, parent_id FROM empl WHERE TRUE $filterByIdWhere";

        $rsm = new ResultSetMappingBuilder($this->_em);
        $rsm->addScalarResult('id', 'id', 'string');
        $rsm->addScalarResult('parent_id', 'parentId', 'string');
        $nativeQuery = $this->_em->createNativeQuery($sql, $rsm);
        $nativeQuery->setParameters($sqlParams);

        return $this->paginator->paginate($nativeQuery, $page, $perPage, ['count' => $counterResult]);
    }

    public function getAllChildrenByEmployer(string $id, int $page, int $perPage): PaginationInterface
    {
        $cte = "
        WITH RECURSIVE tree AS (
          SELECT id, ARRAY[:id]::varchar[] AS ancestors
          FROM empl WHERE parent_id = :id
        
          UNION ALL
        
          SELECT empl.id, tree.ancestors || empl.parent_id
          FROM empl, tree
          WHERE empl.parent_id = tree.id
        )
";

        $countSql = "$cte SELECT COUNT(*) FROM tree";

        $conn = $this->getEntityManager()
            ->getConnection()
        ;

        $counterQuery = $conn->executeQuery(
            $countSql,
            [
                'id' => $id,
            ]
        );

        $counterResult = (int)$counterQuery->fetchOne();

        $sql = "$cte SELECT id, ancestors FROM tree";

        $rsm = new ResultSetMappingBuilder($this->_em);
        $rsm->addScalarResult('id', 'id', 'string');
        $rsm->addScalarResult('ancestors', 'ancestors', 'string');
        $nativeQuery = $this->_em->createNativeQuery($sql, $rsm);
        $nativeQuery->setParameter('id', $id);

        return $this->paginator->paginate($nativeQuery, $page, $perPage, ['count' => $counterResult]);
    }

    public function saveDataFormFile(string $filePath): void
    {
        try {
            $this->_em->getConnection()->beginTransaction();
            $pdo = $this->_em->getConnection()->getNativeConnection();
            $pdo->pgsqlCopyFromFile('empl', $filePath, ",", "\\N", 'id, parent_id');
            $this->_em->getConnection()->commit();
        } catch (\Throwable $t) {
            $this->_em->getConnection()->rollBack();
            throw $t;
        }
    }
}
