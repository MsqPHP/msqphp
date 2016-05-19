<?php declare(strict_types = 1);
namespace msqphp\test\core\template;

class TemplateTest extends \msqphp\test\Test
{
    public function testStart()
    {
        $this->class('\msqphp\core\template\Template');
        $this->method('commpile');
        \msqphp\core\config\Config::set('template',['left_delim'=>'<{','right_delim'=>'}>',]);
        $this->testThis();
    }
    public function testParVar()
    {
        $content = '<{$a}>';
        $vars = [];
        $language = [];
        $result = '<?php echo $a;?>';
        $this->args($content, $vars, $language)->result($result)->test();
    }
    public function testParVar1()
    {
        $content = '<{$a}>';
        $vars = ['a'=>['cache'=>true, 'value'=>'a']];
        $language = [];
        $result = 'a';
        $this->args($content, $vars, $language)->result($result)->test();
    }
    public function testParVar2()
    {
        $content = '<{$a}>';
        $vars = ['a'=>['cache'=>false, 'value'=>'a']];
        $language = [];
        $result = '<?php echo $a;?>';
        $this->args($content, $vars, $language)->result($result)->test();
    }
    public function testParArr()
    {
        $content = '<{$a[\'nihao\']}>';
        $vars = [];
        $language = [];
        $result = '<?php echo $a[\'nihao\'];?>';
        $this->args($content, $vars, $language)->result($result)->test();
    }
    public function testParArr2()
    {
        $content = '<{$a[\'nihao\']}>';
        $vars = ['a'=>['cache'=>false, 'value'=>['nihao'=>'nihao']]];
        $language = [];
        $result = '<?php echo $a[\'nihao\'];?>';
        $this->args($content, $vars, $language)->result($result)->test();
    }
    public function testParArr3()
    {
        $content = '<{$a[\'nihao\']}>';
        $vars = ['a'=>['cache'=>true, 'value'=>['nihao'=>'nihao']]];
        $language = [];
        $result = 'nihao';
        $this->args($content, $vars, $language)->result($result)->test();
    }
    public function testParArr4()
    {
        $content = '<{$a[1][2][3][4][5][6][7][8]}>';
        $vars = ['a'=>['cache'=>true, 'value'=>[1=>[2=>[3=>[4=>[5=>[6=>[7=>[8=>'nihao']]]]]]]]]];
        $language = [];
        $result = 'nihao';
        $this->args($content, $vars, $language)->result($result)->test();
    }
    public function testParForeach()
    {
        $content = '<{foreach $arr as $v}><{$v}><{endforeach}>';
        $vars = ['arr'=>['cache'=>true, 'value'=>[1, 2, 3, 4, 5, 6, 7, 8, 9, 0]]];
        $language = [];
        $result = '1234567890';
        $this->args($content, $vars, $language)->result($result)->test();
    }
    public function testParForeach1()
    {
        $content = '<{foreach $arr as $k => $v}><{$k}><{endforeach}>';
        $vars = ['arr'=>['cache'=>true, 'value'=>[1, 2, 3, 4, 5, 6, 7, 8, 9, 0]]];
        $language = [];
        $result = '0123456789';
        $this->args($content, $vars, $language)->result($result)->test();
    }
}