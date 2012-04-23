<?php

/*
 * This file is part of Composer.
 *
 * (c) Nils Adermann <naderman@naderman.de>
 *     Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Composer\Downloader\Storage;

use Composer\Util\Archive\CompressorInterface;
use Composer\Package\PackageInterface;

/**
 * PackageStorageInterface local archive implementation
 * 
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 */
class ArchiveStorage implements PackageStorageInterface
{
    /**
     * @var string
     */
    private $storageDir;
    /**
     * @var CompressorInterface
     */
    private $compressor;
    /**
     * @var WritableRepositoryInterface
     */
    private $repository;

    /**
     * Constructor
     *
     * @param string                      $storageDir Directory to store package archives
     * @param CompressorInterface         $compressor Archive compressor instance
     * @param WritableRepositoryInterface $repository Writable repository to store package information
     */
    public function __construct($storageDir, CompressorInterface $compressor)
    {
        $this->storageDir = $storageDir;
        $this->compressor = $compressor;
    }

    /**
     * Get storage directory
     *
     * @return string
     */
    public function getStorageDir()
    {
        return $this->storageDir;
    }

    /**
     * Get compressor
     *
     * @return CompressorInterface
     */
    public function getCompressor()
    {
        return $this->compressor;
    }

    /**
     * {@inheritDoc}
     */
    public function storePackage(PackageInterface $package, $sourceDir)
    {
        $storedPackage = $this->createStoredPackage($package);

        $fileName = $this->packageFilename($package);
        $this->compressor->compressDir($sourceDir, $fileName);

        return new Distribution($this->compressor->getArchiveType(), $fileName, sha1_file($fileName));
    }

    /**
     * Get stored package filename
     *
     * @param PackageInterface $package Original package
     *
     * @return string Filename
     */
    private function packageFilename(PackageInterface $package)
    {
        return sprintf('%s/%s.%s', $this->storageDir, $package->getUniqueName(), $this->compressor->getArchiveType());
    }
}
