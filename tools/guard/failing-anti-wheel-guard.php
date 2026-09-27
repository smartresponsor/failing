<?php

declare(strict_types=1);

$root = dirname(__DIR__, 2);
$src = $root . '/src';
$violations = [];

$forbiddenConsumerTokens = [
    'Billing',
    'Shipping',
    'Paying',
    'Payment',
    'Ordering',
    'Cruding',
    'Invoice',
    'Shipment',
];

$forbiddenDeclarations = [
    '/\b(?:class|interface|trait|enum)\s+\w*HttpException\b/',
    '/\b(?:class|interface|trait|enum)\s+HttpStatus\b/',
    '/\b(?:class|interface|trait|enum)\s+\w*EventDispatcher\b/',
    '/\b(?:class|interface|trait|enum)\s+\w*HttpKernel\b/',
];

$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($src, FilesystemIterator::SKIP_DOTS));
foreach ($iterator as $file) {
    if (!$file instanceof SplFileInfo || !$file->isFile() || 'php' !== strtolower($file->getExtension())) {
        continue;
    }

    $path = str_replace('\\', '/', substr($file->getPathname(), strlen($root) + 1));
    $content = (string) file_get_contents($file->getPathname());

    foreach ($forbiddenConsumerTokens as $token) {
        if (str_contains($content, $token)) {
            $violations[] = sprintf('%s contains forbidden consumer/business token "%s".', $path, $token);
        }
    }

    foreach ($forbiddenDeclarations as $pattern) {
        if (1 === preg_match($pattern, $content)) {
            $violations[] = sprintf('%s declares framework-replacement machinery matched by %s.', $path, $pattern);
        }
    }
}

if ([] !== $violations) {
    fwrite(STDERR, "Failing anti-wheel guard failed:\n- " . implode("\n- ", $violations) . "\n");
    exit(1);
}

echo "Failing anti-wheel guard passed.\n";
