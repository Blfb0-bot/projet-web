<?php
declare(strict_types=1);

final class OfferModelTest extends BaseModelTestCase
{
    public function testGetAll_whenOffersExist_returnsOffers(): void
    {
        $companyId = $this->createCompany(['nom' => $companyName = 'OfferCo_' . uniqid('', true)]);
        $this->createOffer(['id_entreprise' => $companyId, 'titre' => 'Offer_' . uniqid('', true)]);

        $model = new OfferModel();
        $rows = $model->getAll();

        $this->assertNotEmpty($rows);
        $this->assertSame(1, count($rows));
        $this->assertSame($companyName, $rows[0]['entreprise_nom']);
    }

    public function testGetAll_whenNoOffers_returnsEmptyArray(): void
    {
        $model = new OfferModel();
        $rows = $model->getAll();
        $this->assertSame([], $rows);
    }

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

    public function testUpdate_success_updatesRow(): void
    {
        $companyId = $this->createCompany(['nom' => 'UpdCo_' . uniqid('', true)]);
        $offerId = $this->createOffer(['id_entreprise' => $companyId, 'titre' => 'Old_' . uniqid('', true)]);

        $model = new OfferModel();
        $model->update($offerId, [
            'id_entreprise' => $companyId,
            'titre' => $newTitle = 'New_' . uniqid('', true),
            'description' => 'newdesc',
            'remuneration' => 2222.00,
            'date_debut' => '2026-02-01',
            'date_fin' => '2026-05-01',
        ]);

        $row = $this->pdo->query("SELECT titre, description, remuneration FROM offre WHERE id = {$offerId}")->fetch();
        $this->assertSame($newTitle, $row['titre']);
        $this->assertSame('newdesc', $row['description']);
    }

    public function testUpdate_whenIdDoesNotExist_doesNotCreateRow(): void
    {
        $initialCount = (int)$this->pdo->query('SELECT COUNT(*) AS c FROM offre')->fetch()['c'];
        $companyId = $this->createCompany(['nom' => 'UpdMissingCo_' . uniqid('', true)]);

        $model = new OfferModel();
        $model->update(9999999, [
            'id_entreprise' => $companyId,
            'titre' => 'NoRow_' . uniqid('', true),
            'description' => 'desc',
            'remuneration' => 3333.00,
            'date_debut' => '2026-01-01',
            'date_fin' => '2026-06-01',
        ]);

        $afterCount = (int)$this->pdo->query('SELECT COUNT(*) AS c FROM offre')->fetch()['c'];
        $this->assertSame($initialCount, $afterCount);
    }

    public function testDelete_success_removesRow(): void
    {
        $companyId = $this->createCompany(['nom' => 'DelCo_' . uniqid('', true)]);
        $offerId = $this->createOffer(['id_entreprise' => $companyId]);

        $model = new OfferModel();
        $model->delete($offerId);

        $row = $this->pdo->query("SELECT COUNT(*) AS c FROM offre WHERE id = {$offerId}")->fetch();
        $this->assertSame(0, (int)$row['c']);
    }

    public function testDelete_whenIdDoesNotExist_keepsCount(): void
    {
        $initialCount = (int)$this->pdo->query('SELECT COUNT(*) AS c FROM offre')->fetch()['c'];
        $model = new OfferModel();
        $model->delete(9999999);
        $afterCount = (int)$this->pdo->query('SELECT COUNT(*) AS c FROM offre')->fetch()['c'];
        $this->assertSame($initialCount, $afterCount);
    }

    public function testSyncCompetencesForOffer_success_insertsAndLinksCompetences(): void
    {
        $companyId = $this->createCompany(['nom' => 'SyncCo_' . uniqid('', true)]);
        $offerId = $this->createOffer(['id_entreprise' => $companyId]);

        $model = new OfferModel();
        $model->syncCompetencesForOffer($offerId, 'PHP, SQL');

        $rows = $this->pdo->query(
            "SELECT c.libelle FROM offre_competence oc JOIN competence c ON c.id = oc.id_competence WHERE oc.id_offre = {$offerId}"
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

        $row = $this->pdo->query(
            "SELECT COUNT(*) AS c FROM offre_competence WHERE id_offre = {$offerId}"
        )->fetch();
        $this->assertSame(0, (int)$row['c']);
    }

    public function testSearchByTitleOrCompany_whenTermMatches_returnsRows(): void
    {
        $companyId = $this->createCompany(['nom' => $companyName = 'SearchCompany_' . uniqid('', true)]);
        $title = 'SearchOffer_' . uniqid('', true);
        $this->createOffer(['id_entreprise' => $companyId, 'titre' => $title]);

        $model = new OfferModel();
        $rows = $model->searchByTitleOrCompany($title);

        $this->assertCount(1, $rows);
        $this->assertSame($companyName, $rows[0]['entreprise_nom']);
        $this->assertSame($title, $rows[0]['titre']);
    }

    public function testSearchByTitleOrCompany_whenNoMatch_returnsEmptyArray(): void
    {
        $model = new OfferModel();
        $this->createCompany(['nom' => 'NoMatchCo_' . uniqid('', true)]);
        $rows = $model->searchByTitleOrCompany('___NO_MATCH___' . uniqid('', true));
        $this->assertSame([], $rows);
    }

    public function testGetStats_whenWishlistAndCandidatureExist_populatesCounts(): void
    {
        $companyId = $this->createCompany(['nom' => $companyName = 'StatsCo_' . uniqid('', true)]);
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

        $this->assertArrayHasKey('totaux', $stats);
        $this->assertArrayHasKey('total_offres', $stats['totaux']);
        $this->assertSame(1, (int)$stats['totaux']['total_offres']);

        $this->assertArrayHasKey('candidatures', $stats);
        $this->assertSame(1, (int)$stats['candidatures']['total_candidatures']);
        $this->assertSame(1, (int)$stats['candidatures']['max_sur_une_offre']);

        $this->assertArrayHasKey('top_wishlist', $stats);
        $this->assertNotEmpty($stats['top_wishlist']);
        $this->assertSame(1, (int)$stats['top_wishlist'][0]['nb_wishlist']);
        $this->assertSame($companyName, $stats['top_wishlist'][0]['entreprise']);
    }

    public function testGetStats_whenNoWishlistAndNoCandidature_stillReturnsExpectedKeys(): void
    {
        $companyId = $this->createCompany(['nom' => 'StatsCo2_' . uniqid('', true)]);
        $offerId = $this->createOffer(['id_entreprise' => $companyId, 'titre' => 'StatsOffer2_' . uniqid('', true)]);
        $this->assertGreaterThan(0, $offerId);

        $model = new OfferModel();
        $stats = $model->getStats();

        $this->assertArrayHasKey('totaux', $stats);
        $this->assertSame(1, (int)$stats['totaux']['total_offres']);

        $this->assertArrayHasKey('candidatures', $stats);
        $this->assertSame(0, (int)$stats['candidatures']['total_candidatures']);

        $this->assertArrayHasKey('top_wishlist', $stats);
        $this->assertNotEmpty($stats['top_wishlist']);
        $this->assertSame(0, (int)$stats['top_wishlist'][0]['nb_wishlist']);
    }
}

