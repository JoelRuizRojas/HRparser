<?php

/**
 * AwsS3Client.php
 *
 * Author: Joel Ruiz
 * Copyright (c) HR parser. All rights reserved.
 */

namespace HRparser\AwsS3Client;                            // Namespace declaration

use Aws\S3\S3Client;
use Aws\Credentials\Credentials;

class AwsS3Client
{
    /**
     * @var $s3Client AWS S3 client
     */
    private $s3Client;

    /**
     * Class constructor
     *
     * @param $clientUser AWS S3 Client user name
     * @param $clientPwd AWS S3 Client password
     * @param $server Target server ip address
     * @param $port Port
     * @return none
     */
    public function __construct(string $clientUser, string $clientPwd, string $server, string $port)
    {
	    // Populate credential
	$endpoint = "http://" . $server . ":" . $port;

	// Set up AWS SDK credentials
        $credentials = new Credentials($clientUser, $clientPwd);

        // Set up S3 client
        $this->s3Client = new S3Client(['version' => 'latest',
                                        'region'  => 'us-east-1',     // Replace with your MinIO region
                                        'endpoint' => $endpoint,
                                        'credentials' => $credentials]);
    }

    /**
     * Retrieves specified object from given bucket using AWS S3 tech
     * Note: This handler is expected to retrieve the object in string format
     *
     * @param $bucketName Bucket from where to get the object
     * @param $objectKey Object to retrieve
     * @return string The target object
     */
    public function getObject(string $bucketName, string $objectKey): string
    {
	$object = "";

        try{
            // Get the object content from the bucket
            $result = $this->s3Client->getObject(['Bucket' => $bucketName,
                                                  'Key'    => $objectKey]);

            $object = $result['Body']->getContents();
	}
	catch (S3Exception $e){
            // Handle S3Exception
            error_log(__METHOD__ . '() -> Error getting object<' . $objectKey . 
                      '>: ' . $e->getMessage());
        }
        catch (\Exception $e){
            // Handle general exceptions
            error_log(__METHOD__ . '() -> An error occurred: ' . $e->getMessage());
	}

	return $object;
    }
}

?>

