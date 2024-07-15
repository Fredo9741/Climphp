<?php
// Fonction pour créer un nom de fichier sécurisé
function createSafeFileName($str) {
    $str = preg_replace('/[^A-Za-z0-9_\-]/', '_', $str);
    return $str;
}

// Vérifier si l'index du diagnostic à supprimer est défini
if (isset($_POST['index'])) {
    $index = (int)$_POST['index'];
    
    // Charger les diagnostics existants
    $filePath = 'saved_diagnostics.json';
    if (file_exists($filePath)) {
        $savedDiagnostics = json_decode(file_get_contents($filePath), true);
        
        if (isset($savedDiagnostics[$index])) {
            // Créer le dossier Archive s'il n'existe pas
            $archiveDir = 'Archive/';
            if (!is_dir($archiveDir)) {
                if (!mkdir($archiveDir, 0755, true)) {
                    die('Erreur : impossible de créer le dossier Archive.');
                }
            }
            
            // Détails pour nommer les fichiers archivés
            $date = date('Y-m-d');
            $emplacement = !empty($savedDiagnostics[$index]['emplacement']) ? createSafeFileName($savedDiagnostics[$index]['emplacement']) : 'EmplacementInconnu';
            $denomination = !empty($savedDiagnostics[$index]['denomination']) ? createSafeFileName($savedDiagnostics[$index]['denomination']) : 'DénominationInconnue';
            $fileBaseName = "{$date}_{$emplacement}_{$denomination}";

            // Déplacer les photos associées
            foreach ($savedDiagnostics[$index] as $key => $value) {
                if (strpos($key, 'photo-') === 0 && file_exists($value)) {
                    $photoFileName = basename($value);
                    if (!rename($value, $archiveDir . $photoFileName)) {
                        die('Erreur : impossible de déplacer la photo ' . $photoFileName);
                    }
                }
            }

            // Sauvegarder la fiche de diagnostic dans le dossier Archive avec un nom unique
            $diagnosticFileName = "Temperature_{$fileBaseName}.json";
            $counter = 1;
            while (file_exists($archiveDir . $diagnosticFileName)) {
                $diagnosticFileName = "Temperature_{$fileBaseName}_{$counter}.json";
                $counter++;
            }
            if (!file_put_contents($archiveDir . $diagnosticFileName, json_encode($savedDiagnostics[$index]))) {
                die('Erreur : impossible de sauvegarder la fiche de diagnostic dans le dossier Archive.');
            }

            // Supprimer la fiche de diagnostic du dossier Relevé de température
            $originalDiagnosticFileName = "Relevé de température/Temperature_{$fileBaseName}.json";
            $counter = 1;
            while (!file_exists($originalDiagnosticFileName) && $counter < 100) {  // Ajout d'une limite pour éviter une boucle infinie
                $originalDiagnosticFileName = "Relevé de température/Temperature_{$fileBaseName}_{$counter}.json";
                $counter++;
            }
            if (file_exists($originalDiagnosticFileName)) {
                unlink($originalDiagnosticFileName);
            }
            
            // Supprimer le diagnostic du fichier JSON principal
            array_splice($savedDiagnostics, $index, 1);
            if (!file_put_contents($filePath, json_encode($savedDiagnostics))) {
                die('Erreur : impossible de mettre à jour le fichier des diagnostics sauvegardés.');
            }
        }
    }
}

// Rediriger l'utilisateur vers la page de diagnostics sauvegardés
header('Location: diagnostics-sauvegardes.php');
exit;
?>
