<?php

namespace App\Infrastructure\Repository;

use App\Domain\Model\Company;
use App\Domain\Model\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;

class CompanyRepository extends AbstractRepository implements CompanyRepositoryInterface
{
    /**
     * CompanyRepository constructor.
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct($entityManager, new ClassMetadata(Company::class));
    }

    public function save(Company $company): void
    {
        $this->_em->persist($company);
        $this->_em->flush($company);
    }

    public function delete(Company $company): void
    {
        $this->_em->remove($company);
        $this->_em->flush($company);
    }

    /**
     * Retourne une page, potentiellement triée et filtrée.
     *
     *
     * @param string $sortBy
     * @param bool   $descending
     * @param string $filterFields
     * @param string $filterText
     * @param int    $currentPage
     * @param int    $perPage
     *
     * @throws LogicException
     * @return Pagerfanta
     */
    public function getPaginatedListAdmin(
        $sortBy = 'id',
        $descending = false,
        $filterFields = '',
        $filterText = '',
        $currentPage = 1,
        $perPage = PHP_INT_MAX,
        $codeStatut = ''
    ) {
        $qb = $this->createQueryBuilder('c');
        if (!empty($codeStatut)) {
            $qb->leftJoin('c.companyStatut', 'companyStatut')
                ->andWhere('companyStatut.code = :codeStatut')
                ->setParameter('codeStatut', $codeStatut);
        }
        if (!empty($filterText)) {
            $queryFilter = ' LOWER(c.'
                . implode(') LIKE LOWER(:searchPattern) OR LOWER(c.', explode(',', $filterFields))
                . ') LIKE LOWER(:searchPattern)';
            $qb->andWhere($queryFilter);
            $qb->setParameter('searchPattern', '%' . mb_strtolower($filterText) . '%');
        }
        if (!empty($sortBy)) {
            $qb->orderBy('c.' . $sortBy, $descending ? 'DESC' : 'ASC');
        }

        return $this->paginate($qb, $perPage, $currentPage);
    }

    /**
     * Retourne une page, potentiellement triée et filtrée.
     *
     *
     * @param string $sortBy
     * @param bool   $descending
     * @param string $filterFields
     * @param string $filterText
     * @param int    $currentPage
     * @param int    $perPage
     *
     */
    public function getPaginatedListUser(
        $sortBy = 'id',
        $descending = false,
        $filterFields = '',
        $filterText = '',
        $codeStatut = '',
        $category = '',
        $city = '',
        $sousCategory = ''
    ) {
        $qb = $this->createQueryBuilder('c');
        if (!empty($codeStatut)) {
            $qb->leftJoin('c.companyStatut', 'companyStatut')
                ->andWhere('companyStatut.code = :codeStatut')
                ->setParameter('codeStatut', $codeStatut);
        }
        if (!empty($filterText)) {
            $queryFilter = ' LOWER(c.'
                . implode(') LIKE LOWER(:searchPattern) OR LOWER(c.', explode(',', $filterFields))
                . ') LIKE LOWER(:searchPattern)';
            $qb->andWhere($queryFilter);
            $qb->setParameter('searchPattern', '%' . mb_strtolower($filterText) . '%');
        }
        if (!empty($category)) {
            $qb->leftJoin('c.category', 'category')
                ->andWhere('category.id = :categoryId')
                ->setParameter('categoryId', $category);
            if (!empty($sousCategory)) {
                $qb->leftJoin('c.sousCategorys', 'sousCategory')
                    ->andWhere('sousCategory.id = :sousCategoryId')
                    ->setParameter('sousCategoryId', $sousCategory);
                $today = \DateTime::createFromFormat("Y-m-d H:i:s", date("Y-m-d H:m:s"));
                $qb->leftJoin('c.companySubscriptions', 'companySubscription')
                    ->andWhere('companySubscription.dtFin >= :today')
                    ->setParameter('today', $today);
            }
        }
        if (!empty($city)) {
            $qb->leftJoin('c.city', 'city')
                ->andWhere('city.id = :cityId')
                ->setParameter('cityId', $city);
        }
        if (!empty($sortBy)) {
            $qb->orderBy('c.' . $sortBy, $descending ? 'DESC' : 'ASC');
        }

        return $qb->getQuery()->getResult();
    }

    public function getCompanyByUserId($utilisateur = '', $onlySubscribed = 'false')
    {
        $qb = $this->createQueryBuilder('c');
        $qb->leftJoin('c.utilisateur', 'utilisateur')
            ->andWhere('utilisateur.id = :utilisateurId')
            ->setParameter('utilisateurId', $utilisateur);
        if ($onlySubscribed == 'true') {
            $today = \DateTime::createFromFormat("Y-m-d H:i:s", date("Y-m-d H:m:s"));
            $qb->leftJoin('c.companySubscriptions', 'companySubscription')
                ->andWhere('companySubscription.dtFin >= :today')
                ->setParameter('today', $today);
        }

        return $qb->getQuery()->getResult();
    }
}
