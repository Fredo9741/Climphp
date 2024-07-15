<?php
// Fonction pour créer un nom de fichier sécurisé
function createSafeFileName($str) {
    $str = preg_replace('/[^A-Za-z0-9_\-]/', '_', $str);
    return $str;
}

// Charger les diagnostics existants
$filePath = 'saved_diagnostics.json';
$savedDiagnostics = [];
if (file_exists($filePath)) {
    $savedDiagnostics = json_decode(file_get_contents($filePath), true);
}

// Créer un nouveau diagnostic
$diagnostic = [];
foreach ($_POST as $key => $value) {
    if (!empty($value)) {
        $diagnostic[$key] = htmlspecialchars($value);
    }
}

// Détails pour nommer les fichiers
$date = date('Y-m-d');
$emplacement = !empty($_POST['emplacement']) ? createSafeFileName($_POST['emplacement']) : 'EmplacementInconnu';
$denomination = !empty($_POST['denomination']) ? createSafeFileName($_POST['denomination']) : 'DénominationInconnue';
$fileBaseName = "{$date}_{$emplacement}_{$denomination}";

// Ajouter le fullName au diagnostic
$fullName = "{$date} - {$emplacement} - {$denomination}";
$diagnostic['fullName'] = $fullName;

// Sauvegarder les photos
if (!empty($_FILES)) {
    $photoDir = 'photo/';
    if (!is_dir($photoDir)) {
        mkdir($photoDir, 0755, true);
    }
    foreach ($_FILES as $key => $file) {
        if ($file['error'] == UPLOAD_ERR_OK && is_uploaded_file($file['tmp_name'])) {
            $fileName = "{$key}_{$fileBaseName}.jpg";
            $targetFile = $photoDir . $fileName;
            move_uploaded_file($file['tmp_name'], $targetFile);
            $diagnostic[$key] = $targetFile;
        }
    }
}

// Ajouter le diagnostic aux diagnostics sauvegardés
$savedDiagnostics[] = $diagnostic;
file_put_contents($filePath, json_encode($savedDiagnostics));

// Sauvegarder la fiche de diagnostic dans un fichier séparé
$diagnosticDir = 'Relevé de température/';
if (!is_dir($diagnosticDir)) {
    mkdir($diagnosticDir, 0755, true);
}
$diagnosticFileName = "Temperature_{$fileBaseName}.json";
file_put_contents($diagnosticDir . $diagnosticFileName, json_encode($diagnostic));

// Rediriger l'utilisateur vers la page de diagnostics sauvegardés
header('Location: diagnostics-sauvegardes.php');
exit;
?>
