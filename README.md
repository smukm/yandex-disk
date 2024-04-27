# Yandex disk SDK

## Installation

Install extension through [composer](http://getcomposer.org/):

```
composer require smukm/yandex-disk
```

## Usage
```php
try {
    $client = new YandexDiskApi(
        env('yandexAccessToken'),
        new \GuzzleHttp\Client()
    );

    // Получение метаинформации о диске
    $diskInfo = $client->resource->getDiskInfo();
    
    // Получение рекурсивного списока ресурсов директории
    $list = $client->resource->listContents(directory: 'Uploads', recursive: true);

    // Копирование файла или папки
    $link = $client->disk->copy(from: 'file1.txt',path: 'file2.txt', overwrite: true);
    // Если копирование происходит асинхронно, то вернёт ответ с кодом 202 и ссылкой на асинхронную операцию.
    if($link->status === ResponseCode::HTTP_ACCEPTED) {
        $op_status = $client->disk->getStatus($link->operation_id);
        echo 'operation status: ' . $op_status['status'];
    }

    // Перемещение файла или папки
    $link = $client->disk->move(from: 'folder1/file1.txt', path: 'folder2/file1.txt', overwrite: true);
    // Если перемещение происходит асинхронно, то вернёт ответ с кодом 202 и ссылкой на асинхронную операцию.
    if($link->status === ResponseCode::HTTP_ACCEPTED) {
        $op_status = $client->disk->getStatus($link->operation_id);
        echo 'operation status: ' . $op_status['status'];
    }

    // Удаление файла или папки
    $link = $client->disk->removeResource(path:'folder2',permanently: true);
    // Если удаление происходит асинхронно, то вернёт ответ со статусом 202 и ссылкой на асинхронную операцию
    if($link->status === ResponseCode::HTTP_ACCEPTED) {
        $op_status = $client->disk->getStatus($link->operation_id);
        echo 'operation status: ' . $op_status['status'];
    }

    // Получение метаинформации о файле или каталоге
    $link = $client->resource->getMeta('file1.txt');
    echo 'file size: ' . $link->size;

    // Добавление пользовательской метаинформации для файла или каталога
    $client->resource->addMeta(path:'file1.txt',custom_properties: ['tag' => 'info'] );

    // Создание директории
    $client->disk->createDir('New folder');

    // Скачивание файла
    $save_path = '/path/to/file.txt';
    $file_contents = $client->io->downloadFile('file1.txt');
    file_put_contents($save_path, $file_contents);

    // Скачивание файла потоком
    $save_path = '/path/to/file.txt';
    $resource = fopen($save_path, 'w');
    $stream = $client->io->downloadStream('file1.txt');
    stream_copy_to_stream($stream->detach(), $resource);
    fclose($resource);

    // Получение списка файлов упорядоченного по имени
    $list = $client->resource->getFlatList(['sort' => 'name']);

    // Получение списка файлов упорядоченного по дате загрузки
    $list = $client->resource->getLastUploaded(['limit' => 5]);

    // Получение списка опубликованных ресурсов
    $list = $client->public->list();

    // Публикация ресурса
    $client->public->publish('file1.txt');

    // Снятие ресурса с публикации
    $client->public->unpublish('file1.txt');

    // Загрузка файла на диск
    $client->io->uploadFile(path: 'file2.txt', contents: 'new content', overwrite: true);

    // Загрузка файла на диск потоком
    $resource = \GuzzleHttp\Psr7\Utils::tryFopen('/path/to/bigfile.txt', 'r');
    $client->io->uploadStream('bigfile.txt', $resource);

    // Загрузка файла на диск по url
    $link = $client->io->uploadFileFromUrl(
        url: 'https://site.com/file.jpg',
        path: 'file.jpg');
    // проверить статус загрузки
    $op_status = $client->disk->getStatus($link->operation_id);
    echo 'operation status: ' . $op_status['status'];

    // Получение метаинформации о публичном ресурсе
    $info = $client->public->getMeta('https://disk.yandex.ru/i/OOrZOQR-w3VW3w');

    // Получение ссылки на скачивание публичного ресурса
    $link = $client->public->download('https://disk.yandex.ru/i/OOrZOQR-w3VW3w');

    // Сохранение публичного ресурса в папку Загрузки
    $link = $client->public->saveToDisk('https://disk.yandex.ru/i/OOrZOQR-w3VW3w');
    // Если сохранение происходит асинхронно, то вернёт ответ с кодом 202 и ссылкой на асинхронную операцию.
    if($link->status === ResponseCode::HTTP_ACCEPTED) {
        $op_status = $client->disk->getStatus($link->operation_id);
        echo 'operation status: ' . $op_status['status'];
    }

    // Очистка корзины
    $link = $client->trash->clear();
    // Если удаление происходит асинхронно, то вернёт ответ со статусом 202 и ссылкой на асинхронную операцию.
    if($link->status === ResponseCode::HTTP_ACCEPTED) {
        $op_status = $client->disk->getStatus($link->operation_id);
        echo 'operation status: ' . $op_status['status'];
    }

    // Получение содержимого корзины
    $list = $client->trash->getMeta('');

    // Восстановление ресурса из корзины
    $link = $client->trash->restore('bigfile.txt');
    // Если восстановление происходит асинхронно, то вернёт ответ с кодом 202 и ссылкой на асинхронную операцию.
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
    
    // Загрузка файла на диск
    $filesystem->write('subdir/file.txt', 'some contents');

    // Загрузка файла на диск потоком
    $resource = \GuzzleHttp\Psr7\Utils::tryFopen('/path/to/bigfile.txt', 'r');
    $filesystem->writeStream('subdir/bigfile.txt', $resource);

    // Перемещение файла
    $filesystem->move('subdir/file.txt', 'subdir/file2.txt');

    // Копирование файла
    $filesystem->copy('subdir/file.txt', 'subdir/file2.txt');

    // Удаление файла
    $filesystem->delete('subdir/file.txt');

    // Создание директории
    $filesystem->createDirectory('subdir/subdir2');

    // Удаление директории
    $filesystem->deleteDirectory('subdir/subdir2');

    // Публикация ресурса
    $filesystem->setVisibility('file.txt', 'public');

    // Снятие ресурса с публикации
    $filesystem->setVisibility('file.txt', 'private');

    // Проверка существования файла
    $exists = $filesystem->fileExists('file.txt');

    // Проверка существования директории
    $exists = $filesystem->directoryExists('dir');

    // Скачивание файла
    $content = $filesystem->read('subdir/file.txt');
    file_put_contents('/path/to/file.txt', $content);

    // Скачивание файла потоком
    $stream = $filesystem->readStream('subdir/file.txt');
    $resource = fopen('/path/to/file.txt', 'w');
    stream_copy_to_stream($stream, $resource);
    fclose($resource);

    // Получение рекурсивного списока ресурсов директории
    $generator = $filesystem->listContents('', true);
    foreach($generator as $r) {
    }

    // Получить размер файла в байтах
    $file_size = $filesystem->fileSize('file1.txt');

    // Получить тип данных у файла
    $mime_type = $filesystem->mimeType('file1.txt');

    // Получить дату последнего изменения файла
    $last_modified = $filesystem->lastModified('file1.txt');

    // Получить статус доступа к файлу
    $visibility = $filesystem->visibility('file1.txt');

} catch (Throwable $ex) {

}

```
