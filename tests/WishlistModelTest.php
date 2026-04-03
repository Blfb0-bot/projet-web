<?php
declare(strict_types=1);

final class WishlistModelTest extends BaseModelTestCase
{
    public function testAjouter_success_returnsTrueAndCreatesRow(): void
    {
        $companyId = $this->createCompany(['nom' => 'WishAddCo_' . uniqid('', true)]);
        $offerId = $this->createOffer(['id_entreprise' => $companyId]);
        $studentId = $this->createUser(['role' => 'etudiant']);

        $model = new WishlistModel();
        $ok = $model->ajouter($studentId, $offerId);

        $this->assertTrue($ok);
        $this->assertTrue($model->existe($studentId, $offerId));
    }

    public function testAjouter_duplicate_returnsFalse(): void
    {
        $companyId = $this->createCompany(['nom' => 'WishDupCo_' . uniqid('', true)]);
        $offerId = $this->createOffer(['id_entreprise' => $companyId]);
        $studentId = $this->createUser(['role' => 'etudiant']);

        $model = new WishlistModel();
        $this->assertTrue($model->ajouter($studentId, $offerId));
        $this->assertFalse($model->ajouter($studentId, $offerId));
    }

    public function testRetirer_success_returnsTrueAndRemovesRow(): void
    {
        $companyId = $this->createCompany(['nom' => 'WishRemCo_' . uniqid('', true)]);
        $offerId = $this->createOffer(['id_entreprise' => $companyId]);
        $studentId = $this->createUser(['role' => 'etudiant']);

        $model = new WishlistModel();
        $model->ajouter($studentId, $offerId);

        $this->assertTrue($model->retirer($studentId, $offerId));
        $this->assertFalse($model->existe($studentId, $offerId));
    }

    public function testExiste_whenRowMissing_returnsFalse(): void
    {
        $companyId = $this->createCompany(['nom' => 'WishExMissingCo_' . uniqid('', true)]);
        $offerId = $this->createOffer(['id_entreprise' => $companyId]);
        $studentId = $this->createUser(['role' => 'etudiant']);

        $model = new WishlistModel();
        $this->assertFalse($model->existe($studentId, $offerId));
    }
}

