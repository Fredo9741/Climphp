<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Relevés Sauvegardés - Clim-Pro</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@1/css/pico.min.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Relevés Sauvegardés</h1>
        <p>Liste des Relevés Sauvegardés</p>
    </header>

    <nav>
        <ul>
            <li><a href="index.html">Accueil</a></li>
            <li><a href="diagnostics-sauvegardes.php">Relevés Sauvegardés</a></li>
        </ul>
    </nav>

    <main class="container">
        <div id="saved-diagnostics">
            <?php
            // Charger les diagnostics depuis le stockage
            $filePath = 'saved_diagnostics.json';
            if (file_exists($filePath)) {
                $savedDiagnostics = json_decode(file_get_contents($filePath), true);
                if (empty($savedDiagnostics)) {
                    echo '<p>Aucun diagnostic sauvegardé.</p>';
                } else {
                    // Tableau des labels lisibles
                    $labels = [
                        "denomination" => "Dénomination",
                        "modele" => "Modèle",
                        "puissance" => "Puissance (Btu)",
                        "type-gaz" => "Type de gaz",
                        "poids-gaz" => "Quantité de gaz (kg)",
                        "emplacement" => "Emplacement",
                        "temp-ambiante" => "Température ambiante intérieure",
                        "temp-exterieure" => "Température extérieure",
                        "evap-air-in" => "Entrée évaporateur (air)",
                        "evap-air-out" => "Sortie évaporateur (air)",
                        "cond-air-in" => "Entrée condenseur (air)",
                        "cond-air-out" => "Sortie condenseur (air)",
                        "evap-pipe-out" => "Sortie évaporateur (tuyau)",
                        "cond-pipe-out" => "Sortie condenseur (tuyau)",
                        "comp-discharge" => "Refoulement compresseur",
                        "bp-off" => "Pression BP à l'arrêt",
                        "bp-on" => "Pression BP en fonctionnement",
                        "hp-on" => "Pression HP en fonctionnement",
                        "hp-off" => "Pression HP à l'arrêt",
                        "bp-temp" => "Température BP",
                        "hp-temp" => "Température HP",
                        "amp-demarrage" => "Ampérage au démarrage",
                        "amp-fonctionnement" => "Ampérage en fonctionnement",
                        "photo-unite" => "Unité complète",
                        "photo-evaporateur" => "Évaporateur",
                        "photo-condenseur" => "Condenseur",
                        "photo-compresseur" => "Compresseur",
                        "photo-plaque-interieure" => "Plaque signalétique unité intérieure",
                        "photo-plaque-exterieure" => "Plaque signalétique unité extérieure"
                    ];

                    foreach ($savedDiagnostics as $index => $diagnostic) {
                        echo '<div class="section">';
                        echo '<h2>' . htmlspecialchars($diagnostic['fullName'] ?? 'Diagnostic sans nom') . '</h2>';
                        echo '<table class="diagnostic-table">';
                        foreach ($labels as $key => $label) {
                            echo '<tr>';
                            echo '<td class="diagnostic-label">' . htmlspecialchars($label) . '</td>';
                            if (isset($diagnostic[$key])) {
                                if (strpos($key, 'photo-') === 0) {
                                    echo '<td><img src="' . htmlspecialchars($diagnostic[$key]) . '" style="max-width: 200px;"></td>';
                                } else {
                                    echo '<td>' . htmlspecialchars($diagnostic[$key]) . '</td>';
                                }
                            } else {
                                echo '<td>Non spécifié</td>';
                            }
                            echo '</tr>';
                        }
                        echo '</table>';
                        // Ajouter un bouton de suppression
                        echo '<form action="delete_diagnostic.php" method="post">';
                        echo '<input type="hidden" name="index" value="' . $index . '">';
                        echo '<button type="submit" class="button">Supprimer</button>';
                        echo '</form>';
                        echo '</div>';
                    }
                }
            } else {
                echo '<p>Aucun diagnostic sauvegardé.</p>';
            }
            ?>
        </div>
    </main>

    <footer class="container">
        <small><a href="#">Conditions d'utilisation</a> • <a href="#">Politique de confidentialité</a></small>
    </footer>
</body>
</html>
