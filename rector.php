<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;

$paths = array_map(fn ($dir) => __DIR__.$dir, ['/app', '/bootstrap', '/config', '/public', '/routes', '/tests']);

return RectorConfig::configure()
    ->withPaths($paths)
    ->withParallel()
    ->withPreparedSets(
        deadCode: true,
        codeQuality: true,
        typeDeclarations: true,
        privatization: true,
        earlyReturn: true,
        strictBooleans: true,
    )
    ->withPhpSets();
