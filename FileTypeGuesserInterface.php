<?php
namespace Liuggio\RackspaceCloudFilesBundle;
/**
 *
 */
interface FileTypeGuesserInterface
{
    /**
     * @static
     * @abstract
     * @param $filename
     * @return String
     */
    public static function guessByFileName($filename);

}
