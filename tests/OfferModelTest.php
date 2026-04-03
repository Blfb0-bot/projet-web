<?php
declare(strict_types=1);

final class OfferModelTest extends BaseModelTestCase
{
    public function testFindIdByTitreAndCompany_whenOfferExists_returnsId(): void
    {
        $companyId = $this->createCompany(['nom' => 'FindCo_' . uniqid('', true)]);
        $title = 'FindTitle_' . uniqid('', true);
        $offerId = $this->createOffer(['id_entreprise' => $companyId, 'titre' => $title]);

        $model = new OfferModel();
        $found = $model->findIdByTitreAndCompany($title, $companyId);
        $this->assertSame($offerId, $found);
    }

    public function testFindIdByTitreAndCompany_whenTitleIsEmpty_returnsNull(): void
    {
        $model = new OfferModel();
        $this->assertNull($model->findIdByTitreAndCompany('', 1));
    }

    public function testCreate_success_insertsAndReturnsId(): void
    {
        $companyId = $this->createCompany(['nom' => 'CreateCo_' . uniqid('', true)]);
        $model = new OfferModel();

        $offerId = $model->create([
            'id_entreprise' => $companyId,
            'titre' => $title = 'CreateTitle_' . uniqid('', true),
            'description' => 'desc',
            'remuneration' => 1234.50,
            'date_debut' => '2026-01-01',
            'date_fin' => '2026-06-01',
        ]);

        $this->assertGreaterThan(0, $offerId);

        $row = $this->pdo->query("SELECT titre, duree_mois FROM offre WHERE id = {$offerId}")->fetch();
        $this->assertSame($title, $row['titre']);
        $this->assertNotNull($row['duree_mois']);
    }

    public function testCreate_invalidCompanyId_throwsPDOException(): void
    {
        $this->expectException(PDOException::class);

        $model = new OfferModel();
        $model->create([
            'id_entreprise' => 9999999,
            'titre' => 'BadCompany_' . uniqid('', true),
            'description' => 'desc',
            'remuneration' => 1000.00,
            'date_debut' => '2026-01-01',
            'date_fin' => '2026-06-01',
        ]);
    }

    public function testSyncCompetencesForOffer_success_insertsAndLinksCompetences(): void
    {
        $companyId = $this->createCompany(['nom' => 'SyncCo_' . uniqid('', true)]);
        $offerId = $this->createOffer(['id_entreprise' => $companyId]);

        $model = new OfferModel();
        $model->syncCompetencesForOffer($offerId, 'PHP, SQL');

        $rows = $this->pdo->query(
            "SELECT c.libelle
             FROM offre_competence oc
             JOIN competence c ON c.id = oc.id_competence
             WHERE oc.id_offre = {$offerId}"
        )->fetchAll();

        $labels = array_map(static fn($r) => $r['libelle'], $rows);
        sort($labels);
        $this->assertSame(['PHP', 'SQL'], $labels);
    }

    public function testSyncCompetencesForOffer_whenCsvIsEmpty_deletesExistingLinks(): void
    {
        $companyId = $this->createCompany(['nom' => 'SyncEmptyCo_' . uniqid('', true)]);
        $offerId = $this->createOffer(['id_entreprise' => $companyId]);

        $model = new OfferModel();
        $model->syncCompetencesForOffer($offerId, 'PHP, SQL');
        $model->syncCompetencesForOffer($offerId, '');

        $row = $this->pdo->query("SELECT COUNT(*) AS c FROM offre_competence WHERE id_offre = {$offerId}")->fetch();
        $this->assertSame(0, (int)$row['c']);
    }

    public function testGetStats_whenWishlistAndCandidatureExist_populatesCounts(): void
    {
        $companyName = 'StatsCo_' . uniqid('', true);
        $companyId = $this->createCompany(['nom' => $companyName]);
        $offerId = $this->createOffer(['id_entreprise' => $companyId, 'titre' => 'StatsOffer_' . uniqid('', true)]);

        $studentId = $this->createUser(['role' => 'etudiant', 'nom' => 'StatsStudent', 'prenom' => 'SS']);

        (new WishlistModel())->ajouter($studentId, $offerId);
        (new ApplicationModel())->create([
            'id_offre' => $offerId,
            'id_etudiant' => $studentId,
            'lettre_motivation' => 'Lettre',
            'cv_path' => null,
        ]);

        $model = new OfferModel();
        $stats = $model->getStats();

        $this->assertSame(1, (int)$stats['totaux']['total_offres']);
        $this->assertSame(1, (int)$stats['candidatures']['total_candidatures']);
        $this->assertSame(1, (int)$stats['candidatures']['max_sur_une_offre']);
        $this->assertSame(1, (int)$stats['top_wishlist'][0]['nb_wishlist']);
        $this->assertSame($companyName, $stats['top_wishlist'][0]['entreprise']);
    }
}

