<?php
$dir = realpath("C:/xampp/htdocs/Poin_Pelanggaran_Siswa");
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));

foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'php' && !in_array($file->getFilename(), ['ROOTPATH_update.php', 'quick_fix.php', 'final_purge.php', 'quick_fix.php'])) {
        $path = $file->getRealPath();
        $content = file_get_contents($path);
        
        $newContent = str_replace('/Poin_Pelanggaran_Siswa/', '<?= BASE_URL ?>/', $content);
        $newContent = str_replace('<?= BASE_URL ?><?= BASE_URL ?>', '<?= BASE_URL ?>', $newContent);
        
        if (basename($path) === 'header.php') {
            $newContent = str_replace('<?= BASE_URL ?>', '<?php echo BASE_URL; ?>', $newContent);
        }

        if ($newContent !== $content) {
            file_put_contents($path, $newContent);
            echo "Purged: " . $path . "\n";
        }
    }
}
?>
