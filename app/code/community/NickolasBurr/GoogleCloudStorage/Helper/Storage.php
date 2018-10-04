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
     * Get object from bucket storage.
     *
     * @param string $name
     * @return Google\Cloud\Storage\StorageObject
     */
    public function getObject($name)
    {
        /** @var Google\Cloud\Storage\Bucket $bucket */
        $bucket = $client->bucket($this->getBucketName());

        return $bucket->object($name);
    }

    /**
     * Get all objects in bucket.
     *
     * @param array $options
     * @return ObjectIterator<StorageObject>
     */
    public function getObjects(array $options = array())
    {
        return $this->getBucket()->objects($options);
    }
}
