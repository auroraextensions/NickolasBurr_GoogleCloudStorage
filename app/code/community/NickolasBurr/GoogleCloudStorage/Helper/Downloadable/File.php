<?php
/**
 * File.php
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

class NickolasBurr_GoogleCloudStorage_Helper_Downloadable_File extends Mage_Downloadable_Helper_File
{
    /**
     * Check against filesystem and bucket, moving files as needed.
     *
     * @param string $tmpPath
     * @param string $basePath
     * @param array $file
     * @return string
     * @see Mage_Downloadable_Helper_File::moveFileFromTmp
     * @see Mage_Downloadable_Helper_File::_moveFileFromTmp
     */
    public function moveFileFromTmp($tmpPath, $basePath, $file)
    {
        /* Current backend storage code. */
        $storageCode = (int) Mage::helper('core/file_storage')->getCurrentStorageCode();

        if ($storageCode !== NickolasBurr_GoogleCloudStorage_Helper_Dict::STORAGE_MEDIA_GCS) {
            return parent::moveFileFromTmp($tmpPath, $basePath, $file);
        }

        /** @var NickolasBurr_GoogleCloudStorage_Helper_Storage $storage */
        $storage = Mage::helper(NickolasBurr_GoogleCloudStorage_Helper_Dict::XML_PATH_HELPER_STORAGE);

        /** @var string $fileName */
        $fileName = '';

        if (isset($file[0])) {
            $fileName = $file[0]['file'];

            /* Whether file type is link or sample. */
            $isLinkFile = ($tmpPath == Mage_Downloadable_Model_Link::getBaseTmpPath());

            if ($file[0]['status'] == 'new') {
                $sourcePath = $this->getFilePath($tmpPath, $fileName);
                $targetPath = $this->getFilePath($basePath, $fileName);
                $targetDir = \dirname($targetPath);

                /** @var Varien_Io_File $resource */
                $resource = new Varien_Io_File();

                try {
                    if (!\file_exists($targetDir) && !$resource->createDestinationDir($targetDir)) {
                        Mage::throwException(Mage::helper('downloadable')->__('Unable to create directory %s', $targetDir));
                    }

                    if (!\is_writable($targetDir)) {
                        Mage::throwException(Mage::helper('downloadable')->__('Unable to write to directory %s', $targetDir));
                    }

                    /** @var resource|bool $handle */
                    if (!($handle = \fopen($sourcePath, 'r'))) {
                        Mage::throwException(Mage::helper('downloadable')->__('Unable to open file %s', $sourcePath));
                    }

                    /** @var string $relativePath */
                    $relativePath = Mage::helper('core/file_storage_database')->getMediaRelativePath($targetPath);

                    if ($isLinkFile) {
                        if ($storage->shouldUploadLinkFiles()) {
                            $storage->uploadToBucket(
                                $handle,
                                array(
                                    'name'          => $relativePath,
                                    'predefinedAcl' => $storage->getLinkFilesAcl(),
                                )
                            );
                        }
                    } else {
                        if ($storage->shouldUploadSampleFiles()) {
                            $storage->uploadToBucket(
                                $handle,
                                array(
                                    'name'          => $relativePath,
                                    'predefinedAcl' => $storage->getSampleFilesAcl(),
                                )
                            );
                        }
                    }

                    if (!\rename($sourcePath, $targetPath)) {
                        Mage::throwException(Mage::helper('downloadable')->__('Unable to rename file %s', $sourcePath));
                    }
                } catch (Mage_Core_Exception $e) {
                    Mage::throwException(Mage::helper('downloadable')->__('Error: %s', $e->getMessage()));
                    Mage::logException($e);
                }
            }
        }

        return $fileName;
    }
}
