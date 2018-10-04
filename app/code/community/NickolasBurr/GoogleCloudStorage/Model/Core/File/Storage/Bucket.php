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

class NickolasBurr_GoogleCloudStorage_Model_Core_File_Storage_Bucket extends Mage_Core_Model_File_Storage_Abstract
{
    /** @property string $_eventPrefix */
    protected $_eventPrefix = 'magegcs_core_file_storage_bucket';

    /** @property array $_errors */
    protected $_errors = array();

    /** @property null $_objects */
    protected $_objects = null;

    /**
     * Magento constructor.
     */
    protected function _construct()
    {
        $this->_init(NickolasBurr_GoogleCloudStorage_Helper_Dict::XML_PATH_MODEL_CORE_FILE_STORAGE_BUCKET);
    }

    /**
     * Get initialized class instance.
     *
     * @return NickolasBurr_GoogleCloudStorage_Model_Core_File_Storage_Bucket
     */
    public function init()
    {
        return $this;
    }

    /**
     * Get translated storage name.
     *
     * @return string
     */
    public function getStorageName()
    {
        return Mage::helper('magegcs')->__('Google Cloud Storage');
    }

    /**
     * Load file object by pathname.
     *
     * @param string $filePath
     * @return NickolasBurr_GoogleCloudStorage_Model_Core_File_Storage_Bucket
     */
    public function loadByFilename($filePath)
    {
        /** @var NickolasBurr_GoogleCloudStorage_Helper_Storage $helper */
        $helper = Mage::helper(NickolasBurr_GoogleCloudStorage_Helper_Dict::XML_PATH_HELPER_STORAGE);

        /** @var Google\Cloud\Storage\StorageObject $object */
        $object = $helper->getObject($filePath);

        if ($object->exists()) {
            $this->setData('id', $filePath);
            $this->setData('filename', $filePath);
            $this->setData('content', $object->downloadAsString());
        } else {
            $this->unsetData();
        }

        return $this;
    }

    /**
     * If errors were encountered during operation.
     *
     * @return bool
     */
    public function hasErrors()
    {
        return !empty($this->_errors);
    }

    /**
     * Clear storage bucket of objects.
     *
     * @return NickolasBurr_GoogleCloudStorage_Model_Core_File_Storage_Bucket
     */
    public function clear()
    {
        /** @var NickolasBurr_GoogleCloudStorage_Helper_Storage $helper */
        $helper = Mage::helper(NickolasBurr_GoogleCloudStorage_Helper_Dict::XML_PATH_HELPER_STORAGE);

        /** @var ObjectIterator<StorageObject> $objects */
        $objects = $helper->getObjects();

        foreach ($objects as $object) {
            $object->delete();
        }

        return $this;
    }

    /**
     * Export directories list from storage.
     *
     * @param int $offset
     * @param int $count
     * @return array|bool
     * @see Mage_Core_Model_File_Storage_File::exportDirectories
     */
    public function exportDirectories($offset = 0, $count = 100)
    {
        return false;
    }

    /**
     * Import directories to storage.
     *
     * @param array $dirs
     * @return NickolasBurr_GoogleCloudStorage_Model_Core_File_Storage_Bucket
     */
    public function importDirectories($dirs)
    {
        return $this;
    }

    /**
     * Export files list in defined range.
     *
     * @param int $offset
     * @param int $count
     * @return array|bool
     */
    public function exportFiles($offset = 0, $count = 100)
    {
        /** @var NickolasBurr_GoogleCloudStorage_Helper_Storage $helper */
        $helper = Mage::helper(NickolasBurr_GoogleCloudStorage_Helper_Dict::XML_PATH_HELPER_STORAGE);

        /** @var array $files */
        $files = array();

        if ($this->_objects === null) {
            $this->_objects = $helper->getObjects(array('maxResults' => $count));
        } else {
            $this->_objects = $helper->getObjects(
                array(
                    'maxResults'    => $count,
                    'nextPageToken' => $this->_objects->nextPageToken,
                )
            );
        }

        if (!$this->_objects) {
            return false;
        }

        foreach ($this->_objects as $object) {
            $name = $object->name();

            if (\strlen($name) && $name[0] !== '/') {
                $files[] = array(
                    'filename' => $name,
                    'content'  => $object->downloadAsString(),
                );
            }
        }

        return $files;
    }

    /**
     * Upload array of files to GCS bucket.
     *
     * @param array $files
     * @return NickolasBurr_GoogleCloudStorage_Model_Core_File_Storage_Bucket
     */
    public function importFiles(array $files = array())
    {
        /** @var NickolasBurr_GoogleCloudStorage_Helper_Storage $helper */
        $helper = Mage::helper(NickolasBurr_GoogleCloudStorage_Helper_Dict::XML_PATH_HELPER_STORAGE);

        /** @var Google\Cloud\Storage\Bucket $bucket */
        $bucket = $helper->getBucket();

        foreach ($files as $file) {
            try {
                /** @var string $filePath */
                $filePath = $this->_getFilePath($file['filename'], $file['directory']);

                /** @var string $content */
                $content = $file['content'];

                /* Upload file object to GCS bucket. */
                $bucket->upload(
                    $content,
                    array(
                        'name'          => $filePath,
                        'predefinedAcl' => 'publicRead',
                    )
                );
            } catch (\Exception $e) {
                $this->_errors[] = $e->getMessage();
                Mage::logException($e);
            }
        }

        return $this;
    }

    /**
     * Get file path (with prefix, if applicable).
     *
     * @param string $filePath
     * @param string|null $prefix
     * @return string
     */
    protected function _getFilePath($filePath, $prefix = null)
    {
        if ($prefix !== null) {
            $filePath = $prefix . '/' . $filePath;
        }

        return $filePath;
    }

