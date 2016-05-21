<?php declare(strict_types = 1);
namespace msqphp\base\date;

use msqphp\base;

class Date
{
    use base\Base;
    /**
     * 年月日合法检测
     * @param  int    $year  年
     * @param  int    $month 月
     * @param  int    $day   日
     * @return bool     是否合法
     */
    public static function checkDate(int $year, int $month, int $day) : bool
    {
        return checkdate($month, $day, $year);
    }
    /**
     * 阳历转阴历
     * @param  int    $year  年
     * @param  int    $month 月
     * @param  int    $day   日
     */
    public static function toChineseTraditionalCalendar(int $year, int $month, int $day)
    {
        static::checkDate($year, $month, $day) || exit('日期非法');
        //农历每月的天数
        //1900 12 21开始  天干7 地支1
        //1900 12 21 十月三十 庚子年 【鼠年】戊子月 戊辰日
        static $cEveryMonth=[0=>[8, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 29, 30], 1=>[0, 29, 30, 29, 29, 30, 29, 30, 29, 30, 30, 30, 29, 0], 2=>[0, 30, 29, 30, 29, 29, 30, 29, 30, 29, 30, 30, 30, 0], 3=>[5, 29, 30, 29, 30, 29, 29, 30, 29, 29, 30, 30, 29, 30], 4=>[0, 30, 30, 29, 30, 29, 29, 30, 29, 29, 30, 30, 29, 0], 5=>[0, 30, 30, 29, 30, 30, 29, 29, 30, 29, 30, 29, 30, 0], 6=>[4, 29, 30, 30, 29, 30, 29, 30, 29, 30, 29, 30, 29, 30], 7=>[0, 29, 30, 29, 30, 29, 30, 30, 29, 30, 29, 30, 29, 0], 8=>[0, 30, 29, 29, 30, 30, 29, 30, 29, 30, 30, 29, 30, 0], 9=>[2, 29, 30, 29, 29, 30, 29, 30, 29, 30, 30, 30, 29, 30], 10=>[0, 29, 30, 29, 29, 30, 29, 30, 29, 30, 30, 30, 29, 0], 11=>[6, 30, 29, 30, 29, 29, 30, 29, 29, 30, 30, 29, 30, 30], 12=>[0, 30, 29, 30, 29, 29, 30, 29, 29, 30, 30, 29, 30, 0], 13=>[0, 30, 30, 29, 30, 29, 29, 30, 29, 29, 30, 29, 30, 0], 14=>[5, 30, 30, 29, 30, 29, 30, 29, 30, 29, 30, 29, 29, 30], 15=>[0, 30, 29, 30, 30, 29, 30, 29, 30, 29, 30, 29, 30, 0], 16=>[0, 29, 30, 29, 30, 29, 30, 30, 29, 30, 29, 30, 29, 0], 17=>[2, 30, 29, 29, 30, 29, 30, 30, 29, 30, 30, 29, 30, 29], 18=>[0, 30, 29, 29, 30, 29, 30, 29, 30, 30, 29, 30, 30, 0], 19=>[7, 29, 30, 29, 29, 30, 29, 29, 30, 30, 29, 30, 30, 30], 20=>[0, 29, 30, 29, 29, 30, 29, 29, 30, 30, 29, 30, 30, 0], 21=>[0, 30, 29, 30, 29, 29, 30, 29, 29, 30, 29, 30, 30, 0], 22=>[5, 30, 29, 30, 30, 29, 29, 30, 29, 29, 30, 29, 30, 30], 23=>[0, 29, 30, 30, 29, 30, 29, 30, 29, 29, 30, 29, 30, 0], 24=>[0, 29, 30, 30, 29, 30, 30, 29, 30, 29, 30, 29, 29, 0], 25=>[4, 30, 29, 30, 29, 30, 30, 29, 30, 30, 29, 30, 29, 30], 26=>[0, 29, 29, 30, 29, 30, 29, 30, 30, 29, 30, 30, 29, 0], 27=>[0, 30, 29, 29, 30, 29, 30, 29, 30, 29, 30, 30, 30, 0], 28=>[2, 29, 30, 29, 29, 30, 29, 29, 30, 29, 30, 30, 30, 30], 29=>[0, 29, 30, 29, 29, 30, 29, 29, 30, 29, 30, 30, 30, 0], 30=>[6, 29, 30, 30, 29, 29, 30, 29, 29, 30, 29, 30, 30, 29], 31=>[0, 30, 30, 29, 30, 29, 30, 29, 29, 30, 29, 30, 29, 0], 32=>[0, 30, 30, 30, 29, 30, 29, 30, 29, 29, 30, 29, 30, 0], 33=>[5, 29, 30, 30, 29, 30, 30, 29, 30, 29, 30, 29, 29, 30], 34=>[0, 29, 30, 29, 30, 30, 29, 30, 29, 30, 30, 29, 30, 0], 35=>[0, 29, 29, 30, 29, 30, 29, 30, 30, 29, 30, 30, 29, 0], 36=>[3, 30, 29, 29, 30, 29, 29, 30, 30, 29, 30, 30, 30, 29], 37=>[0, 30, 29, 29, 30, 29, 29, 30, 29, 30, 30, 30, 29, 0], 38=>[7, 30, 30, 29, 29, 30, 29, 29, 30, 29, 30, 30, 29, 30], 39=>[0, 30, 30, 29, 29, 30, 29, 29, 30, 29, 30, 29, 30, 0], 40=>[0, 30, 30, 29, 30, 29, 30, 29, 29, 30, 29, 30, 29, 0], 41=>[6, 30, 30, 29, 30, 30, 29, 30, 29, 29, 30, 29, 30, 29], 42=>[0, 30, 29, 30, 30, 29, 30, 29, 30, 29, 30, 29, 30, 0], 43=>[0, 29, 30, 29, 30, 29, 30, 30, 29, 30, 29, 30, 29, 0], 44=>[4, 30, 29, 30, 29, 30, 29, 30, 29, 30, 30, 29, 30, 30], 45=>[0, 29, 29, 30, 29, 29, 30, 29, 30, 30, 30, 29, 30, 0], 46=>[0, 30, 29, 29, 30, 29, 29, 30, 29, 30, 30, 29, 30, 0], 47=>[2, 30, 30, 29, 29, 30, 29, 29, 30, 29, 30, 29, 30, 30], 48=>[0, 30, 29, 30, 29, 30, 29, 29, 30, 29, 30, 29, 30, 0], 49=>[7, 30, 29, 30, 30, 29, 30, 29, 29, 30, 29, 30, 29, 30], 50=>[0, 29, 30, 30, 29, 30, 30, 29, 29, 30, 29, 30, 29, 0], 51=>[0, 30, 29, 30, 30, 29, 30, 29, 30, 29, 30, 29, 30, 0], 52=>[5, 29, 30, 29, 30, 29, 30, 29, 30, 30, 29, 30, 29, 30], 53=>[0, 29, 30, 29, 29, 30, 30, 29, 30, 30, 29, 30, 29, 0], 54=>[0, 30, 29, 30, 29, 29, 30, 29, 30, 30, 29, 30, 30, 0], 55=>[3, 29, 30, 29, 30, 29, 29, 30, 29, 30, 29, 30, 30, 30], 56=>[0, 29, 30, 29, 30, 29, 29, 30, 29, 30, 29, 30, 30, 0], 57=>[8, 30, 29, 30, 29, 30, 29, 29, 30, 29, 30, 29, 30, 29], 58=>[0, 30, 30, 30, 29, 30, 29, 29, 30, 29, 30, 29, 30, 0], 59=>[0, 29, 30, 30, 29, 30, 29, 30, 29, 30, 29, 30, 29, 0], 60=>[6, 30, 29, 30, 29, 30, 30, 29, 30, 29, 30, 29, 30, 29], 61=>[0, 30, 29, 30, 29, 30, 29, 30, 30, 29, 30, 29, 30, 0], 62=>[0, 29, 30, 29, 29, 30, 29, 30, 30, 29, 30, 30, 29, 0], 63=>[4, 30, 29, 30, 29, 29, 30, 29, 30, 29, 30, 30, 30, 29], 64=>[0, 30, 29, 30, 29, 29, 30, 29, 30, 29, 30, 30, 30, 0], 65=>[0, 29, 30, 29, 30, 29, 29, 30, 29, 29, 30, 30, 29, 0], 66=>[3, 30, 30, 30, 29, 30, 29, 29, 30, 29, 29, 30, 30, 29], 67=>[0, 30, 30, 29, 30, 30, 29, 29, 30, 29, 30, 29, 30, 0], 68=>[7, 29, 30, 29, 30, 30, 29, 30, 29, 30, 29, 30, 29, 30], 69=>[0, 29, 30, 29, 30, 29, 30, 30, 29, 30, 29, 30, 29, 0], 70=>[0, 30, 29, 29, 30, 29, 30, 30, 29, 30, 30, 29, 30, 0], 71=>[5, 29, 30, 29, 29, 30, 29, 30, 29, 30, 30, 30, 29, 30], 72=>[0, 29, 30, 29, 29, 30, 29, 30, 29, 30, 30, 29, 30, 0], 73=>[0, 30, 29, 30, 29, 29, 30, 29, 29, 30, 30, 29, 30, 0], 74=>[4, 30, 30, 29, 30, 29, 29, 30, 29, 29, 30, 30, 29, 30], 75=>[0, 30, 30, 29, 30, 29, 29, 30, 29, 29, 30, 29, 30, 0], 76=>[8, 30, 30, 29, 30, 29, 30, 29, 30, 29, 29, 30, 29, 30], 77=>[0, 30, 29, 30, 30, 29, 30, 29, 30, 29, 30, 29, 29, 0], 78=>[0, 30, 29, 30, 30, 29, 30, 30, 29, 30, 29, 30, 29, 0], 79=>[6, 30, 29, 29, 30, 29, 30, 30, 29, 30, 30, 29, 30, 29], 80=>[0, 30, 29, 29, 30, 29, 30, 29, 30, 30, 29, 30, 30, 0], 81=>[0, 29, 30, 29, 29, 30, 29, 29, 30, 30, 29, 30, 30, 0], 82=>[4, 30, 29, 30, 29, 29, 30, 29, 29, 30, 29, 30, 30, 30], 83=>[0, 30, 29, 30, 29, 29, 30, 29, 29, 30, 29, 30, 30, 0], 84=>[10, 30, 29, 30, 30, 29, 29, 30, 29, 29, 30, 29, 30, 30], 85=>[0, 29, 30, 30, 29, 30, 29, 30, 29, 29, 30, 29, 30, 0], 86=>[0, 29, 30, 30, 29, 30, 30, 29, 30, 29, 30, 29, 29, 0], 87=>[6, 30, 29, 30, 29, 30, 30, 29, 30, 30, 29, 30, 29, 29], 88=>[0, 30, 29, 30, 29, 30, 29, 30, 30, 29, 30, 30, 29, 0], 89=>[0, 30, 29, 29, 30, 29, 29, 30, 30, 29, 30, 30, 30, 0], 90=>[5, 29, 30, 29, 29, 30, 29, 29, 30, 29, 30, 30, 30, 30], 91=>[0, 29, 30, 29, 29, 30, 29, 29, 30, 29, 30, 30, 30, 0], 92=>[0, 29, 30, 30, 29, 29, 30, 29, 29, 30, 29, 30, 30, 0], 93=>[3, 29, 30, 30, 29, 30, 29, 30, 29, 29, 30, 29, 30, 29], 94=>[0, 30, 30, 30, 29, 30, 29, 30, 29, 29, 30, 29, 30, 0], 95=>[8, 29, 30, 30, 29, 30, 29, 30, 30, 29, 29, 30, 29, 30], 96=>[0, 29, 30, 29, 30, 30, 29, 30, 29, 30, 30, 29, 29, 0], 97=>[0, 30, 29, 30, 29, 30, 29, 30, 30, 29, 30, 30, 29, 0], 98=>[5, 30, 29, 29, 30, 29, 29, 30, 30, 29, 30, 30, 29, 30], 99=>[0, 30, 29, 29, 30, 29, 29, 30, 29, 30, 30, 30, 29, 0], 100=>[0, 30, 30, 29, 29, 30, 29, 29, 30, 29, 30, 30, 29, 0], 101=>[4, 30, 30, 29, 30, 29, 30, 29, 29, 30, 29, 30, 29, 30], 102=>[0, 30, 30, 29, 30, 29, 30, 29, 29, 30, 29, 30, 29, 0], 103=>[0, 30, 30, 29, 30, 30, 29, 30, 29, 29, 30, 29, 30, 0], 104=>[2, 29, 30, 29, 30, 30, 29, 30, 29, 30, 29, 30, 29, 30], 105=>[0, 29, 30, 29, 30, 29, 30, 30, 29, 30, 29, 30, 29, 0], 106=>[7, 30, 29, 30, 29, 30, 29, 30, 29, 30, 30, 29, 30, 30], 107=>[0, 29, 29, 30, 29, 29, 30, 29, 30, 30, 30, 29, 30, 0], 108=>[0, 30, 29, 29, 30, 29, 29, 30, 29, 30, 30, 29, 30, 0], 109=>[5, 30, 30, 29, 29, 30, 29, 29, 30, 29, 30, 29, 30, 30], 110=>[0, 30, 29, 30, 29, 30, 29, 29, 30, 29, 30, 29, 30, 0], 111=>[0, 30, 29, 30, 30, 29, 30, 29, 29, 30, 29, 30, 29, 0], 112=>[4, 30, 29, 30, 30, 29, 30, 29, 30, 29, 30, 29, 30, 29], 113=>[0, 30, 29, 30, 29, 30, 30, 29, 30, 29, 30, 29, 30, 0], 114=>[9, 29, 30, 29, 30, 29, 30, 29, 30, 30, 29, 30, 29, 30], 115=>[0, 29, 30, 29, 29, 30, 29, 30, 30, 30, 29, 30, 29, 0], 116=>[0, 30, 29, 30, 29, 29, 30, 29, 30, 30, 29, 30, 30, 0], 117=>[6, 29, 30, 29, 30, 29, 29, 30, 29, 30, 29, 30, 30, 30], 118=>[0, 29, 30, 29, 30, 29, 29, 30, 29, 30, 29, 30, 30, 0], 119=>[0, 30, 29, 30, 29, 30, 29, 29, 30, 29, 29, 30, 30, 0], 120=>[4, 29, 30, 30, 30, 29, 30, 29, 29, 30, 29, 30, 29, 30]];
        //农历天干
        static $cTianGan = ['天干', '甲', '乙', '丙', '丁', '戊', '己', '庚', '辛', '壬', '癸'];
        //农历地支
        static $cDizhi = ['地支', '子', '丑', '寅', '卯', '辰', '巳', '午', '未', '申', '酉', '戌', '亥'];
        //十二生肖
        static $cShengXiao = ['年', '鼠', '牛', '虎', '兔', '龙', '蛇', '马', '羊', '猴', '鸡', '狗', '猪'];
        //农历月
        static $cMonth=['闰', '正', '二', '三', '四', '五', '六', '七', '八', '九', '十', '十一', '十二', '月'];
        //农历日
        static $cDay=['空', '初一', '初二', '初三', '初四', '初五', '初六', '初七', '初八', '初九', '初十', '十一', '十二', '十三', '十四', '十五', '十六', '十七', '十八', '十九', '二十', '廿一', '廿二', '廿三', '廿四', '廿五', '廿六', '廿七', '廿八', '廿九', '三十'];
        //周几
        static $cWeek = ['空', '周一', '周二', '周三', '周四', '周五', '周六', '周日'];
        //阳历总天数 从1900年12月21日开始算起
        $total = static::passDay($year, $month, $day, 1900, 12, 21);
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
        if ($bool === 0) {
            throw new DateException('暂时不支持2020年及以后的年份转换，请补充完整dateInfo, 并赋值给 $cEveryMoth');
        }
        //day处理
        $day = $cEveryMonth[$year][$month]-($cTotal-$total);
        $day = $cDay[$day];
        //week处理
        //1900 12 21 是周五, 过去total天
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
     * @param  int    $year_b       年
     * @param  int    $month_b      月
     * @param  int    $day_c        日
     * @return int    经过天数
     */
    public static function passDay(int $year_a, int $month_a, int $day_a, int $year_b, int $month_b, int $day_b) : int
    {
        $time_a =strtotime($year_a.'-'.$month_a.'-'.$day_a);
        $time_b =strtotime($year_b.'-'.$month_b.'-'.$day_b);
        return round((abs($time_a-$time_b)) / 86400);
    }
    /**
     * 格式化时间
     */
    public static function format(int $time, string $type = '')
    {
        switch ($type) {
            case 'zh-cn':
                return date('Y-m-d h:i', $time);
            case 'en-us':
                return date('Y-d-m h:i', $time);
            case 'en-uk':
                return date('d-m-Y h:i', $time);
            default:
                return date('Y-m-d h:i', $time);
        }
    }
    public static function today() : int
    {
        return strtotime(date('Y-m-d 00:00:00'));
    }
    public static function before(int $time) : string
    {
        $today = static::today();
        if ($time > $today) {
            $limit = time() - $time;
            if ($limit < 60 ) {
                $result = '刚刚';
            } elseif ($limit < 3600) {
                $result = floor($limit / 60) . ' 分钟前';
            } elseif ($limit < 86400) {
                $result = floor($limit / 3600) . ' 小时前';
            }
        } else {
            if ($time > ($today - 86400)) {
                $result = ' 昨天 ' . date('H:i', $time);
            } elseif ($time > ($today - 172800)) {
                $result = ' 前天 ' . date('H:i', $time);
            } else {
                $result = date('Y-m-d', $time);
            }
        }
        return $result;

    }
}