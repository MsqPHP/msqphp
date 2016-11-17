<?php declare(strict_types = 1);
namespace msqphp\vendor\captcha;

use msqphp\base;

final class Captcha
{
    private $pointer = [
        'width'=>80,
        'height'=>28,
        'type' =>3,
        'length'=>4
    ];
    public function width(int $width) : self
    {
        $this->pointer['width'] = $width;
        return $this;
    }
    public function height(int $height) : self
    {
        $this->pointer['height'] = $height;
        return $this;
    }
    public function type(int $type) : self
    {
        $this->pointer['type'] = $type;
        return $this;
    }
    public function length(int $length) : self
    {
        $this->pointer['length'] = $length;
        return $this;
    }
    public function pixel(int $pixel) : self
    {
        $this->pointer['pixel'] = $pixel;
        return $this;
    }
    public function line(int $line) : self
    {
        $this->pointer['line'] = $line;
        return $this;
    }
    public function font(string $ttf) : self
    {
        $this->font = $ttf;
        return $this;
    }
    public function dump() : void
    {
        $pointer = $this->pointer;

        $width = $pointer['width'];
        $height = $pointer['height'];
        $length = $pointer['length'];
        //1.创建画布
        $image = imagecreatetruecolor($width, $height);
        //2.颜色
        $white = imagecolorallocate($image, 255, 255, 255);
        $balck = imagecolorallocate($image, 0, 0, 0);
        //3.填充白色
        imagefilledrectangle($image, 1, 1, $width - 2, $height - 2, $white);
        //4.得到字符
        $this->pointer['chars'] = $chars = base\str\Str::random($pointer['type'], $length);
        //6.字体
        static $_font_arr = ['verdana.ttf', 'trebuc.ttf', 'tahoma.ttf'];
        for($i=$length;$i>=0;--$i) {
            $_size = random_int(14, 18);
            $_angle = random_int(-15, 15);
            $_x = 5 + $i * $_size;
            $_y = random_int(20, 26);
            $_color = imagecolorallocate($image, random_int(50, 90), random_int(80, 200), random_int(90, 180));
            $_font = './fonts/' . $_font_arr[0];
            $_text = substr($chars, $i, 1);
            imagettftext($image, $_size, $_angle, $_x, $_y, $_color, $_font, $_text);
        }
        if (isset($pointer['pixel'])) {
            for ($i = $pointer['pixel']; $i >= 0; --$i) {
                imagesetpixel($image, random_int(0, $width-1), random_int(0, $height-1), $balck);
            }
        }
        //7.点干扰
        //8.直线干扰
        if (isset($pointer['line'])) {
            for ($i = $pointer['line']; $i >= 0; --$i) {
                $color = imagecolorallocate($image, random_int(50, 90), random_int(80, 200), random_int(90, 180));
                imageline($image, random_int(0, $width-1), random_int(0, $height-1), random_int(0, $width-1), random_int(0, $height-1), $color);
            }
        }
        //9.导出
        base\header\Header::type('gif');
        base\header\Header::code(200);

        imagegif($image);
        imagedestroy($image);
    }
}