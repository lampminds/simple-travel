<?php

$files = [
    __DIR__.'/../database/seeders/CatServiceTypeTranslationsTableSeeder.php',
    __DIR__.'/../database/seeders/CatServiceActivityTypeTranslationsTableSeeder.php',
    __DIR__.'/../database/seeders/CatServiceActivityTypeCategoryTranslationsTableSeeder.php',
    __DIR__.'/../database/seeders/CatServiceFeatureTranslationsTableSeeder.php',
    __DIR__.'/../app/Console/Commands/SeedServiceActivityTypesCommand.php',
    __DIR__.'/../routes/console.php',
];

$replacements = [
    'Ã¡' => 'á',
    'Ã©' => 'é',
    'Ã­' => 'í',
    'Ã³' => 'ó',
    'Ãº' => 'ú',
    'Ã' => 'Á',
    'Ã‰' => 'É',
    'Ã' => 'Í',
    'Ã“' => 'Ó',
    'Ãš' => 'Ú',
    'Ã±' => 'ñ',
    'Ã‘' => 'Ñ',
    'Ã¼' => 'ü',
    'Ãœ' => 'Ü',
    'Â¿' => '¿',
    'Â¡' => '¡',
    'â€“' => '–',
    'â€”' => '—',
    'â€œ' => '“',
    'â€' => '”',
    'â€˜' => '‘',
    'â€™' => '’',
    'â€¦' => '…',
    'â†’' => '→',
    'Ã‚' => '',
    'Â' => '',
];

foreach ($files as $file) {
    $content = file_get_contents($file);
    if ($content === false) {
        fwrite(STDERR, "Cannot read {$file}\n");
        continue;
    }

    $fixed = strtr($content, $replacements);

    if ($fixed === $content) {
        echo "unchanged {$file}\n";
        continue;
    }

    file_put_contents($file, $fixed);
    echo "fixed {$file}\n";
}
