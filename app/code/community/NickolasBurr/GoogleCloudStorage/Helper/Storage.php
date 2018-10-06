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

class NickolasBurr_GoogleCloudStorage_Helper_Storage extends NickolasBurr_GoogleCloudStorage_Helper_Data
{
    /**
     * @property array $acls
     * @static
     */
    protected static $_acls = array(
        array(
            'label' => 'Private',
            'value' => 'private',
        ),
        array(
            'label' => 'Bucket Owner Read',
            'value' => 'bucketOwnerRead',
        ),
        array(
            'label' => 'Bucket Owner Full Control',
            'value' => 'bucketOwnerFullControl',
        ),
        array(
            'label' => 'Project Private',
            'value' => 'projectPrivate',
        ),
        array(
            'label' => 'Authenticated Read',
            'value' => 'authenticatedRead',
        ),
        array(
            'label' => 'Public Read',
            'value' => 'publicRead',
        ),
    );

    /**
     * @property array $_regions
     * @static
     */
    protected static $_regions = array(
        array(
            'label' => 'Montreal (CA)',
            'value' => 'northamerica-northeast1',
        ),
        array(
            'label' => 'Iowa (US)',
            'value' => 'us-central1',
        ),
        array(
            'label' => 'South Carolina (US)',
            'value' => 'us-east1',
        ),
        array(
            'label' => 'Northern Virginia (US)',
            'value' => 'us-east4',
        ),
        array(
            'label' => 'Oregon (US)',
            'value' => 'us-west1',
        ),
        array(
            'label' => 'Los Angeles, California (US)',
            'value' => 'us-west2',
        ),
        array(
            'label' => 'Sao Paulo (BR)',
            'value' => 'southamerica-east1',
        ),
        array(
            'label' => 'Finland',
            'value' => 'europe-north1',
        ),
        array(
            'label' => 'Belgium',
            'value' => 'europe-west1',
        ),
        array(
            'label' => 'London (UK)',
            'value' => 'europe-west2',
        ),
        array(
            'label' => 'Frankfurt (DE)',
            'value' => 'europe-west3',
        ),
        array(
            'label' => 'Netherlands',
            'value' => 'europe-west4',
        ),
        array(
            'label' => 'Taiwan',
            'value' => 'asia-east1',
        ),
        array(
            'label' => 'Tokyo (JP)',
            'value' => 'asia-northeast1',
        ),
        array(
            'label' => 'Mumbai (IN)',
            'value' => 'asia-south1',
        ),
        array(
            'label' => 'Singapore',
            'value' => 'asia-southeast1',
        ),
        array(
            'label' => 'Sydney (AU)',
            'value' => 'australia-southeast1',
        ),
    );

    /**
     * @property null $_client
     */
    protected $_client = null;

    /**
     * Set GCS storage client instance.
     *
     * @constructor
     */
    public function __construct()
    {
        /** @var string $keyFilePath */
        $keyFilePath = $this->getKeyFilePath();

        /* Get absolute path to key file from configuration settings. */
        $keyFilePath = (!$this->isKeyFilePathAbsolute($keyFilePath) ? (Mage::getBaseDir() . DIRECTORY_SEPARATOR . $this->getKeyFilePath()) : $keyFilePath);

        /* Initialize StorageClient with configuration settings. */
        $config = array(
            'projectId'   => $this->getGCPProject(),
            'keyFilePath' => $keyFilePath,
        );

        $this->_client = new Google\Cloud\Storage\StorageClient($config);
    }

    /**
     * Get array of predefined ACLs.
     *
     * @return array
     */
    public function getAcls()
    {
        return self::$_acls;
    }

    /**
     * Get array of GCS regions.
     *
     * @return array
     */
    public function getRegions()
    {
        return self::$_regions;
    }

    /**
     * Get GCS storage client.
     *
     * @return Google\Cloud\Storage\StorageClient
     */
    public function getClient()
    {
        return $this->_client;
    }

    /**
     * Get GCS bucket.
     *
     * @return Google\Cloud\Storage\Bucket
     */
    public function getBucket()
    {
        return $this->getClient()->bucket($this->getBucketName());
    }