    /**
     * Upload file to GCS bucket.
     *
     * @param string $filename
     * @return NickolasBurr_GoogleCloudStorage_Model_Core_File_Storage_Bucket
     */
    public function saveFile($filename)
    {
        /** @var NickolasBurr_GoogleCloudStorage_Helper_Storage $helper */
        $helper = Mage::helper(NickolasBurr_GoogleCloudStorage_Helper_Dict::XML_PATH_HELPER_STORAGE);

        /** @var Google\Cloud\Storage\Bucket $bucket */
        $bucket = $helper->getBucket();

        /** @var string $filePath */
        $filePath = $this->_getFilePath($filename, $this->getMediaBaseDirectory());

        $bucket->upload(
            \fopen($filePath, 'r'),
            array('predefinedAcl' => 'publicRead')
        );

        return $this;
    }

    /**
     * Check if file exists in GCS bucket.
     *
     * @param string $filePath
     * @return bool
     */
    public function fileExists($filePath)
    {
        /** @var NickolasBurr_GoogleCloudStorage_Helper_Storage $helper */
        $helper = Mage::helper(NickolasBurr_GoogleCloudStorage_Helper_Dict::XML_PATH_HELPER_STORAGE);

        /** @var Google\Cloud\Storage\StorageObject $object */
        $object = $helper->getObject($filePath);

        return $object->exists();
    }

    /**
     * Copy existing file object to new destination.
     *
     * @param string $sourcePath
     * @param string $targetPath
     * @return NickolasBurr_GoogleCloudStorage_Model_Core_File_Storage_Bucket
     */
    public function copyFile($sourcePath, $targetPath)
    {
        /** @var NickolasBurr_GoogleCloudStorage_Helper_Storage $helper */
        $helper = Mage::helper(NickolasBurr_GoogleCloudStorage_Helper_Dict::XML_PATH_HELPER_STORAGE);

        /** @var Google\Cloud\Storage\StorageObject $object */
        $object = $helper->getObject($sourcePath);

        if ($object->exists()) {
            $object->copy($targetPath);
        }

        return $this;
    }

    /**
     * Rename existing file object.
     *
     * @param string $sourcePath
     * @param string $targetPath
     * @return NickolasBurr_GoogleCloudStorage_Model_Core_File_Storage_Bucket
     */
    public function renameFile($sourcePath, $targetPath)
    {
        /** @var NickolasBurr_GoogleCloudStorage_Helper_Storage $helper */
        $helper = Mage::helper(NickolasBurr_GoogleCloudStorage_Helper_Dict::XML_PATH_HELPER_STORAGE);

        /** @var Google\Cloud\Storage\StorageObject $object */
        $object = $helper->getObject($sourcePath);

        if ($object->exists()) {
            $object->rename($targetPath);
        }

        return $this;
    }

    /**
     * Delete file object from GCS bucket.
     *
     * @param string $filePath
     * @return NickolasBurr_GoogleCloudStorage_Model_Core_File_Storage_Bucket
     */
    public function deleteFile($filePath)
    {
        /** @var NickolasBurr_GoogleCloudStorage_Helper_Storage $helper */
        $helper = Mage::helper(NickolasBurr_GoogleCloudStorage_Helper_Dict::XML_PATH_HELPER_STORAGE);

        /** @var Google\Cloud\Storage\StorageObject $object */
        $object = $helper->getObject($filePath);

        if ($object->exists()) {
            $object->delete();
        }

        return $this;
    }

    /**
     * Get subdirectories from $path.
     *
     * @param string $path
     * @return array
     */
    public function getSubdirectories($path)
    {
        $subdirs = array();

        /** @var string $prefix */
        $prefix = \rtrim(Mage::helper('core/file_storage_database')->getMediaRelativePath($path), '/') . '/';

        /** @var NickolasBurr_GoogleCloudStorage_Helper_Storage $helper */
        $helper = Mage::helper(NickolasBurr_GoogleCloudStorage_Helper_Dict::XML_PATH_HELPER_STORAGE);

        /** @var ObjectIterator<StorageObject> $objectsPrefixes */
        $objectsPrefixes = $helper->getObjects(
            array(
                'delimiter' => '/',
                'prefix'    => $prefix,
            )
        );

        if (isset($objectsPrefixes)) {
            foreach ($objectsPrefixes as $subdir) {
                $subdirs[] = array(
                    'name' => \substr($subdir, \strlen($prefix))
                );
            }
        }

        return $subdirs;
    }

    /**
     * Get files from $directory.
     *
     * @param string $directory
     * @return array
     */
    public function getDirectoryFiles($directory)
    {
        $files = array();

        /** @var NickolasBurr_GoogleCloudStorage_Helper_Storage $helper */
        $helper = Mage::helper(NickolasBurr_GoogleCloudStorage_Helper_Dict::XML_PATH_HELPER_STORAGE);

        /** @var string $prefix */
        $prefix = \rtrim(Mage::helper('core/file_storage_database')->getMediaRelativePath($path), '/') . '/';

        /** @var ObjectIterator<StorageObject> $objectsPrefixes */
        $objectsPrefixes = $helper->getObjects(
            array(
                'delimiter' => '/',
                'prefix'    => $prefix,
            )
        );

        if (isset($objectsPrefixes['objects'])) {
            foreach ($objectsPrefixes['objects'] as $object) {
                /** @var string $name */
                $name = $object->name();

                if ($name !== $prefix) {
                    $files[] = array(
                        'filename' => $name,
                        'content'  => $object->downloadAsString(),
                    );
                }
            }
        }

        return $files;
    }
}
