<?php
$dir = realpath("C:/xampp/htdocs/smps/process");
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));

foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $path = $file->getRealPath();
        $content = file_get_contents($path);
        
        // Match various hardcoded project folder names in JS redirects
        $newContent = preg_replace("/window\.location\.href = '\/smps[^']*\/pages\//", "window.location.href = '<?= BASE_URL ?>/pages/", $content);
        
        if ($newContent !== $content) {
            file_put_contents($path, $newContent);
            echo "Purged Process JS: " . $path . "\n";
        }
    }
}
?>
