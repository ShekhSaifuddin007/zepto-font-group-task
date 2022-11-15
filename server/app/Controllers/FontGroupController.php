<?php

namespace App\Controllers;

use App\Core\Database;
use App\Core\Request;
use App\Helpers\TTFInfo;
use Exception;

class FontGroupController
{
    public $db;
    public $dbl;

    public function __construct()
    {
        $this->db = new Database;
        $this->dbl = (new Database)->db();
    }

    /**
     * @throws Exception
     */
    public function index()
    {
        $data = $this->db->query("
            SELECT 
                a.id,
                a.title
            FROM 
                font_group_title a
        ")->fetchAll();

        $fontGroups = $this->processData($data);

        return response($fontGroups);
    }

    /**
     * @throws Exception
     */
    public function store(Request $request)
    {
        $form = $request->all();

        $errors = $this->validate($form);

        if (empty($errors)) {
            $title = $form['group_title'];
            $stmt = $this->dbl->prepare("INSERT INTO font_group_title (`title`) VALUES ('$title')");

            $stmt->execute();

            $groupId = $this->dbl->lastInsertId();

            foreach ($form['font_name'] as $key => $item) {
                $font_name = $form['font_name'][$key];
                $ttf_files_id = $form['ttf_files_id'][$key];
                $size = $form['size'][$key];
                $price = $form['price'][$key];

                $this->db->query("INSERT INTO font_group (`font_name`, `ttf_files_id`, `size`, `price`, `font_group_title_id`) 
                                                            VALUES ('$font_name', $ttf_files_id, '$size', '$price', $groupId)");
            }

            $message = "Group has been created.";
            $status = 201;
        } else {
            $message = "Something went wrong.";
            $status = 417;
        }

        return response([
            'message' => $message,
            'errors' => $errors
        ], $status);
    }

    /**
     * @throws Exception
     */
    public function show(Request $request)
    {
        $data = $this->db->query("
            SELECT 
                a.id,
                a.title
            FROM 
                font_group_title a
            WHERE id = ?
        ", [$request->input('id')])->fetch();

        $group = $this->getGroup($data);

        return response($group);
    }

    /**
     * @throws Exception
     */
    public function update(Request $request)
    {
        $form = $request->all();

        $errors = $this->validate($form);

        if (empty($errors)) {
            $title = $form['group_title'];

            $this->db->query("UPDATE font_group_title SET `title` = '$title' WHERE id = ?", [$form['id']]);

            $groupTitleId = $form['id'];

            foreach ($form['font_group_id'] as $key => $item) {
                $font_name = $form['font_name'][$key];
                $ttf_files_id = $form['ttf_files_id'][$key];
                $size = $form['size'][$key];
                $price = $form['price'][$key];

                if ($form['font_group_id'][$key] === '') {
                    $this->db->query("INSERT INTO font_group (`font_name`, `ttf_files_id`, `size`, `price`, `font_group_title_id`) 
                                                        VALUES ('$font_name', $ttf_files_id, '$size', '$price', $groupTitleId)");
                } else {
                    $this->db->query("UPDATE font_group SET 
                        `font_name` = '$font_name', 
                        `ttf_files_id` = $ttf_files_id, 
                        `size` = '$size', 
                        `price` = '$price', 
                        `font_group_title_id` = $groupTitleId
                        WHERE id = ?
                    ", [$form['font_group_id'][$key]]);
                }
            }

            $message = "Group has been created.";
            $status = 201;
        } else {
            $message = "Something went wrong.";
            $status = 417;
        }

        return response([
            'message' => $message,
            'errors' => $errors
        ], $status);
    }

    public function delete()
    {
        $status = 200;

        try {
            $this->db->query("DELETE FROM font_group WHERE font_group_title_id = :font_group_title_id", [
                'font_group_title_id' => request()->input('id')
            ]);

            $this->db->query("DELETE FROM font_group_title WHERE id = :id", [
                'id' => request()->input('id')
            ]);

            $message = "Font group has been deleted.";
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

    public function deleteRow()
    {
        $status = 200;

        try {
            $this->db->query("DELETE FROM font_group WHERE id = :id", [
                'id' => request()->input('id')
            ]);

            $message = "Font has been deleted.";
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

    protected function validate($form): array
    {
        $errors = [];

        if('' === $form['group_title']) {
            $errors[] = "This group_title field is required.";
        }

        foreach ($form['font_name'] as $f => $item) {
            if('' === $form['font_name'][$f]) {
                $errors[] = "This font_name field is required. {$f}";
            }
        }

        foreach ($form['ttf_files_id'] as $tt => $item) {
            if('' === $form['ttf_files_id'][$tt] || $form['ttf_files_id'][$tt] === 'null') {
                $errors[] = "This ttf_files_id field is required. {$tt}";
            }
        }

        return $errors;
    }

    /**
     * @throws Exception
     */
    private function processData($data): array
    {
        $fontGroups = [];

        foreach($data as $key => $value) {
            $fontGroups[$key]["id"] = $value["id"];
            $fontGroups[$key]["title"] = $value["title"];

            $groups = $this->db->query("
                SELECT 
                    *
                FROM 
                    font_group b
                        JOIN
                    ttf_files c ON b.ttf_files_id = c.id
                WHERE font_group_title_id = ?
            ", [$value["id"]])->fetchAll();

            $fontGroups[$key]['fonts'] = [];

            array_map(function ($font) use (&$fontGroups, $key) {
                $filePath = rootPath("public/files/{$font['file_path']}");

                $ttfInfo = (new TTFInfo)->setFontFile($filePath);

                $fontInfo = $ttfInfo->getFontFamily();

                $fontGroups[$key]['fonts'][] = $fontInfo;
            }, $groups);

            $fontGroups[$key]["fonts_count"] = count($groups);
        }

        return $fontGroups;
    }

    /**
     * @throws Exception
     */
    private function getGroup($data): array
    {
        $fontGroups["id"] = $data["id"];
        $fontGroups["title"] = $data["title"];

        $groups = $this->db->query("
            SELECT 
                *
            FROM 
                font_group
            WHERE font_group_title_id = ?
        ", [$data["id"]])->fetchAll();

        $fontGroups['font_groups'] = $groups;

        return $fontGroups;
    }
}