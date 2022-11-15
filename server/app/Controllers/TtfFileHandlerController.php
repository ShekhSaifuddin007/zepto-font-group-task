<?php

namespace App\Controllers;

use App\Core\Database;
use App\Core\Request;
use App\Helpers\TTFInfo;
use Exception;

class TtfFileHandlerController
{
    public $db;

    public function __construct()
    {
        $this->db = new Database;
    }

    /**
     * @throws Exception
     */
    public function index()
    {
        $data = $this->db->query("SELECT * FROM ttf_files")->fetchAll();

        $fonts = $this->getInfo($data);

        return response($fonts);
    }

    public function store(Request $request)
    {
        $status = 200;
        $errors = [];

        $file = $request->file('ttf_file');

        try {
            if (isset($file)) {
                $file_name = $file['name'];

                $file_size = $file['size'];

                $file_tmp = $file['tmp_name'];

                $explode = explode('.', $file['name']);

                $file_ext = strtolower(end($explode));

                if('ttf' !== $file_ext) {
                    $errors[] = "extension not allowed, please choose ttf file.";
                }

                if($file_size > 2097152) {
                    $errors[] = 'File max size 2 MB';
                }

                if (empty($errors)) {
                    $path = rootPath('public/files/');

                    move_uploaded_file($file_tmp, $path.$file_name);

                    $this->db->query("INSERT INTO ttf_files (`file_path`) VALUES ('$file_name')");

                    $message = "File has been uploaded.";
                    $status = 201;
                } else {
                    $message = "Something went wrong.";
                    $status = 417;
                }
            }
        } catch (Exception $e) {
            $message = "Something went wrong.";
            $status = 417;
            $errors[] = $e->getMessage();
        }

        return response([
            'message' => $message ?? '',
            'errors' => $errors
        ], $status);
    }

    public function delete()
    {
        $status = 200;

        try {
            $font = $this->db->query("SELECT * FROM ttf_files WHERE id = :id", [
                'id' => request()->input('id')
            ])->fetch();

            $file = rootPath("public/files/{$font['file_path']}");

            if (file_exists($file)) {
                @unlink($file);
            }

            $this->db->query("DELETE FROM ttf_files WHERE id = :id", [
                'id' => request()->input('id')
            ]);

            $message = "File has been deleted.";
        } catch (Exception $e) {
            $message = "Something went wrong.";
            $status = 417;
            $errors = $e->getMessage();
        }


        return response([
            'message' => $message,
            'errors' => $errors ?? ''
        ], $status);
    }

    protected function getInfo(array $data): array
    {
        $fonts = [];

        array_map(function ($font) use (&$fonts) {
            $filePath = rootPath("public/files/{$font['file_path']}");

            $ttfInfo = (new TTFInfo)->setFontFile($filePath);

            $fontInfo = $ttfInfo->getFontFamily();

            $fonts[] = [
                'id' => $font['id'],
                'family' => $fontInfo,
                'font_url' => asset("files/{$font['file_path']}"),
            ];
        }, $data);

        return $fonts;
    }
}