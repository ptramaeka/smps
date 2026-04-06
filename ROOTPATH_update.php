<?php
$dir = "C:/xampp/htdocs/smps";
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));

foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'php' && !in_array($file->getFilename(), ['ROOTPATH_update.php', 'config.php'])) {
        $path = $file->getRealPath();
        $content = file_get_contents($path);
        $newContent = $content;

        // 1. Fix messy lines (previous failed replacements)
        $newContent = preg_replace("/include_once __DIR__ \. '[^']+';, \$_SERVER\['DOCUMENT_ROOT'\] \. '\/smps'\);/", "", $newContent);
        
        // 2. Remove old defines (ROOTPATH or ROOT_PATH)
        $newContent = preg_replace("/define\('(ROOTPATH|ROOT_PATH)', \$_SERVER\['DOCUMENT_ROOT'\] \. '\/smps'\);\s*/", "", $newContent);
        
        // 3. Remove redundant config includes
        $newContent = preg_replace("/include (ROOTPATH|ROOT_PATH) \. \"\/config\/config\.php\";\s*/", "", $newContent);
        
        // 4. Ensure there is only ONE correct include at the top
        // First remove all our dynamic includes to reset
        $newContent = preg_replace("/include_once __DIR__ \. '\/[.\/]+config\/config\.php';\s*/", "", $newContent);

        // 5. Calculate correct dots for this file
        $relativePath = str_ireplace($dir, '', str_replace('\\', '/', $path));
        $relativePath = ltrim($relativePath, '/');
        $levels = substr_count($relativePath, '/');
        $dots = str_repeat('../', $levels);
        if ($levels == 0) $dots = './';

        // 6. Prepend the correct include if it's a page or process file
        if (strpos($relativePath, 'pages/') === 0 || strpos($relativePath, 'process/') === 0) {
            $includeLine = "<?php\ninclude_once __DIR__ . '/{$dots}config/config.php';\n";
            if (strpos($newContent, "<?php") === 0) {
                $newContent = $includeLine . ltrim(substr($newContent, 5));
            } else {
                $newContent = $includeLine . $newContent;
            }
        }

        // 7. Replace any remaining ROOTPATH with ROOT_PATH
        $newContent = str_replace("ROOTPATH", "ROOT_PATH", $newContent);
        
        // 8. Replace hardcoded project URL in form actions
        $newContent = str_replace('action="/smps/', 'action="<?= BASE_URL ?>/', $newContent);

        // 9. Clean up multiple open tags and newlines
        $newContent = preg_replace("/^<\?php\s+<\?php/m", "<?php", $newContent);
        $newContent = preg_replace("/\?>\s+<\?php/m", "", $newContent); // Merge contiguous PHP blocks

        if ($newContent !== $content) {
            file_put_contents($path, $newContent);
            echo "Cleaned Up: " . $relativePath . "\n";
        }
    }
}
?>
