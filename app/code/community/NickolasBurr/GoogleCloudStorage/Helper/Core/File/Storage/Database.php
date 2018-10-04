<?php
/**
 * Database.php
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

class NickolasBurr_GoogleCloudStorage_Helper_Core_File_Storage_Database extends Mage_Core_Helper_File_Storage_Database
{
    /**
     * @property null $_useBucket
     */
    protected $_useBucket = null;

    /**
     * Check if we're using database storage.
     *
     * @return bool
     * @see Mage_Core_Helper_File_Storage_Database::checkDbUsage
     */
    public function checkDbUsage()
    {
        return !parent::checkDbUsage() ? $this->checkBucketUsage() : $this->_useDb;
    }

    /**
     * Check if we're using GCS bucket for image storage.
     *
     * @return bool
     */
    public function checkBucketUsage()
    {
        if ($this->_useBucket === null) {
            $currentStorage = (int) Mage::app()->getConfig()
                ->getNode(Mage_Core_Model_File_Storage::XML_PATH_STORAGE_MEDIA);
            $this->_useBucket = ($currentStorage === NickolasBurr_GoogleCloudStorage_Helper_Dict::STORAGE_MEDIA_GCS);
        }

        return $this->_useBucket;
    }

    /**
     * Get backend database storage model.
     *
     * @return NickolasBurr_GoogleCloudStorage_Model_Core_File_Storage_Bucket|Mage_Core_Model_File_Storage_Database
     */
    public function getStorageDatabaseModel()
    {
        if ($this->_databaseModel === null && $this->checkBucketUsage()) {
            $this->_databaseModel = Mage::getModel(NickolasBurr_GoogleCloudStorage_Helper_Dict::XML_PATH_MODEL_CORE_FILE_STORAGE_BUCKET);
        }

        return parent::getStorageDatabaseModel();
    }

    /**
     * Save file object to filesystem.
     *
     * @param string $filename
     * @return bool|void
     */
    public function saveFileToFilesystem($filename)
    {
        if ($this->checkDbUsage()) {
            /** @var NickolasBurr_GoogleCloudStorage_Model_Core_File_Storage_Bucket|Mage_Core_Model_File_Storage_Database $backend */
            $backend = $this->getStorageDatabaseModel();

            /** @var Mage_Core_Model_File_Storage_Database $file */
            $file = $backend->loadByFilename($this->_removeAbsPathFromFileName($filename));

            if ($file->getId()) {
                return $this->getStorageFileModel()->saveFile($file, true);
            }
        }

        return false;
    }

    /**
     * Save uploaded file to database with existence tests.
     *
     * @param array $result
     * @return string
     * @see Thai_S3_Helper_Core_File_Storage_Database::saveUploadedFile
     * @see Mage_Core_Helper_File_Storage_Database::saveUploadedFile
     */
    public function saveUploadedFile($result = array())
    {
        return \ltrim(parent::saveUploadedFile($result), '/');
    }
}
