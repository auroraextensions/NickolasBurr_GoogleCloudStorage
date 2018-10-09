<?php
/**
 * Bucket.php
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

class NickolasBurr_GoogleCloudStorage_Model_Resource_Core_File_Storage_Bucket
{
    /**
     * Check if file exists in GCS bucket.
     *
     * @param string $filename
     * @param string $directory
     * @return bool
     */
    public function fileExists($filename, $directory)
    {
        /** @var NickolasBurr_GoogleCloudStorage_Helper_Storage $storage */
        $storage = Mage::helper(NickolasBurr_GoogleCloudStorage_Helper_Dict::XML_PATH_HELPER_STORAGE);

        /** @var string $absolutePath */
        /** @var string $relativePath */
        $absolutePath = \rtrim($directory, DS) . '/' . $filename;
        $relativePath = Mage::helper('core/file_storage_database')->getMediaRelativePath($absolutePath);

        return $storage->objectExists($relativePath);
    }

    /**
     * Copy file object to destination.
     *
     * @param string $sourceName
     * @param string $sourcePath
     * @param string $targetName
     * @param string $targetPath
     * @return NickolasBurr_GoogleCloudStorage_Model_Resource_Core_File_Storage_Bucket
     */
    public function copyFile($sourceName, $sourcePath, $targetName, $targetPath)
    {
        /** @var NickolasBurr_GoogleCloudStorage_Helper_Storage $storage */
        $storage = Mage::helper(NickolasBurr_GoogleCloudStorage_Helper_Dict::XML_PATH_HELPER_STORAGE);

        /** @var string $oldAbsolutePath */
        /** @var string $oldRelativePath */
        $oldAbsolutePath = \rtrim($sourcePath, DS) . '/' . $sourceName;
        $oldRelativePath = Mage::helper('core/file_storage_database')->getMediaRelativePath($oldAbsolutePath);

        /** @var string $newAbsolutePath */
        /** @var string $newRelativePath */
        $newAbsolutePath = \rtrim($targetPath, DS) . '/' . $targetName;
        $newRelativePath = Mage::helper('core/file_storage_database')->getMediaRelativePath($newAbsolutePath);

        if ($storage->objectExists($oldRelativePath)) {
            $storage->copyObject($oldRelativePath, $newRelativePath);
        }

        return $this;
    }

    /**
     * Rename existing file object.
     *
     * @param string $sourceName
     * @param string $sourcePath
     * @param string $targetName
     * @param string $targetPath
     * @return NickolasBurr_GoogleCloudStorage_Model_Resource_Core_File_Storage_Bucket
     */
    public function renameFile($sourceName, $sourcePath, $targetName, $targetPath)
    {
        /** @var NickolasBurr_GoogleCloudStorage_Helper_Storage $storage */
        $storage = Mage::helper(NickolasBurr_GoogleCloudStorage_Helper_Dict::XML_PATH_HELPER_STORAGE);

        /** @var string $oldAbsolutePath */
        /** @var string $oldRelativePath */
        $oldAbsolutePath = \rtrim($sourcePath, DS) . '/' . $sourceName;
        $oldRelativePath = Mage::helper('core/file_storage_database')->getMediaRelativePath($oldAbsolutePath);

        /** @var string $newAbsolutePath */
        /** @var string $newRelativePath */
        $newAbsolutePath = \rtrim($targetPath, DS) . '/' . $targetName;
        $newRelativePath = Mage::helper('core/file_storage_database')->getMediaRelativePath($newAbsolutePath);

        if ($storage->objectExists($oldRelativePath)) {
            $storage->renameObject($oldRelativePath, $newRelativePath);
        }

        return $this;
    }

    /**
     * Delete file object from bucket.
     *
     * @param string $filename
     * @param string $directory
     * @return NickolasBurr_GoogleCloudStorage_Model_Resource_Core_File_Storage_Bucket
     */
    public function deleteFile($filename, $directory)
    {
        /** @var NickolasBurr_GoogleCloudStorage_Helper_Storage $storage */
        $storage = Mage::helper(NickolasBurr_GoogleCloudStorage_Helper_Dict::XML_PATH_HELPER_STORAGE);

        /** @var string $absolutePath */
        /** @var string $relativePath */
        $absolutePath = \rtrim($directory, DS) . '/' . $filename;
        $relativePath = Mage::helper('core/file_storage_database')->getMediaRelativePath($absolutePath);

        if ($storage->objectExists($relativePath)) {
            $storage->deleteObject($relativePath);
        }

        return $this;
    }

    /**
     * Delete directory from bucket.
     *
     * @param string $dirname
     */
    public function deleteFolder($dirname = '')
    {
        /* Trim trailing slash from $dirname. */
        $dirname = \rtrim($dirname, '/');

        if (!\strlen($dirname)) {
            return;
        }

        /* Append slash back to $dirname. */
        $dirname .= '/';

        /** @var NickolasBurr_GoogleCloudStorage_Helper_Storage $storage */
        $storage = Mage::helper(NickolasBurr_GoogleCloudStorage_Helper_Dict::XML_PATH_HELPER_STORAGE);

        /* Delete all objects with $dirname prefix. */
        $storage->deleteAllObjects(array('prefix' => $dirname));
    }
}
