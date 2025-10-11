<?php

namespace Linkion\Core;

use Illuminate\Http\UploadedFile;

trait LinkionUpload {


    public static function isLnknFiles(array $files): bool{
        foreach($files as $file){
            $status = static::isLnknFile($file);
            if(!$status) return false;
        }
        return true;
    }

    public static function isLnknFile(array $file): bool{
        if( isset($file['originalName']) &&
            isset($file['mimeType']) &&
            isset($file['tmpName']) &&
            isset($file['size'])
        ) return true;
        return false;
    }


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

    public static function getUploadedFiles(array $files): array{
        $newFiles = [];
        foreach($files as $file){
            $newFiles[] = static::getUploadedFile($file);
        }
        return $newFiles;
    }

}