<?php

namespace Phalcon\Extended\Attachment {

    use Exception;

    class Image extends File
    {
        /**
         * @var array
         */
        protected $thumbnails = [];

        /**
         * @var array
         */
        protected static $imagesMimeHandlers = [
            'image/jpeg' => [
                'open' => [
                    'handler' => 'imagecreatefromjpeg',
                    'args' => [],
                ],
                'save' => [
                    'handler' => 'imagejpeg',
                    'args' => [],
                ],
                'extension' => 'jpg',
            ],
            'image/png' => [
                'open' => [
                    'handler' => 'imagecreatefrompng',
                    'args' => [],
                ],
                'save' => [
                    'handler' => 'imagepng',
                    'args' => [
                        9, PNG_ALL_FILTERS
                    ],
                ],
                'extension' => 'png',
            ],
            'image/gif' => [
                'open' => [
                    'handler' => 'imagecreatefromgif',
                    'args' => [],
                ],
                'save' => [
                    'handler' => 'imagegif',
                    'args' => [],
                ],
                'extension' => 'gif',
            ],
        ];


        public function thumbnail($alias)
        {
            if (!array_key_exists($alias, $this->thumbnails)) {
                throw new Exception('Алиас ' . $alias . ' не зарегистрирован');
            }

            $srcPath = $this->getPath();
            $dstPath = $this->getPath($alias);
            $srcInfo = $this->getInfo($srcPath);

            if (!array_key_exists($this->getMimeType(), static::$imagesMimeHandlers)) {
                throw new Exception('Тип ' . $this->getMimeType() . ' не поддерживается');
            }

            $image = $this->openImage($srcPath, $this->getMimeType());

            $image = $this->thumbnailImage(
                $image,
                $srcInfo['width'], $srcInfo['height'],
                $this->thumbnails[$alias]['width'], $this->thumbnails[$alias]['height'],
                $this->thumbnails[$alias]['strict']
            );

            $this->saveImage($image, $dstPath, $this->mime);
        }


        private function createImage($width, $height)
        {
            $image = imagecreatetruecolor($width, $height);
            imagefill($image, 0, 0, imagecolorallocatealpha($image, 0, 0, 0, 127));
            imagealphablending($image, true);


            return $image;
        }


        private function openImage($path, $mimeType)
        {
            $handler = static::$imagesMimeHandlers[$mimeType]['open']['handler'];
            $args = array_merge(
                [$path], static::$imagesMimeHandlers[$mimeType]['open']['args']
            );


            return call_user_func_array($handler, $args);
        }


        private function saveImage($image, $path, $mimeType)
        {
            $handler = static::$imagesMimeHandlers[$mimeType]['save']['handler'];
            $args = array_merge(
                [$image, $path], static::$imagesMimeHandlers[$mimeType]['save']['args']
            );


            return call_user_func_array($handler, $args);
        }


        private function thumbnailImage($srcImage, $srcWidth, $srcHeight, $width, $height, $strict)
        {
            if ($strict) {
                $coords = $this->calcStrictCoords($srcWidth, $srcHeight, $width, $height);
            } else {
                $coords = $this->calcCoords($srcWidth, $srcHeight, $width, $height);
            }

            $dstImage = $this->createImage($coords->dst->w, $coords->dst->h);

            imagecopyresampled(
                $dstImage, $srcImage,
                $coords->dst->x, $coords->dst->y,
                $coords->src->x, $coords->src->y,
                $coords->dst->w, $coords->dst->h,
                $coords->src->w, $coords->src->h
            );


            return $dstImage;
        }


        private function calcCoords($srcWidth, $srcHeight, $dstWidth, $dstHeight)
        {
            $aspect = $srcWidth / $srcHeight;

            $width = $srcWidth;
            $height = $srcHeight;

            if ($width > $dstWidth) {
                $width = $dstWidth;
                $height = $width * (1/$aspect);
            }

            if ($height > $dstHeight) {
                $height = $dstHeight;
                $width = $height * $aspect;
            }


            return (object) [
                'src' => (object) [
                    'x' => 0,
                    'y' => 0,
                    'w' => $srcWidth,
                    'h' => $srcHeight,
                ],
                'dst' => (object) [
                    'x' => 0,
                    'y' => 0,
                    'w' => floor($width),
                    'h' => floor($height),
                ],
            ];
        }


        private function calcStrictCoords($srcWidth, $srcHeight, $dstWidth, $dstHeight)
        {
            $aspect = $dstWidth / $dstHeight;

            $width = $srcWidth;
            $height = $srcWidth * (1/$aspect);

            if ($width < $srcWidth || $height > $srcHeight) {
                $height = $srcHeight;
                $width = $srcHeight * $aspect;
            }


            return (object) [
                'src' => (object) [
                    'x' => floor(($srcWidth/2) - ($width/2)),
                    'y' => floor(($srcHeight/2) - ($height/2)),
                    'w' => floor($width),
                    'h' => floor($height),
                ],
                'dst' => (object) [
                    'x' => 0,
                    'y' => 0,
                    'w' => $dstWidth,
                    'h' => $dstHeight,
                ],
            ];
        }


        private function getInfo($srcPath)
        {
            $info = getimagesize($srcPath);

            return [
                'width' => $info[0],
                'height' => $info[1],
                'mime' => $info['mime'],
            ];
        }
    }
}