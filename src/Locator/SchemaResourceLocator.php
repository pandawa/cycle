<?php

declare(strict_types=1);

namespace Pandawa\Cycle\Locator;

use Illuminate\Support\Str;
use Pandawa\Component\Loader\LoaderInterface;
use Pandawa\Cycle\Contract\SchemaResourceLocator as SchemaResourceLocatorContract;
use SplFileInfo;
use Symfony\Component\Finder\Finder;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class SchemaResourceLocator implements SchemaResourceLocatorContract
{
    public function __construct(
        private readonly LoaderInterface $loader,
        private readonly ?string $directory = null,
    ) {
    }

    public function compile(array $defaults = []): array
    {
        if (null === $this->directory) {
            return [];
        }

        $schemas = [];
        foreach (Finder::create()->files()->in($this->directory) as $file) {
            $key = $this->generateKey($file);

            $schemas[$key] = $defaults + $this->loader->load($file->getRealPath());
        }

        return $schemas;
    }

    private function generateKey(SplFileInfo $file): string
    {
        return Str::kebab(pathinfo($file->getBasename(), PATHINFO_FILENAME));
    }
}
