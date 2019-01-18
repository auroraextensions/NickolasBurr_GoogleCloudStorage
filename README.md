# Google Cloud Storage for Magento 1.x

Use [Google Cloud Storage](https://cloud.google.com/storage/) as the backend for storing media assets in Magento 1.x.

## Table of Contents

1. [Prerequisites](#prerequisites)
2. [Documentation](#documentation)
3. [Features](#features)
4. [FAQs](#faqs)
5. [Troubleshooting](#troubleshooting)
6. [Credits](#credits)

## Prerequisites

Before installing this extension, please make sure you've installed [Google Cloud PHP Autoloader](https://github.com/auroraextensions/googlecloudphpautoloader).
This extension depends on the autoloader to load the necessary [Google Cloud PHP](https://github.com/googleapis/google-cloud-php) libraries.

**IMPORTANT**: Please back up your media files prior to installation. Files lost to accidental deletion cannot be recovered.

## Documentation

See [here](https://docs.auroraextensions.com/magento/extensions/1.x/magegcs/latest/) for documentation.

## Features

There are several advantages to cloud-based storage solutions like Google Cloud Storage, including:

+ Integration with CDN
+ External backups
+ Reduced storage costs

## FAQs

> Do I have to use a service account key for authentication? Or can I use a different method?

Currently, only service account keys are supported for authentication.

> Does the extension provide a CLI?

No, it does not. However, you can perform bucket operations via `gsutil`.

> Are log files synchronized to the bucket?

No, only media files are synchronized.

> Can I synchronize downloadable product files to the bucket?

Yes, it can synchronize both sample and link files.

## Troubleshooting

> Fatal error: Uncaught Error: Class 'Google\Cloud\Storage\StorageClient' not found

You need to install and configure [Google Cloud PHP Autoloader](https://github.com/auroraextensions/googlecloudphpautoloader).

> Given keyfile at path /path/to/magento was invalid

You need to create and install a service account key to authenticate with Google Cloud. Verify you've completed the following:

1. Generate a Google Cloud service account key with _Storage Admin_ privileges
2. Install service account key to local or mounted filesystem with read-only permissions for Magento user
3. Under `System > Configuration > Nickolas Burr Extensions > Google Cloud Storage`, make sure:
    1. The extension is enabled
    2. The Google Cloud project name where the bucket exists is set
    3. The path to the service account key (e.g. `/etc/gcs.json`) is set
    4. The Google Cloud Storage bucket name (e.g. `mybucket`) is set
    5. [OPTIONAL] If you use the same bucket for multiple projects, you can specify a subdirectory to synchronize to inside the bucket. Otherwise, it will synchronize to `/`.

For more information on Google Cloud service account keys, please see [Creating and Managing Service Account Keys](https://cloud.google.com/iam/docs/creating-managing-service-account-keys).

## Credits

Several key parts of this extension are derived from the [magento-s3](https://github.com/thaiphan/magento-s3) extension.
