<?php
/**
 * Storage.php
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License, which
 * is bundled with this package in the file LICENSE.txt.
 *
 * It is also available on the Internet at the following URL:
 * https://docs.nickolasburr.com/magento/extensions/1.x/magegcs/LICENSE.txt
 *
 * @package        NickolasBurr_GoogleCloudStorage
 * @copyright      Copyright (C) 2018 Nickolas Burr <nickolasburr@gmail.com>
 * @license        MIT License
 */

class NickolasBurr_GoogleCloudStorage_Model_Cms_Wysiwyg_Images_Storage extends Mage_Cms_Model_Wysiwyg_Images_Storage
{
    /**
     * Get collection of directories.
     *
     * @param string $path
     */
    public function getDirsCollection($path)
    {
        /** @var NickolasBurr_GoogleCloudStorage_Helper_Core_File_Storage_Database $helper */
        $helper = Mage::helper(NickolasBurr_GoogleCloudStorage_Helper_Dict::XML_PATH_HELPER_CORE_FILE_STORAGE_DATABASE);

        if ($helper->checkBucketUsage()) {
            $backend = $helper->getStorageDatabaseModel();
            $subdirs = $backend->getSubdirectories($path);

            foreach ($subdirs as $subdir) {
                $absPath = \rtrim($path, '/') . '/' . $subdir['name'];

                if (!\file_exists($absPath)) {
                    \mkdir($absPath, 0777, true);
                }
            }
        }

        return parent::getDirsCollection($path);
    }

    /**
     * Get collection of files per directory.
     */
    public function getFilesCollection($path, $type = null)
    {
        /** @var NickolasBurr_GoogleCloudStorage_Helper_Core_File_Storage_Database $helper */
        $helper = Mage::helper(NickolasBurr_GoogleCloudStorage_Helper_Dict::XML_PATH_HELPER_CORE_FILE_STORAGE_DATABASE);

        if ($helper->checkBucketUsage()) {
            $storage = Mage::getModel('core/file_storage_file');
            $backend = $helper->getStorageDatabaseModel();
            $files = $backend->getDirectoryFiles($path);

            foreach ($files as $file) {
                $storage->saveFile($file);
            }
        }

        return parent::getFilesCollection($path, $type);
    }

    /**
     * Resize image file for various purposes.
     *
     * @param string $source
     * @param bool $keepRatio
     * @return string
     */
    public function resizeFile($source, $keepRatio = true)
    {
        /** @var NickolasBurr_GoogleCloudStorage_Helper_Core_File_Storage_Database $helper */
        $helper = Mage::helper(NickolasBurr_GoogleCloudStorage_Helper_Dict::XML_PATH_HELPER_CORE_FILE_STORAGE_DATABASE);
        $target = parent::resizeFile($source, $keepRatio);

        if ($helper->checkBucketUsage()) {
            $backend = $helper->getStorageDatabaseModel();
            $filePath = \ltrim(\str_replace(Mage::getConfig()->getOptions()->getMediaDir(), '', $target), DS);

            /* Save resized file to GCS bucket. */
            $backend->saveFile($target);
        }

        return $target;
    }

    /**
     * Get thumbnails path.
     *
     * @param bool|string $filePath
     * @return string
     */
    public function getThumbsPath($filePath = false)
    {
        $mediaDir = Mage::getConfig()->getOptions()->getMediaDir();
        $thumbDir = $this->getThumbnailRoot();

        if ($filePath && \strpos($filePath, $mediaDir) === 0) {
            $thumbDir .= DS . \ltrim(\dirname(\substr($filePath, \strlen($mediaDir))), DS);
        }

        return $thumbDir;
    }
}
