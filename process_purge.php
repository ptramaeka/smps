<?php
$dir = realpath("C:/xampp/htdocs/Poin_Pelanggaran_Siswa/process");
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));

foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $path = $file->getRealPath();
        $content = file_get_contents($path);
        
        // Match various hardcoded project folder names in JS redirects
        $newContent = preg_replace("/window\.location\.href = '\/Poin_Pelanggaran_Siswa[^']*\/pages\//", "window.location.href = '<?= BASE_URL ?>/pages/", $content);
        
        if ($newContent !== $content) {
            file_put_contents($path, $newContent);
            echo "Purged Process JS: " . $path . "\n";
        }
    }
}
?>
