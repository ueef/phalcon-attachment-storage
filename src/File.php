<?php

namespace Phalcon\Extended\Attachment {

    use Phalcon\Di;

    class File implements Interfaces\File
    {
        /**
         * @var string
         */
        protected $key = null;

        /**
         * @var string
         */
        protected $name = null;

        /**
         * @var string
         */
        protected $extension = null;

        /**
         * @var string
         */
        protected $mime_type = null;

        /**
         * @var integer
         */
        protected $time = null;

        /**
         * @var array
         */
        protected $aliases = [];


        public function __construct(array $parameters = [])
        {
            $this->time = time();
            $this->name = $this->createName();

            $this->assign($parameters);
        }


        public function move(array $parameters)
        {
            $moved = clone $this;

            $this->assign($parameters);

            foreach ($moved->getAliases() as $alias) {
                if (!rename($moved->getPath($alias), $this->getPath($alias))) {
                    throw new File\Exception('Не удалось переместить файл ' . $moved->getPath($alias) . ' в ' . $this->getPath($alias));
                }
            }
        }


        public function copy(array $parameters)
        {
            $copy = clone $this;

            $copy->assign($parameters);

            foreach ($this->getAliases() as $alias) {
                if (file_exists($this->getPath($alias)) && !copy($this->getPath($alias), $copy->getPath($alias))) {
                    throw new File\Exception('Не удалось скопировать файл ' . $this->getPath($alias) . ' в ' . $copy->getPath($alias));
                }
            }


            return $copy;
        }


        public function store($path)
        {
            if (!file_exists($path)) {
                throw new File\Exception('Файл ' . $path . ' не существует');
            }

            if (!is_readable($path)) {
                throw new File\Exception('Файл ' . $path . ' не доступен для чтения');
            }

            if ($this->exists()) {
                $this->delete(false);
            }

            $this->assign([
                'mime_type' => $this->detectMimeType($path),
                'time' => time(),
            ]);

            if (!$this->extension && $this->mime_type) {
                $this->extension = MimeTypes::getExtension($this->mime_type);
            }


            if (!copy($path, $this->getPath())) {
                throw new File\Exception('Не удалось скопировать файл ' . $path . ' в ' . $this->getPath());
            }

            if (!chmod($this->getPath(), 0777)) {
                throw new File\Exception('Не установить права для файла ' . $this->getPath());
            }
        }


        public function exists($alias = null)
        {
            return file_exists($this->getPath($alias));
        }


        public function assign(array $parameters)
        {
            foreach ($parameters as $key => $value) {
                if (property_exists($this, $key)) {
                    $this->{$key} = $value;
                }
            }
        }


        public function delete($deleteEmptyDir = true)
        {
            foreach ($this->getAliases() as $alias) {
                if (file_exists($this->getPath($alias)) && !unlink($alias)) {
                    throw new File\Exception('Не удалось удалить файл ' . $this->getPath($alias));
                }
            }

            /*if ($deleteEmptyDir) {
                $this->storage()->remove($this->key, $this->dir, true);
            }*/
        }


        public function deleteAlias($alias)
        {
            if (file_exists($this->getPath($alias)) && !unlink($alias)) {
                throw new File\Exception('Не удалось удалить файл ' . $this->getPath($alias));
            }
        }


        public function toArray()
        {
            $result = [];
            foreach (['key', 'name', 'extension', 'mime_type', 'time', 'aliases'] as $key) {
                $result[$key] = $this->{$key};
            }

            return array_filter($result);
        }


        public function getDirUrl()
        {
            return $this->storage()->getUrl($this->key);
        }


        public function getDirPath()
        {
            return $this->storage()->getPath($this->key);
        }


        public function getUrl($alias = null)
        {
            return $this->getDirUrl() . '/' . $this->getFilename($alias);
        }


        public function getPath($alias = null)
        {
            return $this->getDirPath() . '/' . $this->getFilename($alias);
        }


        public function getKey()
        {
            return $this->key;
        }


        public function getName()
        {
            return $this->name;
        }


        public function getTime()
        {
            return $this->time;
        }


        public function getAliases()
        {
            return array_merge(null, $this->aliases);
        }


        public function getMimeType()
        {
            return $this->mime_type;
        }


        public function getExtension()
        {
            return $this->extension;
        }


        public function getFilename($alias = null)
        {
            $name = [
                $this->name
            ];

            $name[] = $alias;
            $name[] = $this->getExtension();

            return implode('.', array_filter($name));
        }


        protected function storage()
        {
            return Di::getDefault()->get('attachment-storage');
        }


        protected function createName()
        {
            return substr(uniqid(), -10);
        }


        protected function detectMimeType($path)
        {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $path);
            finfo_close($finfo);

            return $mime;
        }
    }
}

