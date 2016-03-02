<?php

namespace Phalcon\Extended\Attachment\Interfaces {

    interface Storage
    {
        /**
         * @param string $key
         * @return string
         */
        public function getUrl($key);


        /**
         * @param string $key
         * @return string
         */
        public function getPath($key);
    }
}