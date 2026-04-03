<?php
declare(strict_types=1);

final class UserModelTest extends BaseModelTestCase
{
    public function testGetByRole_whenUsersExist_returnsRows(): void
    {
        $model = new UserModel();
        $this->createUser(['role' => 'pilote', 'nom' => 'RoleUser1', 'prenom' => 'RU1']);
        $this->createUser(['role' => 'pilote', 'nom' => 'RoleUser2', 'prenom' => 'RU2']);

        $rows = $model->getByRole('pilote');
        $this->assertGreaterThanOrEqual(2, count($rows));

        foreach ($rows as $r) {
            $this->assertSame('pilote', $r['role']);
        }
    }

    public function testGetByRole_whenNoUsers_returnsEmptyArray(): void
    {
        $model = new UserModel();
        $rows = $model->getByRole('admin');
        $this->assertSame([], $rows);
    }

    public function testGetByEmail_whenEmailExists_returnsRow(): void
    {
        $email = 'email_' . uniqid('', true) . '@test.local';
        $id = $this->createUser(['email' => $email, 'role' => 'visiteur', 'nom' => 'EmailUser', 'prenom' => 'EU']);

        $model = new UserModel();
        $row = $model->getByEmail($email);

        $this->assertIsArray($row);
        $this->assertSame($id, (int)$row['id']);
        $this->assertSame($email, $row['email']);
    }

    public function testGetByEmail_whenEmailMissing_returnsNull(): void
    {
        $model = new UserModel();
        $row = $model->getByEmail('missing_' . uniqid('', true) . '@test.local');
        $this->assertNull($row);
    }

    public function testSearchByRoleAndName_whenMatches_returnsRows(): void
    {
        $model = new UserModel();
        $this->createUser(['role' => 'etudiant', 'nom' => 'AliceDupont', 'prenom' => 'AliceP']);
        $this->createUser(['role' => 'etudiant', 'nom' => 'Bob', 'prenom' => 'BobP']);
        // Même nom/term, mais rôle différent => ne doit pas matcher
        $this->createUser(['role' => 'visiteur', 'nom' => 'AliceWrongRole', 'prenom' => 'X']);

        $rows = $model->searchByRoleAndName('etudiant', 'Alice');
        $this->assertNotEmpty($rows);

        $names = array_map(static fn($r) => $r['nom'], $rows);
        $this->assertContains('AliceDupont', $names);
    }

    public function testSearchByRoleAndName_whenNoMatch_returnsEmptyArray(): void
    {
        $model = new UserModel();
        $this->createUser(['role' => 'etudiant', 'nom' => 'Someone', 'prenom' => 'Else']);
        $rows = $model->searchByRoleAndName('etudiant', '___NO_MATCH___' . uniqid('', true));
        $this->assertSame([], $rows);
    }

    public function testCreate_success_hashesPassword(): void
    {
        $rawPassword = 'MySecret_' . uniqid('', true) . '!123';
        $email = 'create_' . uniqid('', true) . '@test.local';

        $model = new UserModel();
        $id = $model->create([
            'prenom' => 'CP',
            'nom' => 'CN',
            'email' => $email,
            'mot_de_passe' => $rawPassword,
            'role' => 'visiteur',
            'id_pilote' => null,
        ]);

        $row = $this->pdo->query("SELECT mot_de_passe FROM utilisateur WHERE id = {$id} LIMIT 1")->fetch();
        $this->assertNotSame($rawPassword, $row['mot_de_passe']);
        $this->assertTrue(password_verify($rawPassword, $row['mot_de_passe']));
    }

    public function testCreate_whenEmailDuplicate_throwsPDOException(): void
    {
        $this->expectException(PDOException::class);

        $email = 'dup_' . uniqid('', true) . '@test.local';
        $model = new UserModel();
        $model->create([
            'prenom' => 'P1',
            'nom' => 'N1',
            'email' => $email,
            'mot_de_passe' => 'Password!123',
            'role' => 'visiteur',
            'id_pilote' => null,
        ]);

        $model->create([
            'prenom' => 'P2',
            'nom' => 'N2',
            'email' => $email,
            'mot_de_passe' => 'Password!456',
            'role' => 'visiteur',
            'id_pilote' => null,
        ]);
    }

