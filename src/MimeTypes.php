<?php

namespace Phalcon\Extended\Attachment {

    class MimeTypes
    {
        private static $extensions = array(
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'application/rtf' => 'rtf',
            'text/rtf' => 'rtf',
            'text/plain' => 'txt',
            'application/msword' => 'doc',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
            'application/vnd.ms-powerpoint' => 'ppt',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'pptx',
            'application/vnd.ms-excel' => 'xls',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx',
            'application/vnd.oasis.opendocument.text' => 'odt',
            'application/vnd.oasis.opendocument.spreadsheet' => 'ods',
            'application/vnd.oasis.opendocument.presentation' => 'odp',
            'audio/mpeg' => 'mp3',
            'video/x-flv' => 'flv',
            'application/pdf' => 'pdf',
            'application/zip' => 'zip',
            'application/x-compressed-zip' => 'zip',
            'application/x-rar' => 'rar',
            'application/x-rar-compressed' => 'rar',
            'application/x-7z-compressed' => '7z',
        );


        public static function getExtension($mimeType)
        {
            if (array_key_exists($mimeType, self::$extensions)) {
                return self::$extensions[$mimeType];
            }

            return null;
        }
    }
}

