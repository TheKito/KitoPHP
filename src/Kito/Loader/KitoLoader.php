<?php

declare(strict_types=1);

/**
 * KitoLoader.
 *
 * php version 7.2.
 *
 * @category Loader
 *
 * @author  The Kito <thekito@blktech.com>
 * @license http://opensource.org/licenses/gpl-license.php GNU GPL
 *
 * @link https://github.com/TheKito/KitoPHP
 */

namespace Kito\Loader;

class KitoLoader extends AbstractLoader
{
    public const server = 'https://raw.githubusercontent.com/TheKito/KitoPHP/main/src/';

    private $cacheLoader;

    public function __construct(PSR0Loader $cacheLoader)
    {
        $this->cacheLoader = $cacheLoader;
    }

    /**
     * Get index url for from class.
     *
     * @param string $className Class name with namespace
     */
    public static function getClassURL(string $className): string
    {
        return self::server.str_replace('\\', '/', self::parsePath($className).'.php');
    }

    /**
     * Download class from repo.
     *
     * @param string $className Class name with namespace
     */
    protected function loadClass(string $className): void
    {
        if ($this->cacheLoader->loadClassHelper($className)) {
            return;
        }

        $data = file_get_contents(self::getClassURL($className));
        $data = @file_get_contents(self::getClassURL($className));

        if ($data == false) {
            return;
        }

        $file = $this->cacheLoader->getFileName($className);
        $dir = pathinfo($file, PATHINFO_DIRNAME);

        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }

        file_put_contents($file, $data);

        $this->cacheLoader->loadClassHelper($className);
    }
}
