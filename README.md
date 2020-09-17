# Install :

Để sử dụng package chạy : composer require webpress/static-file

# Config:

để published file config của module chạy :
php artisan vendor:publish --provider="VCComponent\Laravel\File\Providers\FileServiceProvider"

# Chọn module static-file đẻ publish file config

Route Api:
|Phương thức| URL|

|GET | /api/admin/web/files|
|PUT | /api/admin/web/files|