    public function testUpdate_success_updatesPrenomAndNom(): void
    {
        $id = $this->createUser(['role' => 'visiteur', 'nom' => 'OldNom_' . uniqid('', true), 'prenom' => 'OldPrenom']);
        $model = new UserModel();

        $model->update($id, [
            'prenom' => 'NewPrenom_' . uniqid('', true),
            'nom' => $newNom = 'NewNom_' . uniqid('', true),
        ]);

        $row = $this->pdo->query("SELECT prenom, nom FROM utilisateur WHERE id = {$id}")->fetch();
        $this->assertSame($newNom, $row['nom']);
    }

    public function testUpdate_whenIdDoesNotExist_doesNotThrowAndKeepsCount(): void
    {
        $initialCount = (int)$this->pdo->query('SELECT COUNT(*) AS c FROM utilisateur')->fetch()['c'];
        $model = new UserModel();
        $model->update(9999999, [
            'prenom' => 'X',
            'nom' => 'Y',
        ]);
        $afterCount = (int)$this->pdo->query('SELECT COUNT(*) AS c FROM utilisateur')->fetch()['c'];
        $this->assertSame($initialCount, $afterCount);
    }

    public function testDelete_success_removesUser(): void
    {
        $id = $this->createUser(['role' => 'visiteur', 'nom' => 'ToDelete_' . uniqid('', true)]);
        $model = new UserModel();

        $model->delete($id);

        $row = $model->getUserById($id);
        $this->assertFalse($row);
    }

    public function testDelete_whenIdDoesNotExist_doesNotThrowAndKeepsCount(): void
    {
        $initialCount = (int)$this->pdo->query('SELECT COUNT(*) AS c FROM utilisateur')->fetch()['c'];
        $model = new UserModel();
        $model->delete(9999999);
        $afterCount = (int)$this->pdo->query('SELECT COUNT(*) AS c FROM utilisateur')->fetch()['c'];
        $this->assertSame($initialCount, $afterCount);
    }

    public function testVerifyPassword_whenCorrectPassword_returnsTrue(): void
    {
        $rawPassword = 'Verify_' . uniqid('', true) . '!123';
        $id = $this->createUser(['role' => 'visiteur', 'mot_de_passe' => $rawPassword]);

        $model = new UserModel();
        $this->assertTrue($model->verifyPassword($id, $rawPassword));
    }

    public function testVerifyPassword_whenWrongPassword_returnsFalse(): void
    {
        $rawPassword = 'VerifyWrong_' . uniqid('', true) . '!123';
        $id = $this->createUser(['role' => 'visiteur', 'mot_de_passe' => $rawPassword]);

        $model = new UserModel();
        $this->assertFalse($model->verifyPassword($id, 'wrong_' . uniqid('', true)));
    }

    public function testGetUserById_whenUserExists_returnsArray(): void
    {
        $id = $this->createUser(['role' => 'visiteur']);
        $model = new UserModel();

        $row = $model->getUserById($id);
        $this->assertIsArray($row);
        $this->assertSame($id, (int)$row['id']);
    }

    public function testGetUserById_whenUserDoesNotExist_returnsFalse(): void
    {
        $model = new UserModel();
        $row = $model->getUserById(9999999);
        $this->assertFalse($row);
    }

    public function testUpdatePilot_success_updatesIdPilote(): void
    {
        $pilotId = $this->createUser(['role' => 'pilote', 'nom' => 'Pilot_' . uniqid('', true), 'prenom' => 'P']);
        $studentId = $this->createUser([
            'role' => 'etudiant',
            'nom' => 'Student_' . uniqid('', true),
            'prenom' => 'S',
            'id_pilote' => null,
        ]);

        $model = new UserModel();
        $model->updatePilot($studentId, $pilotId);

        $row = $this->pdo->query("SELECT id_pilote FROM utilisateur WHERE id = {$studentId}")->fetch();
        $this->assertSame($pilotId, (int)$row['id_pilote']);
    }

    public function testUpdatePilot_whenStudentDoesNotExist_doesNotThrowAndKeepsData(): void
    {
        $pilotId = $this->createUser(['role' => 'pilote']);
        $studentId = $this->createUser(['role' => 'etudiant', 'id_pilote' => null]);

        $model = new UserModel();
        $model->updatePilot(9999999, $pilotId);

        $row = $this->pdo->query("SELECT id_pilote FROM utilisateur WHERE id = {$studentId}")->fetch();
        $this->assertNull($row['id_pilote']);
    }
}

