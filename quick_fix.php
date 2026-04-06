<?php
$dir = "C:/xampp/htdocs/Poin_Pelanggaran_Siswa";
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));

foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'php' && !in_array($file->getFilename(), ['ROOTPATH_update.php', 'config.php'])) {
        $path = $file->getRealPath();
        $content = file_get_contents($path);
        
        // Remove the specific messy fragment
        $search = ", \$_SERVER['DOCUMENT_ROOT'] . '/Poin_Pelanggaran_Siswa');";
        if (strpos($content, $search) !== false) {
            $newContent = str_replace($search, "", $content);
            file_put_contents($path, $newContent);
            echo "Cleaned fragment in: " . $path . "\n";
        }
    }
}
?>
