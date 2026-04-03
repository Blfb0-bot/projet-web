<?php
declare(strict_types=1);

final class CompanyModelTest extends BaseModelTestCase
{
    public function testGetAll_whenCompanyExists_returnsCompanies(): void
    {
        $companyId = $this->createCompany(['nom' => $name = 'Company_' . uniqid('', true)]);
        $model = new CompanyModel();

        $rows = $model->getAll();
        $this->assertCount(1, $rows);
        $this->assertSame($companyId, (int)$rows[0]['id']);
        $this->assertSame($name, $rows[0]['nom']);
    }

    public function testGetAll_whenNoCompany_returnsEmptyArray(): void
    {
        $model = new CompanyModel();
        $rows = $model->getAll();
        $this->assertSame([], $rows);
    }

    public function testFindIdByNom_whenCompanyExists_returnsId(): void
    {
        $name = 'Company_' . uniqid('', true);
        $companyId = $this->createCompany(['nom' => $name]);
        $model = new CompanyModel();

        $found = $model->findIdByNom($name);
        $this->assertSame($companyId, $found);
    }

    public function testFindIdByNom_whenNomIsEmpty_returnsNull(): void
    {
        $model = new CompanyModel();
        $this->assertNull($model->findIdByNom(''));
    }

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

    public function testCreate_whenNomIsNull_throwsPDOException(): void
    {
        $this->expectException(PDOException::class);

        $model = new CompanyModel();
        $model->create([
            'nom' => null,
            'description' => 'desc',
            'email' => 'contact_' . uniqid('', true) . '@test.local',
            'telephone' => '0000000002',
        ]);
    }

    public function testUpdate_success_updatesRow(): void
    {
        $id = $this->createCompany(['nom' => 'Before_' . uniqid('', true)]);
        $model = new CompanyModel();

        $model->update($id, [
            'nom' => $newName = 'After_' . uniqid('', true),
            'description' => 'newdesc',
            'email' => 'new_' . uniqid('', true) . '@test.local',
            'telephone' => '0000000003',
        ]);

        $row = $this->pdo->query("SELECT nom, description, email, telephone FROM entreprise WHERE id = {$id}")->fetch();
        $this->assertSame($newName, $row['nom']);
        $this->assertSame('newdesc', $row['description']);
    }

    public function testUpdate_whenIdDoesNotExist_doesNotCreateRow(): void
    {
        $initialCount = (int)$this->pdo->query('SELECT COUNT(*) AS c FROM entreprise')->fetch()['c'];
        $model = new CompanyModel();

        $model->update(9999999, [
            'nom' => 'X',
            'description' => 'Y',
            'email' => 'x_' . uniqid('', true) . '@test.local',
            'telephone' => '0000000004',
        ]);

        $afterCount = (int)$this->pdo->query('SELECT COUNT(*) AS c FROM entreprise')->fetch()['c'];
        $this->assertSame($initialCount, $afterCount);
    }

    public function testDelete_success_removesRow(): void
    {
        $id = $this->createCompany(['nom' => 'ToDelete_' . uniqid('', true)]);
        $model = new CompanyModel();

        $model->delete($id);

        $row = $this->pdo->query("SELECT COUNT(*) AS c FROM entreprise WHERE id = {$id}")->fetch();
        $this->assertSame(0, (int)$row['c']);
    }

    public function testDelete_whenIdDoesNotExist_doesNotThrowAndKeepsCount(): void
    {
        $initialCount = (int)$this->pdo->query('SELECT COUNT(*) AS c FROM entreprise')->fetch()['c'];
        $model = new CompanyModel();

        $model->delete(9999999);

        $afterCount = (int)$this->pdo->query('SELECT COUNT(*) AS c FROM entreprise')->fetch()['c'];
        $this->assertSame($initialCount, $afterCount);
    }

    public function testSearchByName_whenMatches_returnsRows(): void
    {
        $model = new CompanyModel();
        $this->createCompany(['nom' => $n1 = 'Comp_A_' . uniqid('', true)]);
        $this->createCompany(['nom' => $n2 = 'Comp_B_' . uniqid('', true)]);

        $term = 'Comp_';
        $rows = $model->searchByName($term);
        $this->assertGreaterThanOrEqual(2, count($rows));
        $names = array_column($rows, 'nom');
        $this->assertContains($n1, $names);
        $this->assertContains($n2, $names);
    }

    public function testSearchByName_whenNoMatch_returnsEmptyArray(): void
    {
        $model = new CompanyModel();
        $this->createCompany(['nom' => 'OnlyOne_' . uniqid('', true)]);

        $rows = $model->searchByName('___NoMatch___' . uniqid('', true));
        $this->assertSame([], $rows);
    }

    public function testCreerEvaluation_success_insertsEvaluation(): void
    {
        $companyId = $this->createCompany(['nom' => 'EvalCo_' . uniqid('', true)]);
        $studentId = $this->createUser(['role' => 'etudiant', 'nom' => 'EvalStudent', 'prenom' => 'ES']);

        $model = new CompanyModel();
        $model->creerEvaluation([
            'id_entreprise' => $companyId,
            'id_etudiant' => $studentId,
            'note' => 4,
            'commentaire' => 'Super',
        ]);

        $row = $this->pdo->query(
            "SELECT note, commentaire FROM evaluation WHERE id_entreprise = {$companyId} AND id_etudiant = {$studentId} LIMIT 1"
        )->fetch();
        $this->assertSame(4, (int)$row['note']);
        $this->assertSame('Super', $row['commentaire']);
    }

    public function testCreerEvaluation_whenDuplicatePair_throwsPDOException(): void
    {
        $this->expectException(PDOException::class);

        $companyId = $this->createCompany(['nom' => 'EvalCo_' . uniqid('', true)]);
        $studentId = $this->createUser(['role' => 'etudiant', 'nom' => 'EvalStudent', 'prenom' => 'ES2']);

        $model = new CompanyModel();
        $model->creerEvaluation([
            'id_entreprise' => $companyId,
            'id_etudiant' => $studentId,
            'note' => 3,
            'commentaire' => 'Ok',
        ]);

        $model->creerEvaluation([
            'id_entreprise' => $companyId,
            'id_etudiant' => $studentId,
            'note' => 5,
            'commentaire' => 'Duplicate',
        ]);
    }
}

