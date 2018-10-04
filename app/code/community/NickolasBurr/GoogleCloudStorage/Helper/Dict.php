<?php
/**
 * Dict.php
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

class NickolasBurr_GoogleCloudStorage_Helper_Dict extends NickolasBurr_GoogleCloudStorage_Helper_Data
{
    /** @constant int STORAGE_MEDIA_GCS */
    const STORAGE_MEDIA_GCS = 4;

    /** @constant string XML_PATH_HELPER_STORAGE */
    const XML_PATH_HELPER_STORAGE = 'magegcs/storage';

    /** @constant string XML_PATH_HELPER_CORE_FILE_STORAGE_DATABASE */
    const XML_PATH_HELPER_CORE_FILE_STORAGE_DATABASE = 'magegcs/core_file_storage_database';

    /** @constant string XML_PATH_MODEL_CORE_FILE_STORAGE_BUCKET */
    const XML_PATH_MODEL_CORE_FILE_STORAGE_BUCKET = 'magegcs/core_file_storage_bucket';
}
