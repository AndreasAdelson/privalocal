<?php

namespace App\Application\Service;

use App\Domain\Model\CompanyStatut;
use App\Infrastructure\Repository\CompanyStatutRepositoryInterface;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CompanyStatutService
{
    /**
     * @var CompanyStatutRepositoryInterface
     */
    private $companyStatutRepository;

    /**
     * CompanyStatutRestController constructor.
     */
    public function __construct(CompanyStatutRepositoryInterface $companyStatutRepository)
    {
        $this->companyStatutRepository = $companyStatutRepository;
    }

    /// Afficher un CompanyStatut
    public function getCompanyStatut(int $companyStatutId): ?CompanyStatut
    {
        return $this->companyStatutRepository->find($companyStatutId);
    }

    public function getAllCompanyStatuts(): ?array
    {
        return $this->companyStatutRepository->findAll();
    }

    public function deleteCompanyStatut(int $companyStatutId): void
    {
        $companyStatut = $this->companyStatutRepository->find($companyStatutId);
        if (!$companyStatut) {
            throw new EntityNotFoundException('CompanyStatut with id ' . $companyStatutId . ' does not exist!');
        }
        $this->companyStatutRepository->delete($companyStatut);
    }

    public function getCompanyStatutByCode(string $code)
    {
        return $this->companyStatutRepository->findOneBy(['code' => $code]);
    }



    /**
     * Retourne une page, potentiellement triée et filtrée.
     *
     * @author vbioret
     *
     * @param string $sortBy
     * @param bool   $descending
     * @param string $filterFields
     * @param string $filterText
     * @param int    $currentPage
     * @param int    $perPage
     *
     * @return Pagerfanta
     */
    public function getPaginatedList(
        $sortBy = 'id',
        $descending = false,
        $filterFields = '',
        $filterText = '',
        $currentPage = 1,
        $perPage = PHP_INT_MAX ? PHP_INT_MAX : 10
    ) {
        return $this->companyStatutRepository
            ->getPaginatedList($sortBy, $descending, $filterFields, $filterText, $currentPage, $perPage);
    }
}
