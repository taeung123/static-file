<?php

namespace VCComponent\Laravel\File\Http\Controllers\Api\Admin;

use Illuminate\Http\Request;
use VCComponent\Laravel\Vicoders\Core\Controllers\ApiController;

class StaticFileController extends ApiController
{

    public function __construct()
    {
        if (config('static-file.auth_middleware.admin.middleware') !== '') {
            $this->middleware(
                config('static-file.auth_middleware.admin.middleware'),
                ['except' => config('static-file.auth_middleware.admin.except')]
            );
        }
        else {
            throw new Exception("Admin middleware configuration is required");
        }
    }

    public function recursiveDirectoryIterator($directory = null, $files = array())
    {
        $iterator = new \DirectoryIterator($directory);
        foreach ($iterator as $index => $info) {
            if ($info->isFile()) {
                $list = array([
                    'name' => $info->__toString(),
                    'type' => 'file',
                ]);
                $files = array_merge_recursive($files, $list);
            } elseif (!$info->isDot()) {
                $list = array([
                    'name'    => $info->__toString(),
                    'type'    => 'folder',
                    "sub_dir" => $this->recursiveDirectoryIterator(
                        $directory . DIRECTORY_SEPARATOR . $info->__toString()
                    ),
                ]);
                if (!empty($files)) {
                    $files = array_merge_recursive($files, $list);
                } else {
                    $files = $list;
                }
            }
        }
        return $files;
    }

    public function index(Request $request)
    {
        $user      = $this->getAuthenticatedUser();
        $folerEdit = config('static-file.folder_edit');
        $path      = '../resources/views/' . $folerEdit;
        $data      = $this->recursiveDirectoryIterator($path);
        return response()->json($data);
    }
    public function getFileContent(Request $request)
    {
        $user      = $this->getAuthenticatedUser();
        $folerEdit = config('static-file.folder_edit');

        $file = $request->file;

        if (!$file) {
            throw new \Exception('Không tìm thấy file');
        }
        if (!is_file('../resources/views/' . $folerEdit . '/' . $file)) {
            throw new \Exception('Không tìm thấy file');
        }
        return response()->file('../resources/views/' . $folerEdit . '/' . $file);
    }

    public function update(Request $request)
    {
        $user = $this->getAuthenticatedUser();

        $folerEdit = config('static-file.folder_edit');

        $file = $request->file;

        if (!$file) {
            throw new \Exception('Không tìm thấy file');
        }

        $pathFile = '../resources/views/' . $folerEdit . '/' . $file;
        $data     = $request->content;

        if (!is_file($pathFile)) {
            throw new \Exception('Không tìm thấy file');
        }

        $fileOpen = fopen($pathFile, 'w');

        if (!$fileOpen) {
            throw new \Exception('Mở file không thành công');
        }

        fwrite($fileOpen, $data);
        fclose($fileOpen);
        return response()->file($pathFile);
    }
}
