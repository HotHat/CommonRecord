<?php
/**
 * 头像生成
 */
if (!function_exists('create_avatar')) {
    function create_avatar($name, $avatarPath) {
        $width = 256;
        $height = 256;
        $fontSize = 60;

        $last2 = mb_substr($name, -2);

        // 创建图像资源
        $image = imagecreatetruecolor($width, $height);
        // 分配颜色
        $blue = imagecolorallocate($image, 0, 127, 255);

        // 填充背景色
        imagefill($image, 0, 0, $blue);

        $white = imagecolorallocatealpha($image, 255, 255, 255,19);
        $font = storage_path('fonts/SourceHanSerifCN-SemiBold.otf');

        $bbox = imagettfbbox($fontSize, 0, $font, $last2);
        $bw = abs($bbox[2] - $bbox[0]);
        $bh = abs($bbox[7] - $bbox[1]);

        $sx = intval(($width - $bw) / 2);
        $sy = intval(($height - $bh) / 2);

        imagettftext($image, $fontSize, 0, $sx, $height-$sy-$bbox[1], $white, $font, $last2);

        imageSaveAlpha($image, true);
        imagepng($image, $avatarPath);

        imagedestroy($image);
    }
}

/**
 * 生成用户头像
 */
if (!function_exists('create_user_avatar')) {
    function create_user_avatar($id, $name) {
        $relPath = '/upload/users/avatar/' . date('Y/m');
        $avatarPath = public_path($relPath);

        mkdir_rec($avatarPath, 0755);

        $fileName = md5($name . $id) . '.png';

        create_avatar($name, $avatarPath . '/'. $fileName);

        return $relPath . '/' . $fileName;
    }
}
