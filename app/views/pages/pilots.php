<!--Le contenue de la page-->
<section id="presentation-pilote">
    <h1>Nos Pilotes</h1>
</section>
<section id="outils-pilote">
    <button id="creation-offre" onclick="ouvrir('popup-creer-pilote')">créer un pilote</button>
</section>
<section id="nos-pilote">
    <div class="pilote">
        <div class="table-pilote">
            <table>
                <thead>
                    <tr>
                        <th>Prenom</th>
                        <th>Nom</th>
                        <th>outils</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach (($pilots ?? []) as $pilot): ?>
                        <tr>
                            <td><?= htmlspecialchars((string)($pilot['prenom'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars((string)($pilot['nom'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                            <td>
                                <button onclick="ouvrir('popup-modifier-pilote')">modifier</button>
                                <button onclick="ouvrir('popup-supprimer-pilote')">supprimer</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>