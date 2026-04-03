<?php
declare(strict_types=1);

final class WishlistModelTest extends BaseModelTestCase
{
    public function testGetByEtudiant_whenNoWishlist_returnsEmptyArray(): void
    {
        $studentId = $this->createUser(['role' => 'etudiant', 'nom' => 'WUser1', 'prenom' => 'W1']);
        $model = new WishlistModel();

        $rows = $model->getByEtudiant($studentId);
        $this->assertSame([], $rows);
    }

    public function testGetByEtudiant_whenWishlistExists_returnsOffers(): void
    {
        $companyId = $this->createCompany(['nom' => 'WishCo_' . uniqid('', true)]);
        $offerId = $this->createOffer(['id_entreprise' => $companyId, 'titre' => $title = 'WishOffer_' . uniqid('', true)]);
        $studentId = $this->createUser(['role' => 'etudiant', 'nom' => 'WUser2', 'prenom' => 'W2']);

        $model = new WishlistModel();
        $ok = $model->ajouter($studentId, $offerId);
        $this->assertTrue($ok);

        $rows = $model->getByEtudiant($studentId);
        $this->assertCount(1, $rows);
        $this->assertSame($offerId, (int)$rows[0]['id']);
        $this->assertSame($title, $rows[0]['titre']);
    }

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

    public function testRetirer_whenPairDoesNotExist_returnsTrueAndKeepsNoRow(): void
    {
        $companyId = $this->createCompany(['nom' => 'WishRemEmptyCo_' . uniqid('', true)]);
        $offerId = $this->createOffer(['id_entreprise' => $companyId]);
        $studentId = $this->createUser(['role' => 'etudiant']);

        $model = new WishlistModel();
        $this->assertTrue($model->retirer($studentId, $offerId));
        $this->assertFalse($model->existe($studentId, $offerId));
    }

    public function testExiste_whenRowExists_returnsTrue(): void
    {
        $companyId = $this->createCompany(['nom' => 'WishExCo_' . uniqid('', true)]);
        $offerId = $this->createOffer(['id_entreprise' => $companyId]);
        $studentId = $this->createUser(['role' => 'etudiant']);

        $model = new WishlistModel();
        $model->ajouter($studentId, $offerId);
        $this->assertTrue($model->existe($studentId, $offerId));
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

