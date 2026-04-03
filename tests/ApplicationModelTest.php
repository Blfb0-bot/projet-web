<?php
declare(strict_types=1);

final class ApplicationModelTest extends BaseModelTestCase
{
    public function testCreate_success_insertsAndGetByIdReturnsRow(): void
    {
        $companyId = $this->createCompany(['nom' => 'C_' . uniqid('', true)]);
        $offerId = $this->createOffer(['id_entreprise' => $companyId, 'titre' => 'OfferA_' . uniqid('', true)]);

        $studentId = $this->createUser(['role' => 'etudiant', 'nom' => 'Student', 'prenom' => 'S']);
        $model = new ApplicationModel();

        $id = $model->create([
            'id_offre' => $offerId,
            'id_etudiant' => $studentId,
            'lettre_motivation' => 'Lettre',
            'cv_path' => null,
        ]);

        $this->assertIsInt($id);
        $this->assertGreaterThan(0, $id);

        $row = $model->getById($id);
        $this->assertIsArray($row);
        $this->assertSame($offerId, (int)$row['id_offre']);
        $this->assertSame($studentId, (int)$row['id_etudiant']);
    }

    public function testCreate_invalidForeignKeys_throwsPDOException(): void
    {
        $this->expectException(PDOException::class);

        $studentId = $this->createUser(['role' => 'etudiant', 'nom' => 'StudentX', 'prenom' => 'SX']);
        $model = new ApplicationModel();

        // id_offre inexistant -> FK user/candidature échoue
        $model->create([
            'id_offre' => 9999999,
            'id_etudiant' => $studentId,
            'lettre_motivation' => 'Lettre',
            'cv_path' => null,
        ]);
    }

    public function testAlreadyApplied_whenNoCandidature_returnsFalse(): void
    {
        $companyId = $this->createCompany(['nom' => 'C_' . uniqid('', true)]);
        $offerId = $this->createOffer(['id_entreprise' => $companyId]);
        $studentId = $this->createUser(['role' => 'etudiant', 'nom' => 'StudentY', 'prenom' => 'SY']);

        $model = new ApplicationModel();
        $this->assertFalse($model->alreadyApplied($offerId, $studentId));
    }

    public function testAlreadyApplied_whenCandidatureExists_returnsTrue(): void
    {
        $companyId = $this->createCompany(['nom' => 'C_' . uniqid('', true)]);
        $offerId = $this->createOffer(['id_entreprise' => $companyId]);
        $studentId = $this->createUser(['role' => 'etudiant', 'nom' => 'StudentZ', 'prenom' => 'SZ']);

        $model = new ApplicationModel();
        $model->create([
            'id_offre' => $offerId,
            'id_etudiant' => $studentId,
            'lettre_motivation' => 'Lettre',
            'cv_path' => null,
        ]);

        $this->assertTrue($model->alreadyApplied($offerId, $studentId));
    }

    public function testGetByStudent_whenCandidatureExists_returnsRows(): void
    {
        $companyName = 'Entreprise_' . uniqid('', true);
        $companyId = $this->createCompany(['nom' => $companyName]);
        $offerTitle = 'Titre_' . uniqid('', true);
        $offerId = $this->createOffer(['id_entreprise' => $companyId, 'titre' => $offerTitle]);
        $studentId = $this->createUser(['role' => 'etudiant', 'nom' => 'StudentA', 'prenom' => 'SA']);

        $model = new ApplicationModel();
        $model->create([
            'id_offre' => $offerId,
            'id_etudiant' => $studentId,
            'lettre_motivation' => 'Lettre',
            'cv_path' => null,
        ]);

        $rows = $model->getByStudent($studentId);
        $this->assertCount(1, $rows);
        $this->assertSame($offerTitle, $rows[0]['offre_titre'] ?? null);
        $this->assertSame($companyName, $rows[0]['entreprise_nom'] ?? null);
    }

    public function testGetById_whenNonExisting_returnsFalse(): void
    {
        $model = new ApplicationModel();
        $row = $model->getById(123456789);
        $this->assertFalse($row);
    }
}

