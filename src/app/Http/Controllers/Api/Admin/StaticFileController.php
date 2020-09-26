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
        $user = $this->getAuthenticatedUser();

        $folerEdit = config('static-file.folder_edit');
        if (strpos($request->file, $folerEdit) !== 0) {
            throw new \Exception('Không thể edit file');
        }
        $file = $request->file;
        if (!$file) {
            throw new \Exception('Không tìm thấy file');
        }
        if (is_file('../resources/views/' . $file)) {
            return response()->file('../resources/views/' . $file);
        } else {
            throw new \Exception('Không tìm thấy file');
        }
    }

    public function update(Request $request)
    {
        $user = $this->getAuthenticatedUser();

        $folerEdit = config('static-file.folder_edit');

        if ($request->file) {
            if (strpos($request->file, $folerEdit) !== 0) {
                throw new \Exception('Không thể edit file');
            }
            $file = '../resources/views/' . $request->file;
            $data = $request->content;
            if (is_file($file)) {
                $fileOpen = fopen($file, 'w');
                if (!$fileOpen) {
                    throw new \Exception('Mở file không thành công');
                } else {
                    fwrite($fileOpen, $data);
                    fclose($fileOpen);
                    return response()->file($file);
                }
            } else {
                throw new \Exception('Không tìm thấy file');
            }
        }
        throw new \Exception('Không tìm thấy file');
    }
}
