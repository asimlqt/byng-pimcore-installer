<?php

namespace Byng;

use Composer\Script\Event;
use Composer\Util\Filesystem;
use Composer\Installer\PackageEvent;

class Composer
{

    public static function postInstall(Event $event)
    {
        $config = $event->getComposer()->getConfig();
        $vendorPath = $config->get('vendor-dir');
        $rootPath = dirname($vendorPath);
                
        $filesystem = new Filesystem();

        if (!file_exists($rootPath."/index.php")) {
            self::copyFolder($vendorPath . "/pimcore/pimcore/pimcore", $rootPath . "/pimcore");
            self::copyFolder($vendorPath . "/pimcore/pimcore/website_example", $rootPath . "/website");
            self::copyFolder($vendorPath . "/pimcore/pimcore/plugins_example", $rootPath . "/plugins");

            copy($vendorPath . "/pimcore/pimcore/index.php", $rootPath . "/index.php");
            copy($vendorPath . "/pimcore/pimcore/.gitignore", $rootPath . "/.gitignore");
            copy($vendorPath . "/pimcore/pimcore/.htaccess", $rootPath . "/.htaccess");
        }


        
    }

    /**
     * Copy a given folder to a given location
     *
     * @param string $from
     * @param string $to
     *
     * @return void
     */
    private static function copyFolder($from, $to)
    {
        $objects = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($from),
            \RecursiveIteratorIterator::SELF_FIRST
        );
        foreach ($objects as $name => $object) {
            $startsAt = substr(dirname($name), strlen($from));
            $dir = $to . $startsAt;
            if (!is_dir($dir)) {
                mkdir($dir, 0755);
            }
            if (is_writable($to) && $object->isFile()) {
                copy($name, $dir . DIRECTORY_SEPARATOR . basename($name));
            }
        }
    }
}