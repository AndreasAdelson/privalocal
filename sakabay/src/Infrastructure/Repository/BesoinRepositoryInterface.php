<?php

namespace App\Infrastructure\Repository;

use App\Domain\Model\Besoin;

/**
 * Interface BesoinRepositoryInterface.
 */
interface BesoinRepositoryInterface
{
    public function save(Besoin $besoin): void;

    public function delete(Besoin $besoin): void;

    public function getPaginatedBesoinByUserId(
        ?string $utilisateur,
        ?string $codeStatut,
        ?string $company,
        ?string $isCounting
    );
    public function getBesoinAnsweredByCompany(string $besoinId, string $companyId): ?Besoin;
    public function getPaginatedOpportunityList(
        ?string $category,
        ?string $sousCategory,
        ?string $isCounting,
        ?string $company
    );
}
