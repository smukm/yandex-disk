<?php

namespace smukm\YandexDisk\Helpers;

use smukm\YandexDisk\Exceptions\PathNotValidException;
use InvalidArgumentException;

class RequestHelper
{
    public function makeQuery(array $args, array $allowed): array
    {
        $ret = [];
        foreach($args as $k => $v) {
            if(!in_array($k, $allowed)) {
                throw new InvalidArgumentException('argument ' . $k . ' not allowed');
            }
            switch ($k) {
                case 'path':
                case 'from':
                case 'url':
                    $ret[$k] = $this->validatePath($v);
                    break;
                case 'fields':
                    if(count($v)) {
                        $ret[$k] = implode(',', $v);
                    }
                    break;
                case 'limit':
                case 'offset':
                    $ret[$k] = (int) $v;
                    break;
                case 'preview_crop':
                case 'permanently':
                case 'overwrite':
                    $ret[$k] = (bool) $v;
                    break;
                case 'preview_size':
                    $ret[$k] = $this->validatePreviewSize($v);
                    break;
                case 'sort':
                    $ret[$k] = $this->validateSort($v);
                    break;
                case 'media_type':
                    $ret[$k] = $this->validateMediaType($v);
                    break;
                case 'type':
                    $ret[$k] = $this->validateType($v);
                    break;
                default:
                    $ret[$k] = $v;
            }
        }

        return $ret;
    }

    private function validatePath($path): string
    {
        $pi = pathinfo($path);
        if(strlen($pi['basename']) > 255) {
            throw new PathNotValidException('Resource name larger then 255 symbols');
        }
        if(strlen($path) > 32760) {
            throw new PathNotValidException('Too long path');
        }

        return $this->urlencode($path);
    }

    private function validateSort($val)
    {
        $allowed = [
            'name','-name',
            'path','-path',
            'created','-created',
            'modified','-modified',
            'size','-size'
        ];
        if(!in_array($val, $allowed)) {
            throw new InvalidArgumentException('Invalid sort param');
        }
        return $val;
    }

    private function validatePreviewSize($val)
    {
        //allowed sizes: 120, 120x, x120, 120x240, S, M, L, XL, XXL, XXL
        if(preg_match('/^(x?\d+)$|^(\d+x?)$|^[SML]$|^XL$|^XXL$|^XXXL$|^\d+x\d+$/', $val)) {
            return $val;
        }
        throw new InvalidArgumentException('Invalid preview_size');
    }

    private function validateMediaType($val)
    {
        $arr = explode(',', $val);

        $allowed_options = [
            'audio',
            'backup',
            'book',
            'compressed',
            'development',
            'diskimage',
            'document',
            'encoded',
            'executable',
            'flash',
            'font',
            'image',
            'settings',
            'spreadsheet',
            'text',
            'unknown',
            'video',
            'web',
        ];

        $intersection = array_intersect($arr, $allowed_options);
        if(count($intersection) !== count($arr)) {
            throw new InvalidArgumentException('Invalid media type');
        }

        return $val;
    }

    private function validateType($type)
    {
        $allowed_options = [
            'dir',
            'file',
        ];
        if(!in_array($type, $allowed_options)) {
            throw new InvalidArgumentException('Invalid type');
        }

        return $type;
    }

    //@todo
    private function urlencode(string $url): string
    {
        $arr = explode('/', $url);
        //$arr = array_map(static fn($item) => urlencode($item), $arr);

        $ret = implode('/', $arr);

        return (empty($ret)) ? '/' : $ret;
    }
}