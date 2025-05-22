<?php
/**
 * ManiyaTech
 *
 * @author        Milan Maniya
 * @package       ManiyaTech_AdminLogo
 */

namespace ManiyaTech\AdminLogo\Setup\Patch\Data;

use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Filesystem\Driver\File as FileDriver;
use Magento\Framework\Filesystem\Io\File;
use Magento\Framework\Module\Dir\Reader as ModuleReader;

/**
 * Patch class to copy default admin images to the media folder.
 */
class CopyDefaultImages implements DataPatchInterface
{
    /**
     * @var File
     */
    private $file;

    /**
     * @var FileDriver
     */
    private $driverFile;

    /**
     * @var ModuleReader
     */
    private $moduleReader;

    /**
     * Constructor
     *
     * @param File $file
     * @param FileDriver $driverFile
     * @param ModuleReader $moduleReader
     */
    public function __construct(
        File $file,
        FileDriver $driverFile,
        ModuleReader $moduleReader
    ) {
        $this->file = $file;
        $this->driverFile = $driverFile;
        $this->moduleReader = $moduleReader;
    }

    /**
     * Apply patch to copy default images from module to pub/media folder.
     *
     * @return void
     */
    public function apply()
    {
        $modulePath = $this->moduleReader->getModuleDir('', 'ManiyaTech_AdminLogo');
        $sourceDir = $modulePath . '/view/adminhtml/web/images/';
        $destDir = BP . '/pub/media/ManiyaTech/Adminlogo/';

        $filesToCopy = ['admin-logo.jpg', 'menu-logo.ico'];

        $this->file->checkAndCreateFolder($destDir);

        foreach ($filesToCopy as $fileName) {
            $sourceFile = $sourceDir . $fileName;
            $destFile = $destDir . $fileName;

            if (!$this->driverFile->isExists($destFile)) {
                $this->driverFile->copy($sourceFile, $destFile);
            }
        }
    }

    /**
     * Returns patch dependencies.
     *
     * @return array
     */
    public static function getDependencies(): array
    {
        return [];
    }

    /**
     * Returns patch aliases.
     *
     * @return array
     */
    public function getAliases(): array
    {
        return [];
    }
}
