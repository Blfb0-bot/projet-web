<?php
try {
    $pdo = new PDO("mysql:host=127.0.0.1;dbname=projet_web", "root", "Beuvry/0710");
    echo "✅ Connexion réussie en direct !";
} catch (Exception $e) {
    echo "❌ Échec direct : " . $e->getMessage();
}