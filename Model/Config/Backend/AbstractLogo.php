<?php
/**
 * ManiyaTech
 *
 * @author        Milan Maniya
 * @package       ManiyaTech_AdminLogo
 */

namespace ManiyaTech\AdminLogo\Model\Config\Backend;

use Magento\Config\Model\Config\Backend\Image;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem\Driver\File as FileDriver;
use ManiyaTech\AdminLogo\File\Uploader as FileUploader;
use Magento\Framework\App\RequestInterface;

class AbstractLogo extends Image
{
    /**
     * Max file size in MB.
     *
     * @var int
     */
    protected $_maxFileSize = 2;

    /**
     * Allowed extensions.
     */
    protected function _getAllowedExtensions(): array
    {
        return ['jpg', 'jpeg', 'gif', 'png', 'svg'];
    }

    /**
     * Validation callback for checking max file size
     *
     * @param  string $filePath Path to temporary uploaded file
     * @return void
     * @throws LocalizedException
     */
    public function validateMaxSize($filePath)
    {
        $maxFileSizeInBytes = $this->_maxFileSize * 1024 * 1024;

        $directory = $this->_filesystem->getDirectoryRead(DirectoryList::SYS_TMP);
        $relativePath = $directory->getRelativePath($filePath);
        $fileStat = $directory->stat($relativePath);

        if ($this->_maxFileSize > 0 &&
            isset($fileStat['size']) &&
            $fileStat['size'] > $maxFileSizeInBytes
        ) {
            throw new LocalizedException(
                __('The file you\'re uploading exceeds the server size limit of %1 MB.', $this->_maxFileSize)
            );
        }
    }

    /**
     * Validation callback for checking max file size
     *
     * @param  string $filePath Path to temporary uploaded file
     * @return void
     * @throws LocalizedException
     */
    public function beforeSave()
    {
        $field = $this->getField();

        $fileDriver = \Magento\Framework\App\ObjectManager::getInstance()->create(FileDriver::class);
        $request = \Magento\Framework\App\ObjectManager::getInstance()->create(RequestInterface::class);
        $upload = $request->getFiles('groups');
        
        // Access real uploaded file
        if (isset($upload['general']['fields'][$field]['value']['tmp_name']) &&
            $fileDriver->isExists($upload['general']['fields'][$field]['value']['tmp_name'])
        ) {
            try {
                $uploadDir = $this->_getUploadDir();

                $fileData = [
                    'name' => $upload['general']['fields'][$field]['value']['name'],
                    'type' => $upload['general']['fields'][$field]['value']['type'],
                    'tmp_name' => $upload['general']['fields'][$field]['value']['tmp_name'],
                    'error' => $upload['general']['fields'][$field]['value']['error'],
                    'size' => $upload['general']['fields'][$field]['value']['size'],
                ];

                $uploader = \Magento\Framework\App\ObjectManager::getInstance()->create(
                    \ManiyaTech\AdminLogo\File\Uploader::class,
                    ['fileId' => $fileData]
                );

                $uploader->setAllowedExtensions($this->_getAllowedExtensions());
                $uploader->setAllowRenameFiles(true);
                $uploader->addValidateCallback('size', $this, 'validateMaxSize');
                $uploader->addValidateCallback('custom_mime_check', $this, 'validateSvgMimeType');

                $result = $uploader->save($uploadDir);

                if (!$result) {
                    throw new LocalizedException(
                        __('File cannot be saved to the destination folder.')
                    );
                }

                $filename = $result['file'];
                if ($this->_addWhetherScopeInfo()) {
                    $filename = $this->_prependScopeInfo($filename);
                }

                // Only set value if it's different
                if ($filename !== $this->getOldValue()) {
                    $this->setValue($filename);
                }

            } catch (\Exception $e) {
                throw new LocalizedException(
                    __('File upload error: %1', $e->getMessage())
                );
            }
        } else {
            $value = $this->getValue();
            if (is_array($value) && !empty($value['delete'])) {
                $oldFile = $this->getOldValue();
                if ($oldFile) {
                    try {
                        $filePath = $this->_getUploadDir() . DIRECTORY_SEPARATOR . ltrim($oldFile, '/');
                        $finalFilePath = preg_replace('#/default/default/#', '/default/', $filePath, 1);
                        if ($fileDriver->isExists($finalFilePath)) {
                            $fileDriver->deleteFile($finalFilePath);
                        }
                    } catch (\Exception $e) {
                        throw new LocalizedException(
                            __('File delete error: %1', $e->getMessage())
                        );
                    }
                }
                $this->setValue('');
            } else {
                $this->setValue($this->getOldValue());
            }
        }

        return $this;
    }

    /**
     * Custom MIME type validator to allow SVGs.
     *
     * @param string $filePath
     * @return void
     * @throws LocalizedException
     */
    public function validateSvgMimeType($filePath)
    {
        $allowedMimeTypes = [
            'image/jpeg',
            'image/png',
            'image/gif',
            'image/svg+xml',
        ];

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $filePath);
        finfo_close($finfo);

        if (!in_array($mimeType, $allowedMimeTypes, true)) {
            throw new LocalizedException(
                __('Disallowed MIME type: %1', $mimeType)
            );
        }
    }
}
