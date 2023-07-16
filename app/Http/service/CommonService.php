<?php


namespace App\Http\service;


class CommonService extends Service
{
    public function delDirAndFile($path, $delDir = true) {
        if (is_array($path)) {
            foreach ($path as $subPath)
                $this->delDirAndFile($subPath, $delDir);
        }
        if (is_dir((string)$path)) {
            $handle = opendir($path);
            if ($handle) {
                while (false !== ( $item = readdir($handle) )) {
                    if ($item != "." && $item != "..")
                        is_dir("$path/$item") ?  $this->delDirAndFile("$path/$item", $delDir) : unlink("$path/$item");
                }
                closedir($handle);
                if ($delDir)
                    return rmdir($path);
            }
        } else {
            if (file_exists($path)) {
                return unlink($path);
            } else {
                return FALSE;
            }
        }
        clearstatcache();
    }

    public function uploadStatus(){
        return $status = [
            1 => "店铺已上传",
            2 => "ERP已下载",
            3 => "ERP已回传",
            4 => "店铺已下栽"
        ];
    }
}