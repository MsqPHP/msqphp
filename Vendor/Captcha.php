<?php declare(strict_types = 1);
namespace Tool;
class CaptchaTool {
	static public $_captcha_chars = '';
	/**
	 * 得到随机字符
	 * @param  int $type   字符类型
	 * @param  int $length 字符长度
	 * @return string       生成的字符
	 */
	static private function buildRandomString(int $type = 3, int $length = 4) : string {
		$captcha_chars = '';
		switch ($type) {
			case 3:
				$captcha_chars .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
			case 2:
				$captcha_chars .= 'abcdefghijklmnopqrstuvwxyz';
			case 1:
				$captcha_chars .= '0123456789';
				break;
			default:
				$captcha_chars .= '0123456789';
				break;
		}
		return substr(str_shuffle($captcha_chars), 0,$length);
	}

	static public function createCaptcha(int $type = 3,int $length = 4, int $pixel = 0, int $line = 0) {
		static $_width = 80;
		static $_height = 28;
		//1.创建画布
		$_image = imagecreatetruecolor($_width, $_height);
		//2.颜色
		$white = imagecolorallocate($_image, 255, 255, 255);
		$balck = imagecolorallocate($_image, 0, 0, 0);
		//3.填充白色
		imagefilledrectangle($_image, 1, 1, $_width - 2, $_height - 2, $white);
		//4.得到字符
		$_chars = self::buildRandomString($type, $length);
		//5.存入本对象中
		self::$_captcha_chars = $_chars;
		//6.字体
		static $_font_arr = array('verdana.ttf','trebuc.ttf','tahoma.ttf');
		for($i=$length;$i>=0;--$i) {
			$_size = mt_rand(14,18);
			$_angle = mt_rand(-15,15);
			$_x = 5 + $i * $_size;
			$_y = mt_rand(20,26);
			$_color = imagecolorallocate($_image, mt_rand(50,90), mt_rand(80,200), mt_rand(90,180));
			$_font = './fonts/' . $_font_arr[0];
			$_text = substr($_chars, $i, 1);
			imagettftext($_image, $_size, $_angle, $_x, $_y, $_color, $_font, $_text);
		}
		//7.点干扰
		for($i=$pixel;$i>=0;--$i) {
			imagesetpixel($_image, mt_rand(0,$_width-1), mt_rand(0,$_height-1), $balck);
		}
		//8.直线干扰
		for($i=$line;$i>=0;--$i) {
			$color = imagecolorallocate($_image, mt_rand(50,90), mt_rand(80,200), mt_rand(90,180));
			imageline($_image, mt_rand(0,$_width-1), mt_rand(0,$_height-1),mt_rand(0,$_width-1), mt_rand(0,$_height-1), $color);
		}
		//9.导出
		header('Content-Type:image/gif');
		header('HTTP/1.1 200 OK');
		imagegif($_image);
		imagedestroy($_image);
	}
}