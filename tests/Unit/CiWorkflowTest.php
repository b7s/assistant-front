<?php

declare(strict_types=1);

use Symfony\Component\Yaml\Yaml;

test('CI tests supported PHP versions', function (): void {
    $workflow = Yaml::parseFile(dirname(__DIR__, 2).'/.github/workflows/tests.yml');

    expect($workflow['jobs']['ci']['strategy']['matrix']['php-version'])
        ->toBe(['8.4', '8.5']);
});