    /**
     * Upload file to GCS bucket.
     *
     * @param resource $handle
     * @param array $options
     * @param bool $includeBucketPrefix
     * @return Google\Cloud\Storage\StorageObject|null
     */
    public function uploadToBucket($handle, array $options = array(), $includeBucketPrefix = true)
    {
        /** @var NickolasBurr_GoogleCloudStorage_Helper_Core_File_Storage_Database $helper */
        $helper = Mage::helper(NickolasBurr_GoogleCloudStorage_Helper_Dict::XML_PATH_HELPER_CORE_FILE_STORAGE_DATABASE);

        if ($includeBucketPrefix) {
            /** @var string $bucketPrefix */
            $bucketPrefix = $this->getBucketPrefixAsUnixPath();

            if (isset($options['name'])) {
                $options['name'] = $bucketPrefix . '/' . \ltrim($options['name'], DS);
            } else {
                /** @var array $metadata */
                $metadata = \stream_get_meta_data($handle);

                $mediaBaseDir = \rtrim($helper->getMediaBaseDir(), DS);
                $absolutePath = \realpath($metadata['uri']);
                $relativePath = \ltrim(\str_replace($mediaBaseDir, '', $absolutePath), DS);

                /* Set bucket-prefixed, absolute pathname on $options['name']. */
                $options['name'] = $bucketPrefix . '/' . \ltrim($mediaBaseDir, DS) . '/' . $relativePath;
            }
        }

        /** @var Google\Cloud\Storage\Bucket $bucket */
        $bucket = $this->getBucket($this->getBucketName());

        return $bucket->upload($handle, $options);
    }

    /**
     * Get object from bucket storage.
     *
     * @param string $filePath
     * @param bool $includeBucketPrefix
     * @return Google\Cloud\Storage\StorageObject
     */
    public function getObject($filePath, $includeBucketPrefix = true)
    {
        /** @var Google\Cloud\Storage\Bucket $bucket */
        $bucket = $this->getBucket($this->getBucketName());

        if ($includeBucketPrefix) {
            $filePath = $this->getBucketPrefixAsUnixPath() . '/' . \ltrim($filePath, DS);
        }

        return $bucket->object($filePath);
    }

    /**
     * Get all objects in bucket.
     *
     * @param array $options
     * @param bool $includeBucketPrefix
     * @return ObjectIterator<StorageObject>
     */
    public function getObjects(array $options = array(), $includeBucketPrefix = true)
    {
        /** @var Google\Cloud\Storage\Bucket $bucket */
        $bucket = $this->getBucket($this->getBucketName());

        if ($includeBucketPrefix) {
            if (isset($options['prefix'])) {
                $options['prefix'] = $this->getBucketPrefixAsUnixPath() . '/' . \ltrim($options['prefix'], DS);
            } else {
                $options['prefix'] = $this->getBucketPrefixAsUnixPath();
            }
        }

        return $bucket->objects($options);
    }

    /**
     * Check if object exists in GCS bucket.
     *
     * @param string $filePath
     * @return bool
     */
    public function objectExists($filePath)
    {
        /** @var Google\Cloud\Storage\StorageObject|null $object */
        $object = $this->getObject($filePath);

        return ($object && $object->exists());
    }

    /**
     * Copy file object from $sourcePath to $targetPath.
     *
     * @param string $sourcePath
     * @param string $targetPath
     * @return Google\Cloud\Storage\StorageObject|bool
     */
    public function copyObject($sourcePath, $targetPath)
    {
        if (!$this->objectExists($sourcePath)) {
            return false;
        }

        if ($this->hasBucketPrefix()) {
            $targetPath = $this->getBucketPrefixAsUnixPath() . '/' . \ltrim($targetPath, DS);
        }

        /** @var Google\Cloud\Storage\StorageObject $object */
        $object = $this->getObject($sourcePath);

        if ($object->exists()) {
            return $object->copy($targetPath);
        }

        return false;
    }

    /**
     * Rename file object from $sourcePath to $targetPath.
     *
     * @param string $sourcePath
     * @param string $targetPath
     * @return Google\Cloud\Storage\StorageObject|bool
     */
    public function renameObject($sourcePath, $targetPath)
    {
        if (!$this->objectExists($sourcePath)) {
            return false;
        }

        if ($this->hasBucketPrefix()) {
            $targetPath = $this->getBucketPrefixAsUnixPath() . '/' . \ltrim($targetPath, DS);
        }

        /** @var Google\Cloud\Storage\StorageObject $object */
        $object = $this->getObject($sourcePath);

        if ($object->exists()) {
            return $object->rename($targetPath);
        }

        return false;
    }

    /**
     * Delete file object from GCS bucket.
     *
     * @param string $filePath
     * @return bool
     */
    public function deleteObject($filePath)
    {
        if (!$this->objectExists($filePath)) {
            return false;
        }

        /** @var Google\Cloud\Storage\StorageObject $object */
        $object = $this->getObject($filePath);

        if ($object->exists()) {
            $object->delete();
        }

        return !$this->objectExists($filePath);
    }
}
