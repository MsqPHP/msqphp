<?php declare(strict_types = 1);
namespace Core\Vendor\Date;
class Date {
	/**
	 * 年月日合法检测
	 * @param  int    $year  年
	 * @param  int    $month 月
	 * @param  int    $day   日
	 * @return bool 	是否合法
	 */
	static public function checkDate(int $year,int $month,int $day) : bool {
		return checkdate($month,$day,$year);
	}
	/**
	 * 阳历转阴历
	 * @param  int    $year  年
	 * @param  int    $month 月
	 * @param  int    $day   日
	 */
	static public function toChineseTraditionalCalendar(int $year,int $month,int $day) {
		self::checkDate($year,$month,$day) || exit('日期非法');
		//农历每月的天数
		//1900 12 21开始  天干7 地支1
		//1900 12 21 十月三十 庚子年 【鼠年】戊子月 戊辰日
		static $cEveryMonth=array(0=>array(8,0,0,0,0,0,0,0,0,0,0,0,29,30),1=>array(0,29,30,29,29,30,29,30,29,30,30,30,29,0),2=>array(0,30,29,30,29,29,30,29,30,29,30,30,30,0),3=>array(5,29,30,29,30,29,29,30,29,29,30,30,29,30),4=>array(0,30,30,29,30,29,29,30,29,29,30,30,29,0),5=>array(0,30,30,29,30,30,29,29,30,29,30,29,30,0),6=>array(4,29,30,30,29,30,29,30,29,30,29,30,29,30),7=>array(0,29,30,29,30,29,30,30,29,30,29,30,29,0),8=>array(0,30,29,29,30,30,29,30,29,30,30,29,30,0),9=>array(2,29,30,29,29,30,29,30,29,30,30,30,29,30),10=>array(0,29,30,29,29,30,29,30,29,30,30,30,29,0),11=>array(6,30,29,30,29,29,30,29,29,30,30,29,30,30),12=>array(0,30,29,30,29,29,30,29,29,30,30,29,30,0),13=>array(0,30,30,29,30,29,29,30,29,29,30,29,30,0),14=>array(5,30,30,29,30,29,30,29,30,29,30,29,29,30),15=>array(0,30,29,30,30,29,30,29,30,29,30,29,30,0),16=>array(0,29,30,29,30,29,30,30,29,30,29,30,29,0),17=>array(2,30,29,29,30,29,30,30,29,30,30,29,30,29),18=>array(0,30,29,29,30,29,30,29,30,30,29,30,30,0),19=>array(7,29,30,29,29,30,29,29,30,30,29,30,30,30),20=>array(0,29,30,29,29,30,29,29,30,30,29,30,30,0),21=>array(0,30,29,30,29,29,30,29,29,30,29,30,30,0),22=>array(5,30,29,30,30,29,29,30,29,29,30,29,30,30),23=>array(0,29,30,30,29,30,29,30,29,29,30,29,30,0),24=>array(0,29,30,30,29,30,30,29,30,29,30,29,29,0),25=>array(4,30,29,30,29,30,30,29,30,30,29,30,29,30),26=>array(0,29,29,30,29,30,29,30,30,29,30,30,29,0),27=>array(0,30,29,29,30,29,30,29,30,29,30,30,30,0),28=>array(2,29,30,29,29,30,29,29,30,29,30,30,30,30),29=>array(0,29,30,29,29,30,29,29,30,29,30,30,30,0),30=>array(6,29,30,30,29,29,30,29,29,30,29,30,30,29),31=>array(0,30,30,29,30,29,30,29,29,30,29,30,29,0),32=>array(0,30,30,30,29,30,29,30,29,29,30,29,30,0),33=>array(5,29,30,30,29,30,30,29,30,29,30,29,29,30),34=>array(0,29,30,29,30,30,29,30,29,30,30,29,30,0),35=>array(0,29,29,30,29,30,29,30,30,29,30,30,29,0),36=>array(3,30,29,29,30,29,29,30,30,29,30,30,30,29),37=>array(0,30,29,29,30,29,29,30,29,30,30,30,29,0),38=>array(7,30,30,29,29,30,29,29,30,29,30,30,29,30),39=>array(0,30,30,29,29,30,29,29,30,29,30,29,30,0),40=>array(0,30,30,29,30,29,30,29,29,30,29,30,29,0),41=>array(6,30,30,29,30,30,29,30,29,29,30,29,30,29),42=>array(0,30,29,30,30,29,30,29,30,29,30,29,30,0),43=>array(0,29,30,29,30,29,30,30,29,30,29,30,29,0),44=>array(4,30,29,30,29,30,29,30,29,30,30,29,30,30),45=>array(0,29,29,30,29,29,30,29,30,30,30,29,30,0),46=>array(0,30,29,29,30,29,29,30,29,30,30,29,30,0),47=>array(2,30,30,29,29,30,29,29,30,29,30,29,30,30),48=>array(0,30,29,30,29,30,29,29,30,29,30,29,30,0),49=>array(7,30,29,30,30,29,30,29,29,30,29,30,29,30),50=>array(0,29,30,30,29,30,30,29,29,30,29,30,29,0),51=>array(0,30,29,30,30,29,30,29,30,29,30,29,30,0),52=>array(5,29,30,29,30,29,30,29,30,30,29,30,29,30),53=>array(0,29,30,29,29,30,30,29,30,30,29,30,29,0),54=>array(0,30,29,30,29,29,30,29,30,30,29,30,30,0),55=>array(3,29,30,29,30,29,29,30,29,30,29,30,30,30),56=>array(0,29,30,29,30,29,29,30,29,30,29,30,30,0),57=>array(8,30,29,30,29,30,29,29,30,29,30,29,30,29),58=>array(0,30,30,30,29,30,29,29,30,29,30,29,30,0),59=>array(0,29,30,30,29,30,29,30,29,30,29,30,29,0),60=>array(6,30,29,30,29,30,30,29,30,29,30,29,30,29),61=>array(0,30,29,30,29,30,29,30,30,29,30,29,30,0),62=>array(0,29,30,29,29,30,29,30,30,29,30,30,29,0),63=>array(4,30,29,30,29,29,30,29,30,29,30,30,30,29),64=>array(0,30,29,30,29,29,30,29,30,29,30,30,30,0),65=>array(0,29,30,29,30,29,29,30,29,29,30,30,29,0),66=>array(3,30,30,30,29,30,29,29,30,29,29,30,30,29),67=>array(0,30,30,29,30,30,29,29,30,29,30,29,30,0),68=>array(7,29,30,29,30,30,29,30,29,30,29,30,29,30),69=>array(0,29,30,29,30,29,30,30,29,30,29,30,29,0),70=>array(0,30,29,29,30,29,30,30,29,30,30,29,30,0),71=>array(5,29,30,29,29,30,29,30,29,30,30,30,29,30),72=>array(0,29,30,29,29,30,29,30,29,30,30,29,30,0),73=>array(0,30,29,30,29,29,30,29,29,30,30,29,30,0),74=>array(4,30,30,29,30,29,29,30,29,29,30,30,29,30),75=>array(0,30,30,29,30,29,29,30,29,29,30,29,30,0),76=>array(8,30,30,29,30,29,30,29,30,29,29,30,29,30),77=>array(0,30,29,30,30,29,30,29,30,29,30,29,29,0),78=>array(0,30,29,30,30,29,30,30,29,30,29,30,29,0),79=>array(6,30,29,29,30,29,30,30,29,30,30,29,30,29),80=>array(0,30,29,29,30,29,30,29,30,30,29,30,30,0),81=>array(0,29,30,29,29,30,29,29,30,30,29,30,30,0),82=>array(4,30,29,30,29,29,30,29,29,30,29,30,30,30),83=>array(0,30,29,30,29,29,30,29,29,30,29,30,30,0),84=>array(10,30,29,30,30,29,29,30,29,29,30,29,30,30),85=>array(0,29,30,30,29,30,29,30,29,29,30,29,30,0),86=>array(0,29,30,30,29,30,30,29,30,29,30,29,29,0),87=>array(6,30,29,30,29,30,30,29,30,30,29,30,29,29),88=>array(0,30,29,30,29,30,29,30,30,29,30,30,29,0),89=>array(0,30,29,29,30,29,29,30,30,29,30,30,30,0),90=>array(5,29,30,29,29,30,29,29,30,29,30,30,30,30),91=>array(0,29,30,29,29,30,29,29,30,29,30,30,30,0),92=>array(0,29,30,30,29,29,30,29,29,30,29,30,30,0),93=>array(3,29,30,30,29,30,29,30,29,29,30,29,30,29),94=>array(0,30,30,30,29,30,29,30,29,29,30,29,30,0),95=>array(8,29,30,30,29,30,29,30,30,29,29,30,29,30),96=>array(0,29,30,29,30,30,29,30,29,30,30,29,29,0),97=>array(0,30,29,30,29,30,29,30,30,29,30,30,29,0),98=>array(5,30,29,29,30,29,29,30,30,29,30,30,29,30),99=>array(0,30,29,29,30,29,29,30,29,30,30,30,29,0),100=>array(0,30,30,29,29,30,29,29,30,29,30,30,29,0),101=>array(4,30,30,29,30,29,30,29,29,30,29,30,29,30),102=>array(0,30,30,29,30,29,30,29,29,30,29,30,29,0),103=>array(0,30,30,29,30,30,29,30,29,29,30,29,30,0),104=>array(2,29,30,29,30,30,29,30,29,30,29,30,29,30),105=>array(0,29,30,29,30,29,30,30,29,30,29,30,29,0),106=>array(7,30,29,30,29,30,29,30,29,30,30,29,30,30),107=>array(0,29,29,30,29,29,30,29,30,30,30,29,30,0),108=>array(0,30,29,29,30,29,29,30,29,30,30,29,30,0),109=>array(5,30,30,29,29,30,29,29,30,29,30,29,30,30),110=>array(0,30,29,30,29,30,29,29,30,29,30,29,30,0),111=>array(0,30,29,30,30,29,30,29,29,30,29,30,29,0),112=>array(4,30,29,30,30,29,30,29,30,29,30,29,30,29),113=>array(0,30,29,30,29,30,30,29,30,29,30,29,30,0),114=>array(9,29,30,29,30,29,30,29,30,30,29,30,29,30),115=>array(0,29,30,29,29,30,29,30,30,30,29,30,29,0),116=>array(0,30,29,30,29,29,30,29,30,30,29,30,30,0),117=>array(6,29,30,29,30,29,29,30,29,30,29,30,30,30),118=>array(0,29,30,29,30,29,29,30,29,30,29,30,30,0),119=>array(0,30,29,30,29,30,29,29,30,29,29,30,30,0),120=>array(4,29,30,30,30,29,30,29,29,30,29,30,29,30));
		//农历天干
		static $cTianGan = array('天干','甲','乙','丙','丁','戊','己','庚','辛','壬','癸');
		//农历地支
		static $cDizhi = array('地支','子','丑','寅','卯','辰','巳','午','未','申','酉','戌','亥');
		//十二生肖
		static $cShengXiao = array('年','鼠','牛','虎','兔','龙','蛇','马','羊','猴','鸡','狗','猪');
		//农历月
		static $cMonth=array('闰','正','二','三','四','五','六','七','八','九','十','十一','十二','月');
		//农历日
		static $cDay=array('空','初一','初二','初三','初四','初五','初六','初七','初八','初九','初十','十一','十二','十三','十四','十五','十六','十七','十八','十九','二十','廿一','廿二','廿三','廿四','廿五','廿六','廿七','廿八','廿九','三十');
		//周几
		static $cWeek = array('空','周一','周二','周三','周四','周五','周六','周日');
		//阳历总天数 从1900年12月21日开始算起
		$total = self::passDay($year,$month,$day,1900,12,21);
		//农历经过天数
		$cTotal = 0;
		//用农历的天数累加来判断是否超过阳历的天数
		for($year=0;$year<=120;++$year) {
		    $month=1;
		    for($month=1;$month<=13;++$month)
		    {
		        $cTotal+=$cEveryMonth[$year][$month];
		        if ($cTotal >= $total) {
		            $bool = true;
		            break 2;
		        }
		    }
		}
		if($bool === 0) {trigger_error('暂时不支持2020年及以后的年份转换，请补充完整dateInfo,并赋值给 $cEveryMoth');exit;}
		//day处理
		$day = $cEveryMonth[$year][$month]-($cTotal-$total);
		$day = $cDay[$day];
		//week处理
		//1900 12 21 是周五,过去total天
        $week = ($total+5) % 7;
        $week = $cWeek[$week];
        //月份处理
        $runMonth = '';
        if ($cEveryMonth[$year][0] !== 0 ) {
        	if ($cEveryMonth[$year][0]+1 === $month) {
        		$runMonth = $cMonth[0];
        	} elseif ($month > $cEveryMonth[$year][0]) {
                --$month;
        	}
        }
        $month = $runMonth . $cMonth[$month].'月';
        //天干处理
        $tianganNum = ($year + 7) % 10 + 1;
        $tiangan = $cTianGan[$tianganNum];
        //地支处理
        $dizhiNum = $year % 12 + 1 ;
        $dizhi = $cDizhi[$dizhiNum];
        //生肖（对应地支）
        $shengxiao = $cShengXiao[$dizhiNum];
        // return 闰八月初二 庚子年 【鼠年】
        return $tiangan.$dizhi.'年'.' '.'['.$shengxiao.'年] '.$month.$day;
	}
	/**
	 * 两个时间差的天数
	 * @param  int    $year_a       年
	 * @param  int    $month_a      月
	 * @param  int    $day_a        日
	 * @param  int    $year_b  		年
	 * @param  int    $month_b 		月
	 * @param  int    $day_c   		日
	 * @return int    经过天数
	 */
	static public function passDay(int $year_a,int $month_a,int $day_a,int $year_b,int $month_b,int $day_b) : int {
		$time_a =strtotime($year_a.'-'.$month_a.'-'.$day_a);
		$time_b =strtotime($year_b.'-'.$month_b.'-'.$day_b);
		return round((abs($time_a-$time_b)) / 86400);
	}
	/**
	 * 格式化时间
	 */
	static public function formatDate(int $time,string $type = '') {
		switch ($type) {
			case 'zh-cn':
				$date = date('Y-m-d h:i',$time);
				break;
			
			default:
				$date = date('Y-m-d h:i',$time);
				break;
		}
		return $date;
	}
}