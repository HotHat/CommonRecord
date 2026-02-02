<?php declare(strict_types=1);


class ZipDir
{
   
    public static function addFileToZip($zipFile, $dirPath){
        $zipObj = new ZipArchive();

        if ($zipObj->open($zipFile, ZipArchive::CREATE) === TRUE) {
            $cont = self::addFileToZipWithPath($zipObj, $dirPath, []);

            var_dump('file count: ' . $cont);

            $zipObj->close(); //关闭处理的zip文件

        } else {
            throw new \Exception('ZipArchive open failed:' . $zipFile);
        }

        if(file_exists($zipFile)){
            return true;
        }

        return false;
    }

    private static function addFileToZipWithPath($zipObj, $basePath, $pathArr, $contFile = 0) {
        $path = implode('/', $pathArr);
        $absPath = $basePath . '/' . $path;
        $handler = opendir($absPath); //打开当前文件夹由$path指定。
        while (($filename = readdir($handler)) !== false) {
            if ($filename != "." && $filename != "..") {//文件夹文件名字为'.'和‘..’，不要对他们进行操作
                if (is_dir($absPath. "/" . $filename)) {// 如果读取的某个对象是文件夹，则递归
                    $pathArr[] = $filename;
                    $zipObj->addEmptyDir(implode('/', $pathArr));
                    $cont = self::addFileToZipWithPath($zipObj, $basePath, $pathArr, $contFile++);
                    $contFile += $cont;
                } else { //将文件加入zip对象
                    $zipObj->addFile($absPath. "/" . $filename, $path . '/' . $filename);
                    $contFile++;
                }
            }
        }

        closedir($handler);

        return $contFile;
    }
}
