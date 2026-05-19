<?php
$dir = __DIR__ . '/views';
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
$replacements = [
    'Ã¡' => 'á',
    'Ã©' => 'é',
    'Ã­' => 'í',
    'Ã³' => 'ó',
    'Ãº' => 'ú',
    'Ã±' => 'ñ',
    'Ã“' => 'Ó',
    'Ãš' => 'Ú',
    'Â¿' => '¿',
    'Â¡' => '¡',
    'Ã‰' => 'É',
    'Ã‘' => 'Ñ',
    'Ã?' => 'Í',
    'Ã¡' => 'á'
];

foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $content = file_get_contents($file->getPathname());
        $newContent = strtr($content, $replacements);
        if ($content !== $newContent) {
            file_put_contents($file->getPathname(), $newContent);
            echo "Fixed: " . $file->getPathname() . "\n";
        }
    }
}
?>
