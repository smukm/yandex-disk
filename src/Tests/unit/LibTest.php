<?php

namespace smukm\YandexDisk\Tests\unit;

use Codeception\Test\Unit;
use InvalidArgumentException;
use smukm\YandexDisk\Helpers\RequestHelper;

class LibTest extends Unit
{
    protected $tester;


    public function testValidOptions()
    {
        $requestHelper = new RequestHelper();

        $options = [
            'path' => 'dir/filename.txt',
            'fields' => ['name','_embedded.items.path'],
            'limit' => 100,
            'offset' => 10,
            'preview_crop' => false,
            'preview_size' => '100x100',
            'sort' => '-name',
            'media_type' => 'image',
            'type' => 'file',
            'permanently' => true,
            'from' => 'dir/dir',
            'overwrite' => true,
        ];

        $allowed_options = [
            'path',
            'fields',
            'limit',
            'offset',
            'preview_crop',
            'preview_size',
            'sort',
            'media_type',
            'type',
            'permanently',
            'from',
            'overwrite',
        ];
        $query = $requestHelper->makeQuery($options, $allowed_options);
        $this->assertEquals('dir/filename.txt', $query['path']);
        $this->assertEquals('name,_embedded.items.path', $query['fields']);
        $this->assertEquals(100, $query['limit']);
        $this->assertEquals(10, $query['offset']);
        $this->assertEquals(false, $query['preview_crop']);
        $this->assertEquals('100x100', $query['preview_size']);
        $this->assertEquals('-name', $query['sort']);
        $this->assertEquals('image', $query['media_type']);
        $this->assertEquals('file', $query['type']);
        $this->assertEquals(true, $query['permanently']);
        $this->assertEquals('dir/dir', $query['from']);
        $this->assertEquals(true, $query['overwrite']);
    }

    public function testNotAllowedOption()
    {
        $requestHelper = new RequestHelper();

        $this->tester->expectThrowable(InvalidArgumentException::class, function () use($requestHelper) {
            $options = [
                'path' => 'dir/filename.txt',
                'limit' => 100,
            ];
            $allowed_options = [
                'path',
            ];
            $requestHelper->makeQuery($options, $allowed_options);
        });
    }

    public function testValidatePreviewSize()
    {
        $requestHelper = new RequestHelper();

        $this->tester->expectThrowable(InvalidArgumentException::class, function () use ($requestHelper) {
            $requestHelper->makeQuery(
                ['preview_size' => '100-100'],
                ['preview_size']
            );
        });


        $this->tester->expectThrowable(InvalidArgumentException::class, function () use ($requestHelper) {
            $requestHelper->makeQuery(
                ['preview_size' => '-100'],
                ['preview_size']
            );
        });

        $this->tester->expectThrowable(InvalidArgumentException::class, function () use ($requestHelper) {
            $requestHelper->makeQuery(
                ['preview_size' => 'x100x'],
                ['preview_size']
            );
        });

        $query = $requestHelper->makeQuery(
            ['preview_size' => 'x100'],
            ['preview_size']
        );
        $this->assertEquals('x100', $query['preview_size']);

        $query = $requestHelper->makeQuery(
            ['preview_size' => '100x'],
            ['preview_size']
        );
        $this->assertEquals('100x', $query['preview_size']);

        $query = $requestHelper->makeQuery(
            ['preview_size' => '100x100'],
            ['preview_size']
        );
        $this->assertEquals('100x100', $query['preview_size']);
    }

    public function testValidateMediaType()
    {
        $requestHelper = new RequestHelper();

        $this->tester->expectThrowable(InvalidArgumentException::class, function () use ($requestHelper) {
            $requestHelper->makeQuery(
                ['media_type' => 'sometype'],
                ['media_type']
            );
        });

        $query = $requestHelper->makeQuery(
            ['media_type' => 'audio'],
            ['media_type']
        );
        $this->assertEquals('audio', $query['media_type']);

        $query = $requestHelper->makeQuery(
            ['media_type' => 'audio,video,document'],
            ['media_type']
        );
        $this->assertEquals('audio,video,document', $query['media_type']);

        $this->tester->expectThrowable(InvalidArgumentException::class, function () use ($requestHelper) {
            $requestHelper->makeQuery(
                ['media_type' => 'audio,video,sometype'],
                ['media_type']
            );
        });
    }
}
