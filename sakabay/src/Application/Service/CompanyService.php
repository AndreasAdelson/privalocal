<?php

namespace App\Application\Service;

use App\Domain\Model\Company;
use App\Infrastructure\Repository\CommentRepositoryInterface;
use App\Infrastructure\Repository\CompanyRepositoryInterface;
use DateTime;
use Doctrine\ORM\EntityNotFoundException;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;

class CompanyService
{
    /**
     * @var CompanyRepositoryInterface
     */
    private $companyRepository;
    private $commentRepository;


    /**
     * CompanyRestController constructor.
     */
    public function __construct(CompanyRepositoryInterface $companyRepository, CommentRepositoryInterface $commentRepository)
    {
        $this->companyRepository = $companyRepository;
        $this->commentRepository = $commentRepository;
    }


    /// Afficher un Company
    public function getCompany(int $companyId): ?Company
    {
        return $this->companyRepository->find($companyId);
    }

    public function getCompanyByUrlName(string $urlName): ?Company
    {
        return $this->companyRepository->findOneBy(['urlName' => $urlName]);
    }

    public function getAllCompanys(): ?array
    {
        return $this->companyRepository->findAll();
    }

    public function deleteCompany(int $companyId): void
    {
        $company = $this->companyRepository->find($companyId);
        if (!$company) {
            throw new EntityNotFoundException('Company with id ' . $companyId . ' does not exist!');
        }
        $this->companyRepository->delete($company);
    }

    public function isCompanySubscribtionActive(Company $company): bool
    {
        $companySubscriptions = $company->getCompanySubscriptions()->toArray();
        if (!empty($companySubscriptions)) {
            usort($companySubscriptions, function ($a, $b) {
                return ($a->getDtFin()->format('Y-m-d H:i:s') < $b->getDtFin()->format('Y-m-d H:i:s'));
            });
            $now = new DateTime('now');
            $lastDtFinSubscription = $companySubscriptions[0]->getDtfin();
            if ($lastDtFinSubscription > $now) {
                return true;
            }
        }
        return false;
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
     * @param int    $category
     * @param int    $city
     * @param int    $sousCategory
     *
     * @return Pagerfanta
     */
    public function getPaginatedListAdmin(
        $sortBy = 'id',
        $descending = false,
        $filterFields = '',
        $filterText = '',
        $currentPage = 1,
        $perPage = PHP_INT_MAX ? PHP_INT_MAX : 10,
        $codeStatut = ''

    ) {
        return $this->companyRepository
            ->getPaginatedListAdmin($sortBy, $descending, $filterFields, $filterText, $currentPage, $perPage, $codeStatut);
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
     * @param int    $category
     * @param int    $city
     * @param int    $sousCategory
     *
     * @return Pagerfanta
     */
    public function getPaginatedListUser(
        $sortBy = 'id',
        $descending = false,
        $filterFields = '',
        $filterText = '',
        $currentPage = 1,
        $perPage = PHP_INT_MAX ? PHP_INT_MAX : 10,
        $codeStatut = '',
        $category = '',
        $city = '',
        $sousCategory = ''

    ) {
        //Trie des entreprises recherchées.
        $companys = $this->companyRepository
            ->getPaginatedListUser($sortBy, $descending, $filterFields, $filterText, $codeStatut, $category, $city, $sousCategory);

        //Entreprises abonnées
        $subscribedCompanys = array_filter($companys, function ($company) {
            return $this->isCompanySubscribtionActive($company) === true;
        });

        //Trie des entreprises abonnées par ordre alphabétique
        usort($subscribedCompanys, function ($a, $b) {
            return (strcmp($a->getName(), $b->getName()));
        });

        //Entreprises non abonnées
        $othersCompanys = array_udiff($companys, $subscribedCompanys, function ($a, $b) {
            return $a->getId() - $b->getId();
        });

        //Entreprises non abonnées avec notes
        $othersCompanysWithNote = array_filter($othersCompanys, function ($company) {
            return is_null($company->getNote()) === false;
        });

        //Entreprises non abonnées sans notes
        $othersCompanys = array_udiff($othersCompanys, $othersCompanysWithNote, function ($a, $b) {
            return $a->getId() - $b->getId();
        });

        //Trie des entreprises non abonnées avec note par note
        usort($othersCompanysWithNote, function ($a, $b) {
            $aNote = $a->getNote();
            $bNote = $b->getNote();
            return ($aNote > $bNote) ? -1 : 1;
        });

        //Trie des entreprises non abonnées sans note par ordre alphabétique
        usort($othersCompanys, function ($a, $b) {
            return (strcmp($a->getName(), $b->getName()));
        });

        //Tableau de fin dans l'ordre souhaité
        $companys = array_merge($subscribedCompanys, $othersCompanysWithNote, $othersCompanys);
        return $this->paginateArray($companys, $perPage, $currentPage);
    }

    /**
     * Retourne une page en fonction d'une requète, d'une taille et d'une position.
     *

     *
     * @param int $perPage
     * @param int $currentPage
     *
     * @throws LogicException
     * @return Pagerfanta
     */
    public function paginateArray($data, $perPage, $currentPage)
    {
        $perPage = (int) $perPage;
        if (0 >= $perPage) {
            throw new \LogicException('$perPage must be greater than 0.');
        }
        if (0 >= $currentPage) {
            throw new \LogicException('$currentPage must be greater than 0.');
        }
        $pager = new Pagerfanta(new ArrayAdapter($data));
        $pager->setMaxPerPage((int) $perPage);
        $pager->setCurrentPage((int) $currentPage);

        return $pager;
    }

    public function getCompanyByUserId($utilisateur = '', $onlySubscribed = 'false')
    {
        return $this->companyRepository->getCompanyByUserId($utilisateur, $onlySubscribed);
    }

    public function updateNotation($companyId)
    {
        $company = $this->companyRepository->find($companyId);
        if (!$company) {
            throw new EntityNotFoundException('Company with id ' . $companyId . ' does not exist!');
        }
        $averageNotation = $this->commentRepository->getAverageNotation($companyId);
        $company->setNote($averageNotation[0]['note_avg']);
        $this->companyRepository->save($company);
    }
}
