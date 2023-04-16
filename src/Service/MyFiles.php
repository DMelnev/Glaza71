<?php


namespace App\Service;


class MyFiles
{

    private function checkPath(string &$path): bool
    {
        $path = trim($path);
        if ($path == '') $path = './';
        if ($path[-1] != '/') $path .= '/';
        if (file_exists($path)) return true;
        echo PHP_EOL . 'No such directory - ' . $path . PHP_EOL;
        return false;
    }


    public function getListFiles(string $path): array
    {
        $resultArray = [];
        if (!$this->checkPath($path)) return $resultArray;
        $filesList = array_diff(scandir($path), ['.', '..','.gitignore']);
        foreach ($filesList as $file) {
            if (!is_dir($path . $file)) {
                $resultArray[] = [
                    'filename' => $file,
                    'size' => filesize($path . $file),
                    'created' => filectime($path . $file),
                    'link' => $path . $file,
                ];
            }
        }
        return $resultArray;
    }

}