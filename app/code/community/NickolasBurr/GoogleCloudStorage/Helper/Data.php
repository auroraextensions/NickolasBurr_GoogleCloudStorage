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

    /** @constant string XML_PATH_FIELD_BUCKET_PREFIX */
    const XML_PATH_FIELD_BUCKET_PREFIX = 'magegcs/bucket/prefix';

    /** @constant string XML_PATH_FIELD_BUCKET_ACL */
    const XML_PATH_FIELD_BUCKET_ACL = 'magegcs/bucket/acl';

    /** @constant string XML_PATH_FIELD_DOWNLOADABLE_UPLOAD_LINK_FILES */
    const XML_PATH_FIELD_DOWNLOADABLE_UPLOAD_LINK_FILES = 'magegcs/downloadable/upload_link_files';

    /** @constant string XML_PATH_FIELD_DOWNLOADABLE_LINK_FILES_ACL */
    const XML_PATH_FIELD_DOWNLOADABLE_LINK_FILES_ACL = 'magegcs/downloadable/link_files_acl';

    /** @constant string XML_PATH_FIELD_DOWNLOADABLE_UPLOAD_SAMPLE_FILES */
    const XML_PATH_FIELD_DOWNLOADABLE_UPLOAD_SAMPLE_FILES = 'magegcs/downloadable/upload_sample_files';

    /** @constant string XML_PATH_FIELD_DOWNLOADABLE_SAMPLE_FILES_ACL */
    const XML_PATH_FIELD_DOWNLOADABLE_SAMPLE_FILES_ACL = 'magegcs/downloadable/sample_files_acl';

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
     * Get GCS bucket prefix.
     *
     * @param string $field
     * @return string
     */
    public function getBucketPrefix($field = self::XML_PATH_FIELD_BUCKET_PREFIX)
    {
        return Mage::getStoreConfig($field, Mage::app()->getStore());
    }

    /**
     * Check if GCS bucket prefix is set.
     *
     * @return bool
     */
    public function hasBucketPrefix()
    {
        /** @var string $prefix */
        $prefix = $this->getBucketPrefix();

        return !empty($prefix);
    }

    /**
     * Get GCS bucket prefix as well-formed, Unix-like path.
     *
     * @param bool $trimEnd
     * @return string
     */
    public function getBucketPrefixAsUnixPath($trimEnd = true)
    {
        /** @var string $prefix */
        $prefix = \preg_replace('#//+#', '/', $this->getBucketPrefix());

        /* Remove leading slash, if needed. */
        if (\strlen($prefix) && $prefix[0] === '/') {
            $prefix = \ltrim($prefix, '/');
        }

        /* Remove trailing slash, if needed. */
        if ($trimEnd) {
            $prefix = \rtrim($prefix, '/');
        }

        return $prefix;
    }

    /**
     * Get GCS bucket ACL policy.
     *
     * @param string $field
     * @return string
     */
    public function getBucketAcl($field = self::XML_PATH_FIELD_BUCKET_ACL)
    {
        return Mage::getStoreConfig($field, Mage::app()->getStore());
    }

    /**
     * Should we upload downloadable product link files to the bucket?
     *
     * @param string $field
     * @return bool
     */
    public function shouldUploadLinkFiles($field = self::XML_PATH_FIELD_DOWNLOADABLE_UPLOAD_LINK_FILES)
    {
        return Mage::getStoreConfigFlag($field, Mage::app()->getStore());
    }

    /**
     * Get predefined ACL for downloadable product link files.
     *
     * @param string $field
     * @return string
     */
    public function getLinkFilesAcl($field = self::XML_PATH_FIELD_DOWNLOADABLE_LINK_FILES_ACL)
    {
        return Mage::getStoreConfig($field, Mage::app()->getStore());
    }

    /**
     * Should we upload downloadable product sample files to the bucket?
     *
     * @param string $field
     * @return bool
     */
    public function shouldUploadSampleFiles($field = self::XML_PATH_FIELD_DOWNLOADABLE_UPLOAD_SAMPLE_FILES)
    {
        return Mage::getStoreConfigFlag($field, Mage::app()->getStore());
    }

    /**
     * Get predefined ACL for downloadable product sample files.
     *
     * @param string $field
     * @return string
     */
    public function getSampleFilesAcl($field = self::XML_PATH_FIELD_DOWNLOADABLE_SAMPLE_FILES_ACL)
    {
        return Mage::getStoreConfig($field, Mage::app()->getStore());
    }
}
