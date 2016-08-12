<?php

namespace Venice\AppBundle\Services;

use Gaufrette\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * todo: Copied from necktie, needs review
 *
 * Class FileUploader
 * @package Venice\AppBundle\Services
 */
class FileUploader
{
    private $allowedMimeTypes = [
        'image/jpeg',
        'image/png',
        'image/gif'
    ];

    private $filesystem;
    private $region;
    private $bucket;

    public function __construct(Filesystem $filesystem, $region, $bucket)
    {
        $this->region = $region;
        $this->bucket = $bucket;
        $this->filesystem = $filesystem;
    }

    //S3 is one layer file system doesn't have folders
    public function upload(UploadedFile $file, $prefix = '')
    {
        // Check if the file's mime type is in the list of allowed mime types.
        if (!in_array($file->getClientMimeType(), $this->allowedMimeTypes)) {
            throw new \InvalidArgumentException(sprintf('Files of type %s are not allowed.', $file->getClientMimeType()));
        }
        if ($prefix) {
            $prefix .= '/';
        }

        // Generate a unique filename based on the date and add file extension of the uploaded file
        $filename = sprintf('%s%s/%s/%s/%s.%s', $prefix, date('Y'), date('m'), date('d'), uniqid(), $file->getClientOriginalExtension());

        $adapter = $this->filesystem->getAdapter();
        $adapter->setMetadata($filename, ['contentType' => $file->getClientMimeType()]);
        $adapter->write($filename, file_get_contents($file->getPathname()));

        return $filename;
    }


    /**
     * Generate full URL for file from s3 key(file identifier)
     *
     * @param string
     * @return string
     */
    public function genUrl($key)
    {
        return "https://s3-{$this->region}.amazonaws.com/{$this->bucket}/{$key}";
    }

    /**
     * Extract file identifier from full URL
     *
     * @param string
     * @return string
     */
    public function urlToKey($url)
    {
        $key = null;

        $prefix = "https://s3-{$this->region}.amazonaws.com/{$this->bucket}/";

        if (substr($url, 0, strlen($prefix)) == $prefix) {
            $key = substr($url, strlen($prefix));
        }
        return $key;
    }


    /**
     * Return all pictures in bucket
     *
     * @param string
     * @return array of string
     */
    public function getAllUrls()
    {
        $urls = [];
        $filesystem = $this->filesystem;
        $keys = $filesystem->keys();
        $adapter = $filesystem->getAdapter();
        foreach ($keys as $key) {
            if ($adapter->isDirectory($key)) {
                continue;
            }
            $urls[] = $this->genUrl($key);
        }

        return $urls;
    }

}