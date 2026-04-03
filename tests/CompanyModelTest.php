<?php
declare(strict_types=1);

final class CompanyModelTest extends BaseModelTestCase
{
    public function testCreate_success_insertsAndReturnsId(): void
    {
        $name = 'Company_' . uniqid('', true);
        $model = new CompanyModel();

        $id = $model->create([
            'nom' => $name,
            'description' => 'desc',
            'email' => 'contact_' . uniqid('', true) . '@test.local',
            'telephone' => '0000000001',
        ]);

        $this->assertGreaterThan(0, $id);

        $row = $this->pdo->query("SELECT nom FROM entreprise WHERE id = {$id}")->fetch();
        $this->assertSame($name, $row['nom']);
    }

    public function testFindIdByNom_whenNomIsEmpty_returnsNull(): void
    {
        $model = new CompanyModel();
        $this->assertNull($model->findIdByNom(''));
    }

    public function testCreerEvaluation_whenDuplicatePair_throwsPDOException(): void
    {
        $this->expectException(PDOException::class);

        $companyId = $this->createCompany(['nom' => 'EvalCo_' . uniqid('', true)]);
        $studentId = $this->createUser(['role' => 'etudiant', 'nom' => 'EvalStudent', 'prenom' => 'ES']);

        $model = new CompanyModel();
        $model->creerEvaluation([
            'id_entreprise' => $companyId,
            'id_etudiant' => $studentId,
            'note' => 4,
            'commentaire' => 'Super',
        ]);

        // Deuxième évaluation pour la même paire -> UNIQUE uq_evaluation
        $model->creerEvaluation([
            'id_entreprise' => $companyId,
            'id_etudiant' => $studentId,
            'note' => 5,
            'commentaire' => 'Duplicate',
        ]);
    }
}

