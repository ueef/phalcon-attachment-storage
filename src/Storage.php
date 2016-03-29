<?php

namespace Phalcon\Extended\Attachment {

    use Phalcon\Di;

    class Storage implements Interfaces\Storage
    {
        private $prefixDir;
        private $storageDir;

        public function __construct($storageDir, $prefixDir = null)
        {
            $this->prefixDir = $this->correctPath($prefixDir);
            $this->storageDir = $this->correctPath($storageDir);
        }


        public function getPath($key, $suffixDir = null)
        {
            return $this->makeDir($this->storageDir . $this->getUrl($key, $suffixDir));
        }


        public function getUrl($key, $suffixDir = null)
        {
            return $this->prefixDir . $this->buildUrl($key) . $this->correctPath($suffixDir);
        }


        private function buildUrl($key)
        {
            if (!is_numeric($key)) {
                throw new Storage\Exception('$key должен быть целым числом');
            }

            return preg_replace('/(.{3})(.{3})(.+)/', '/$1/$2/$3', sprintf('%09d', $key));
        }


        private function correctPath($path)
        {
            if (null === $path || !strlen($path)) {
                return $path;
            }

            return preg_replace('/^\/{0,}([^\/].{0,})\/{0,}$/U', '/$1', $path);
        }


        private function makeDir($dir, $mode = 0777)
        {
            $mask = umask(0);
            if (!is_dir($dir) && !mkdir($dir, $mode, true)) {
                throw new Storage\Exception('Не удалось создать папку ' . $dir);
            }
            umask($mask);

            return $dir;
        }
    }
}

