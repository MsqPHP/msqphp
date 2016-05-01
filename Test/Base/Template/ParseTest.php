<?php declare(strict_types = 1);
namespace Core\Test\Base\Template;

class ParseTest extends \Core\Test\Test
{
    public function testStart()
    {
        $this->testThis();
    }
    public function testParVar()
    {
        $content = '<{$a}>';
        $vars = array();
        $language = array();
        $config = array();
        $result = '<?php echo $a;?>';
        return $this->testStaticMethod('\Core\Base\Template\Parse::commpile',array($content,$vars,$language,$config),$result);
    }
    public function testParVar1()
    {
        $content = '<{$a}>';
        $vars = array('a'=>array('cache'=>true,'value'=>'a'));
        $language = array();
        $config = array();
        $result = 'a';
        return $this->testStaticMethod('\Core\Base\Template\Parse::commpile',array($content,$vars,$language,$config),$result);
    }
    public function testParVar2()
    {
        $content = '<{$a}>';
        $vars = array('a'=>array('cache'=>false,'value'=>'a'));
        $language = array();
        $config = array();
        $result = '<?php echo $a;?>';
        return $this->testStaticMethod('\Core\Base\Template\Parse::commpile',array($content,$vars,$language,$config),$result);
    }
    public function testParArr()
    {
        $content = '<{$a[\'nihao\']}>';
        $vars = array();
        $language = array();
        $config = array();
        $result = '<?php echo $a[\'nihao\'];?>';
        return $this->testStaticMethod('\Core\Base\Template\Parse::commpile',array($content,$vars,$language,$config),$result);
    }
    public function testParArr2()
    {
        $content = '<{$a[\'nihao\']}>';
        $vars = array('a'=>array('cache'=>false,'value'=>array('nihao'=>'nihao')));
        $language = array();
        $config = array();
        $result = '<?php echo $a[\'nihao\'];?>';
        return $this->testStaticMethod('\Core\Base\Template\Parse::commpile',array($content,$vars,$language,$config),$result);
    }
    public function testParArr3()
    {
        $content = '<{$a[\'nihao\']}>';
        $vars = array('a'=>array('cache'=>true,'value'=>array('nihao'=>'nihao')));
        $language = array();
        $config = array();
        $result = 'nihao';
        return $this->testStaticMethod('\Core\Base\Template\Parse::commpile',array($content,$vars,$language,$config),$result);
    }
    public function testParArr4()
    {
        $content = '<{$a[1][2][3][4][5][6][7][8]}>';
        $vars = array('a'=>array('cache'=>true,'value'=>array(1=>array(2=>array(3=>array(4=>array(5=>array(6=>array(7=>array(8=>'nihao'))))))))));
        $language = array();
        $config = array();
        $result = 'nihao';
        return $this->testStaticMethod('\Core\Base\Template\Parse::commpile',array($content,$vars,$language,$config),$result);
    }
    public function testParForeach()
    {
        $content = '<{foreach $arr as $v}><{$v}><{endforeach}>';
        $vars = array('arr'=>array('cache'=>true,'value'=>array(1,2,3,4,5,6,7,8,9,0)));
        $language = array();
        $config = array();
        $result = '1234567890';
        return $this->testStaticMethod('\Core\Base\Template\Parse::commpile',array($content,$vars,$language,$config),$result);
    }
    public function testParForeach1()
    {
        $content = '<{foreach $arr as $k => $v}><{$k}><{endforeach}>';
        $vars = array('arr'=>array('cache'=>true,'value'=>array(1,2,3,4,5,6,7,8,9,0)));
        $language = array();
        $config = array();
        $result = '0123456789';
        return $this->testStaticMethod('\Core\Base\Template\Parse::commpile',array($content,$vars,$language,$config),$result);
    }
}