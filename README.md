# Yandex disk SDK

## Installation

Install extension through [composer](http://getcomposer.org/):

```
composer require smukm/yandex-disk
```

Useful Links
------------

- The Yandex Disk API [https://yandex.ru/dev/disk-api/doc/en/](https://yandex.ru/dev/disk-api/doc/en/)
- Sandbox [https://yandex.ru/dev/disk/poligon/](https://yandex.ru/dev/disk/poligon/) is `#behat`

Usage Example
-------------

```php
try {
    $client = new YandexDiskApi(
        env('yandexAccessToken'),
        new \GuzzleHttp\Client()
    );

    // Getting meta information about the user`s Yandex Disk
    $diskInfo = $client->resource->getDiskInfo();
    
    // Getting a recursive list of folder resources
    $list = $client->resource->listContents(directory: 'Uploads', recursive: true);

    // Copying a file or folder
    $link = $client->disk->copy(from: 'file1.txt',path: 'file2.txt', overwrite: true);
    // If copying has started but not completed yet, Yandex Disk returns the 202 Accepted code.
    if($link->status === ResponseCode::HTTP_ACCEPTED) {
        $op_status = $client->disk->getStatus($link->operation_id);
        echo 'operation status: ' . $op_status['status'];
    }

    // Moving a file or folder
    $link = $client->disk->move(from: 'folder1/file1.txt', path: 'folder2/file1.txt', overwrite: true);
    // If moving has started but not completed yet, Yandex Disk returns the 202 Accepted code.
    if($link->status === ResponseCode::HTTP_ACCEPTED) {
        $op_status = $client->disk->getStatus($link->operation_id);
        echo 'operation status: ' . $op_status['status'];
    }

    // Deleting a file or folder
    $link = $client->disk->removeResource(path:'folder2',permanently: true);
    // If deleting has started but not completed yet, Yandex Disk returns the 202 Accepted code.
    if($link->status === ResponseCode::HTTP_ACCEPTED) {
        $op_status = $client->disk->getStatus($link->operation_id);
        echo 'operation status: ' . $op_status['status'];
    }

    // Getting File or folder meta information
    $link = $client->resource->getMeta('file1.txt');
    echo 'file size: ' . $link->size;

    // Adding resource meta information
    $client->resource->addMeta(path:'file1.txt',custom_properties: ['tag' => 'info'] );

    // Creating a folder
    $client->disk->createDir('New folder');

    // Downloading a file from Yandex Disk
    $save_path = '/path/to/file.txt';
    $file_contents = $client->io->downloadFile('file1.txt');
    file_put_contents($save_path, $file_contents);

    // Downloading a file from Yandex Disk use stream
    $save_path = '/path/to/file.txt';
    $resource = fopen($save_path, 'w');
    $stream = $client->io->downloadStream('file1.txt');
    stream_copy_to_stream($stream->detach(), $resource);
    fclose($resource);

    // Getting a list of files ordered by name
    $list = $client->resource->getFlatList(['sort' => 'name']);

    // Getting a list of files ordered by upload date
    $list = $client->resource->getLastUploaded(['limit' => 5]);

    // Getting a list of published resourses
    $list = $client->public->list();

    // Publishing a file or folder
    $client->public->publish('file1.txt');

    // Unpublishing a file or folder
    $client->public->unpublish('file1.txt');

    // Uploading a file to Yandex Disk
    $client->io->uploadFile(path: 'file2.txt', contents: 'new content', overwrite: true);

    // Uploading a file to Yandex Disk use stream
    $resource = \GuzzleHttp\Psr7\Utils::tryFopen('/path/to/bigfile.txt', 'r');
    $client->io->uploadStream('bigfile.txt', $resource);

    // Downloading a file from the internet to Yandex Disk
    $link = $client->io->uploadFileFromUrl(
        url: 'https://site.com/file.jpg',
        path: 'file.jpg');
    // check up a download status
    $op_status = $client->disk->getStatus($link->operation_id);
    echo 'operation status: ' . $op_status['status'];

    // Getting a public resource meta information
    $info = $client->public->getMeta('https://disk.yandex.ru/i/OOrZOQR-w3VW3w');

    // Getting links for downloading a public file or folder
    $link = $client->public->download('https://disk.yandex.ru/i/OOrZOQR-w3VW3w');

    // Saving a public file to the Downloads folder
    $link = $client->public->saveToDisk('https://disk.yandex.ru/i/OOrZOQR-w3VW3w');
    // If saving has started but not completed yet, Yandex Disk returns the 202 Accepted code.
    if($link->status === ResponseCode::HTTP_ACCEPTED) {
        $op_status = $client->disk->getStatus($link->operation_id);
        echo 'operation status: ' . $op_status['status'];
    }

    // Emptying Trash
    $link = $client->trash->clear();
    // If deleting has started but not completed yet, Yandex Disk returns the 202 Accepted code.
    if($link->status === ResponseCode::HTTP_ACCEPTED) {
        $op_status = $client->disk->getStatus($link->operation_id);
        echo 'operation status: ' . $op_status['status'];
    }

    // Getting contents of trash
    $list = $client->trash->getMeta('');

    // Restoring a file or folder from Trash
    $link = $client->trash->restore('bigfile.txt');
    // If restoring has started but not completed yet, Yandex Disk returns the 202 Accepted code.
    if($link->status === ResponseCode::HTTP_ACCEPTED) {
        $op_status = $client->disk->getStatus($link->operation_id);
        echo 'operation status: ' . $op_status['status'];
    }

} catch (YandexDiskNotFound $ex) {
    var_dump($ex->getError());
} catch (YandexDiskConflict $ex) {
    var_dump($ex->getError());
} catch (Throwable $ex) {
    echo $ex->getMessage();
}

```

Or use adapter for league flysystem
```php


try {
    $client = new YandexDiskApi(
        env('yandexAccessToken'),
        new \GuzzleHttp\Client()
    );
    $adapter = new Adapter($client, 'Test');
    $filesystem = new Filesystem($adapter);
    
    // Uploading a file to Yandex Disk
    $filesystem->write('subdir/file.txt', 'some contents');

    // Uploading a file to Yandex Disk use stream
    $resource = \GuzzleHttp\Psr7\Utils::tryFopen('/path/to/bigfile.txt', 'r');
    $filesystem->writeStream('subdir/bigfile.txt', $resource);

    // Moving a file
    $filesystem->move('subdir/file.txt', 'subdir/file2.txt');

    // Copying a file
    $filesystem->copy('subdir/file.txt', 'subdir/file2.txt');

    // Deleting a file
    $filesystem->delete('subdir/file.txt');

    // Making a folder
    $filesystem->createDirectory('subdir/subdir2');

    // Deleting a folder
    $filesystem->deleteDirectory('subdir/subdir2');

    // Publishing a resource
    $filesystem->setVisibility('file.txt', 'public');

    // Unpublished a resource
    $filesystem->setVisibility('file.txt', 'private');

    // Checking the file existing
    $exists = $filesystem->fileExists('file.txt');

    // Checking the folder existing
    $exists = $filesystem->directoryExists('dir');

    // Downloading a file
    $content = $filesystem->read('subdir/file.txt');
    file_put_contents('/path/to/file.txt', $content);

    // Downloading a file use stream
    $stream = $filesystem->readStream('subdir/file.txt');
    $resource = fopen('/path/to/file.txt', 'w');
    stream_copy_to_stream($stream, $resource);
    fclose($resource);

    // Getting a recursive list of folder resources
    $generator = $filesystem->listContents('', true);
    foreach($generator as $r) {
    }

    // Get the file size in bytes
    $file_size = $filesystem->fileSize('file1.txt');

    // Get the file data type
    $mime_type = $filesystem->mimeType('file1.txt');

    // Get the file last change date
    $last_modified = $filesystem->lastModified('file1.txt');

    // Get the file published status
    $visibility = $filesystem->visibility('file1.txt');

} catch (Throwable $ex) {

}

```
