<?php
/**
 * ManiyaTech
 *
 * @author        Milan Maniya
 * @package       ManiyaTech_AdminLogo
 */

namespace ManiyaTech\AdminLogo\File;

use Magento\Framework\File\Uploader as CoreUploader;
use Magento\Framework\Exception\LocalizedException;

class Uploader extends CoreUploader
{
    /**
     * Validates the MIME type of the uploaded file.
     *
     * This method checks if the MIME type of the given file path
     * is among the list of allowed MIME types. If not, it throws
     * a LocalizedException.
     *
     * @param string $filePath Absolute path to the file being validated.
     * @return bool Returns true if the MIME type is allowed.
     * @throws \Magento\Framework\Exception\LocalizedException If the MIME type is disallowed.
     */
    protected function _validateMimeType($filePath)
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

        return true;
    }
}
