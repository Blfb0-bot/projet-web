INSERT INTO competence (libelle) VALUES
('PHP'),('JavaScript'),('React'),('Node.js'),('Python'),
('SQL'),('Docker'),('Git'),('Java'),('C#'),
('DevOps'),('Data Science'),('Machine Learning'),
('Réseaux'),('Cybersécurité'),('Flutter'),('iOS'),('Android'),
('REST API'),('Linux');


INSERT INTO utilisateur (nom, prenom, email, mot_de_passe, role) VALUES
('Admin','Système','admin@cesi.fr', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

INSERT INTO utilisateur (nom, prenom, email, mot_de_passe, role) VALUES
('Dupont','Paul','p.dupont@cesi.fr', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'pilote'),
('Bernard','Marie','m.bernard@cesi.fr','$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'pilote');

INSERT INTO utilisateur (nom, prenom, email, mot_de_passe, role, id_pilote) VALUES
('Martin','Thomas','t.martin@cesi.fr','$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','etudiant',2),
('Leroy','Sophie','s.leroy@cesi.fr','$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','etudiant',2),
('Petit','Lucas','l.petit@cesi.fr','$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','etudiant',3);


INSERT INTO entreprise (nom, description, email, telephone) VALUES
('SoftCorp','Éditeur de solutions SaaS B2B depuis 2010. 250 collaborateurs en France et Europe.','contact@softcorp.fr','01 23 45 67 89'),
('Allianz Innovation','Pôle innovation du groupe Allianz, axé Data Science et IA appliquée à la finance.','stages@allianz-innovation.fr','01 34 56 78 90'),
('Nexia Tech','Société spécialisée en infrastructure réseau et cybersécurité pour les PME.','rh@nexiatech.com','05 67 89 01 23'),
('DataVault','Startup DevOps – solutions cloud et CI/CD pour les équipes engineering.','jobs@datavault.io','04 56 78 90 12'),
('MachineCore','Laboratoire d''IA appliquée, spécialisé NLP et Computer Vision.','recrutement@machinecore.ai','01 45 67 89 01'),
('GlobeTech','Agence mobile cross-platform, développement Flutter et React Native.','hello@globetech.fr','02 34 56 78 90');

-- Offres
INSERT INTO offre (titre, type_annonce, description, remuneration, date_debut, date_fin, duree_mois, id_entreprise) VALUES
('Développeur Full-Stack React/Node.js','stage','Stage axé sur la mise en pratique des connaissances théoriques, le développement de compétences professionnelles et la participation active aux missions de l''équipe, dans un environnement dynamique et formateur.',800.00,'2026-03-01','2026-09-01',6,1),
('Data Scientist Junior','stage','Rejoignez notre équipe Data et participez à l''analyse de données d''assurance. Vous serez formé aux outils Python/Pandas et contribuerez à la construction de modèles prédictifs.',900.00,'2026-04-01','2026-10-01',6,2),
('Ingénieur Réseaux & Sécurité','stage','Mission de stage en infrastructure réseau : supervision, configuration de pare-feux, tests de pénétration supervisés et documentation.',750.00,'2026-03-15','2026-07-15',4,3),
('Ingénieur DevOps','alternance','Participation à l''automatisation des pipelines CI/CD, containerisation Docker/Kubernetes et monitoring des services cloud.',950.00,'2026-03-01','2026-09-01',6,4),
('Stage IA – NLP Engineer','stage','Développement de modèles de traitement du langage naturel (NLP) : classification de textes, extraction d''entités, résumé automatique.',1000.00,'2026-04-01','2026-10-01',6,5),
('Développeur Mobile Flutter','stage','Développement d''une application mobile cross-platform Flutter. Vous participerez à la conception UI/UX et à l''intégration des APIs REST.',700.00,'2026-03-01','2026-07-01',4,6);


INSERT INTO offre_competence (id_offre, id_competence) VALUES
(1,1),(1,3),(1,4),(1,6),(1,8),
(2,5),(2,6),(2,12),(2,13),
(3,14),(3,15),(3,20),
(4,7),(4,8),(4,11),(4,20),
(5,5),(5,13),(5,19),
(6,16),(6,2),(6,19);


INSERT INTO evaluation (id_entreprise, id_etudiant, note, commentaire) VALUES
(1,4,4,'Très bonne ambiance, équipe sympa et projets intéressants.'),
(1,5,5,'Excellente expérience, j''ai beaucoup appris !'),
(2,6,5,'Super stage, encadrement de qualité.'),
(3,4,3,'Offre correcte mais peu de projets innovants.');
