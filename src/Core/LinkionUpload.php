<?php

namespace Linkion\Core;

use Illuminate\Http\UploadedFile;

/**
 * tarit for file uploads
 */
trait LinkionUpload {


    /**
     * check if the files are 
     * @param array $files
     * @return bool
     */
    public static function isLnknFiles(array $files): bool{
        foreach($files as $file){
            $status = static::isLnknFile($file);
            if(!$status) return false;
        }
        return true;
    }

    /**
     * check if the file array has 
     * (originalName, mimeType, tmpName, size)
     * 
     * @param array $file
     * @return bool
     */
    public static function isLnknFile(array $file): bool{
        if( isset($file['originalName']) &&
            isset($file['mimeType']) &&
            isset($file['tmpName']) &&
            isset($file['size'])
        ) return true;
        return false;
    }


    /**
     * converts UploadedFile to linkion array file 
     * and stores it in linkion-temp folder
     * @param UploadedFile $file
     * @return array{mimeType: string|null, originalName: string, size: bool|int, tmpName: string}
     */
    public static function fileUpload(UploadedFile $file): array{
        $tmpName = uniqid().'_'.$file->getClientOriginalName();
        $file->storeAs('/linkion-temp', $tmpName);
        $newFile = [
            'originalName' => $file->getClientOriginalName(),
            'mimeType' => $file->getMimeType(),
            'tmpName' => $tmpName,
            'size' => $file->getSize(),
        ];
        return $newFile;
    }

    /**
     * retrieve and convert the uploaded file from the tmp folder to the UploadedFile
     * @param array $file
     * @return UploadedFile
     */
    public static function getUploadedFile(array $file): UploadedFile{
        $path = storage_path('app/private/linkion-temp');
        return new UploadedFile(
            $path. '/' .$file['tmpName'], 
            $file['originalName'],
            $file['mimeType'],
            null,
            true
        );
    }

    /**
     * get array of uploaded files from array of linkion files array
     * @param array $files
     * @return UploadedFile[]
     */
    public static function getUploadedFiles(array $files): array{
        $newFiles = [];
        foreach($files as $file){
            $newFiles[] = static::getUploadedFile($file);
        }
        return $newFiles;
    }

}