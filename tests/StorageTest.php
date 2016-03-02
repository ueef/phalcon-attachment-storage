<?php

use Phalcon\Extended\Attachment\Storage;

class MyClassTest extends PHPUnit_Framework_TestCase {

    /**
     * @dataProvider providerGetUrl
     */
    public function testGetUrl($storageDir, $prefixDir, $key, $result)
    {
        $storage = new Storage($storageDir, $prefixDir);

        $this->assertEquals($result, $storage->getUrl($key));
    }

    public function providerGetUrl()
    {
        $storageDir = __DIR__ . '/../temp';

        return [
            [$storageDir, null, 1, '/000/000/001'],
            [$storageDir, '', '1', '/000/000/001'],
            [$storageDir, null, '1', '/000/000/001'],
            [$storageDir, '1', 1, '/1/000/000/001'],
            [$storageDir, '2', '999999999', '/2/999/999/999'],
            [$storageDir, '3', '9999999999', '/3/999/999/9999'],
        ];
    }

    /**
     * @dataProvider providerGetPath
     */
    public function testGetPath($storageDir, $prefixDir, $key, $result)
    {
        $storage = new Storage($storageDir, $prefixDir);

        $this->assertEquals($result, $storage->getPath($key));
        $this->assertTrue(is_dir($result));
    }

    public function providerGetPath()
    {
        $storageDir = __DIR__ . '/../temp';

        return [
            [$storageDir, null, 1, $storageDir . '/000/000/001'],
            [$storageDir, '', '1', $storageDir . '/000/000/001'],
            [$storageDir, null, '1', $storageDir . '/000/000/001'],
            [$storageDir, '1', 1, $storageDir . '/1/000/000/001'],
            [$storageDir, '2', '999999999', $storageDir . '/2/999/999/999'],
            [$storageDir, '3', '9999999999', $storageDir . '/3/999/999/9999'],
        ];
    }
}