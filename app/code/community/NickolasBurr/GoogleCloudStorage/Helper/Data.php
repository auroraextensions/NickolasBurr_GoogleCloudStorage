<?php
/**
 * Data.php
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

class NickolasBurr_GoogleCloudStorage_Helper_Data extends Mage_Core_Helper_Abstract
{
    /** @constant string XML_PATH_FIELD_GENERAL_ENABLE_MODULE */
    const XML_PATH_FIELD_GENERAL_ENABLE_MODULE = 'magegcs/general/enable_module';

    /** @constant string XML_PATH_FIELD_GENERAL_GCP_PROJECT */
    const XML_PATH_FIELD_GENERAL_GCP_PROJECT = 'magegcs/general/gcp_project';

    /** @constant string XML_PATH_FIELD_GENERAL_KEY_FILE_PATH */
    const XML_PATH_FIELD_GENERAL_KEY_FILE_PATH = 'magegcs/general/key_file_path';

    /** @constant string XML_PATH_FIELD_BUCKET_NAME */
    const XML_PATH_FIELD_BUCKET_NAME = 'magegcs/bucket/name';

    /** @constant string XML_PATH_FIELD_BUCKET_REGION */
    const XML_PATH_FIELD_BUCKET_REGION = 'magegcs/bucket/region';

    /**
     * Check if the module is enabled from admin panel.
     *
     * @return bool
     */
    public function isModuleEnabled($field = self::XML_PATH_FIELD_GENERAL_ENABLE_MODULE)
    {
        return Mage::getStoreConfigFlag($field, Mage::app()->getStore());
    }

    /**
     * Get GCP project name.
     *
     * @param string $field
     * @return string
     */
    public function getGCPProject($field = self::XML_PATH_FIELD_GENERAL_GCP_PROJECT)
    {
        return Mage::getStoreConfig($field, Mage::app()->getStore());
    }

    /**
     * Get JSON key file path.
     *
     * @param string $field
     * @return string
     */
    public function getKeyFilePath($field = self::XML_PATH_FIELD_GENERAL_KEY_FILE_PATH)
    {
        return Mage::getStoreConfig($field, Mage::app()->getStore());
    }

    /**
     * Is the keyfile path absolute?
     *
     * @param string $keyFilePath
     * @return bool
     */
    public function isKeyFilePathAbsolute($keyFilePath = '')
    {
        return (\strlen($keyFilePath) && $keyFilePath[0] === DIRECTORY_SEPARATOR);
    }

    /**
     * Get GCS bucket name.
     *
     * @param string $field
     * @return string
     */
    public function getBucketName($field = self::XML_PATH_FIELD_BUCKET_NAME)
    {
        return Mage::getStoreConfig($field, Mage::app()->getStore());
    }

    /**
     * Get GCS bucket region.
     *
     * @param string $field
     * @return string
     */
    public function getBucketRegion($field = self::XML_PATH_FIELD_BUCKET_REGION)
    {
        return Mage::getStoreConfig($field, Mage::app()->getStore());
    }
}
