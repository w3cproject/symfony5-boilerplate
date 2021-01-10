<?php

namespace App\Utils;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * Class FileUploader
 * @package App\Utils
 */
class FileUploader
{
    /**
     * @var string
     */
    private string $uploadsDirectory;

    /**
     * @var string
     */
    private string $publicDirectory;

    /**
     * @var SluggerInterface
     */
    private SluggerInterface $slugger;

    /**
     * FileUploader constructor.
     *
     * @param                  $uploadsDirectory
     * @param                  $publicDirectory
     * @param SluggerInterface $slugger
     */
    public function __construct(
        $uploadsDirectory,
        $publicDirectory,
        SluggerInterface $slugger
    ) {
        $this->uploadsDirectory = $uploadsDirectory;
        $this->publicDirectory  = $publicDirectory;
        $this->slugger          = $slugger;
    }

    public function upload(UploadedFile $file, $directory = null)
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename     = $this->slugger->slug($originalFilename);
        $fileName         = $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();

        try {
            $file->move($directory ?? $this->getUploadsDirectory(), $fileName);
        } catch (FileException $e) {

        }

        return $fileName;
    }

    /**
     * @return string
     */
    public function getUploadsDirectory(): string
    {
        return $this->uploadsDirectory;
    }

    /**
     * @return string
     */
    public function getPublicDirectory(): string
    {
        return $this->publicDirectory;
    }
}
