<?php declare(strict_types = 1);
namespace msqphp\test\main\template;

class TemplateTest extends \msqphp\test\Test
{
    public function testStart() : void
    {
        $this->class('\msqphp\main\template\Template')->method('commpile');
        // 配置对象
        $config = app()->config;
        // 得到当前模版配置
        $template_config = $config->get('template');
        // 设置测试值
        $config->set('template',['left_delimiter'=>'<{','right_delimiter'=>'}>',]);
        // 测试
        $this->testThis();
        // 还原配置值
        $config->set('template', $template_config);
    }
    public function testParVar() : void
    {
        $content = '<{$a}>';
        $vars = [];
        $language = [];
        $result = '<?php echo $a;?>';
        $this->args($content, $vars, $language)->result($result)->test();
    }
    public function testParVar1() : void
    {
        $content = '<{$a}>';
        $vars = ['a'=>['cache'=>true, 'value'=>'a']];
        $language = [];
        $result = 'a';
        $this->args($content, $vars, $language)->result($result)->test();
    }
    public function testParVar2() : void
    {
        $content = '<{$a}>';
        $vars = ['a'=>['cache'=>false, 'value'=>'a']];
        $language = [];
        $result = '<?php echo $a;?>';
        $this->args($content, $vars, $language)->result($result)->test();
    }
    public function testParArr() : void
    {
        $content = '<{$a[\'nihao\']}>';
        $vars = [];
        $language = [];
        $result = '<?php echo $a[\'nihao\'];?>';
        $this->args($content, $vars, $language)->result($result)->test();
    }
    public function testParArr2() : void
    {
        $content = '<{$a[\'nihao\']}>';
        $vars = ['a'=>['cache'=>false, 'value'=>['nihao'=>'nihao']]];
        $language = [];
        $result = '<?php echo $a[\'nihao\'];?>';
        $this->args($content, $vars, $language)->result($result)->test();
    }
    public function testParArr3() : void
    {
        $content = '<{$a[\'nihao\']}>';
        $vars = ['a'=>['cache'=>true, 'value'=>['nihao'=>'nihao']]];
        $language = [];
        $result = 'nihao';
        $this->args($content, $vars, $language)->result($result)->test();
    }
    public function testParArr4() : void
    {
        $content = '<{$a[1][2][3][4][5][6][7][8]}>';
        $vars = ['a'=>['cache'=>true, 'value'=>[1=>[2=>[3=>[4=>[5=>[6=>[7=>[8=>'nihao']]]]]]]]]];
        $language = [];
        $result = 'nihao';
        $this->args($content, $vars, $language)->result($result)->test();
    }
    public function testParArr5() : void
    {
        $content = '<{$a.1.2.3.4.5.6.7.8}>';
        $vars = ['a'=>['cache'=>true, 'value'=>[1=>[2=>[3=>[4=>[5=>[6=>[7=>[8=>'nihao']]]]]]]]]];
        $language = [];
        $result = 'nihao';
        $this->args($content, $vars, $language)->result($result)->test();
    }
    public function testParArr6() : void
    {
        $content = '<{$a.nihao}>';
        $vars = ['a'=>['cache'=>false, 'value'=>['nihao'=>'nihao']]];
        $language = [];
        $result = '<?php echo $a[\'nihao\'];?>';
        $this->args($content, $vars, $language)->result($result)->test();
    }
    public function testParFunc() : void
    {
        $content = '<{substr($a, 0, 2)}>';
        $vars = ['a'=>['cache'=>true, 'value'=>'test']];
        $language = [];
        $result = 'te';
        $this->args($content, $vars, $language)->result($result)->test();
    }
    public function testParFunc1() : void
    {
        $content = '<{substr($a, 0, 2)}>';
        $vars = ['a'=>['cache'=>false, 'value'=>'test']];
        $language = [];
        $result = '<?php echo (string) substr($a,0,2);?>';
        $this->args($content, $vars, $language)->result($result)->test();
    }
    public function testParFunc2() : void
    {
        $content = '<{php_sapi_name()}>';
        $vars = ['a'=>['cache'=>false, 'value'=>'test']];
        $language = [];
        $result = '<?php echo (string) php_sapi_name();?>';
        $this->args($content, $vars, $language)->result($result)->test();
    }
    public function testParForeach() : void
    {
        $content = '<{foreach $arr as $v}><{$v}><{endforeach}>';
        $vars = ['arr'=>['cache'=>true, 'value'=>[1, 2, 3, 4, 5, 6, 7, 8, 9, 0]]];
        $language = [];
        $result = '1234567890';
        $this->args($content, $vars, $language)
             ->result($result)
             ->test();
    }
    public function testParForeach1() : void
    {
        $content = '<{foreach $arr as $k => $v}><{$k}><{endforeach}>';
        $vars = ['arr'=>['cache'=>true, 'value'=>[1, 2, 3, 4, 5, 6, 7, 8, 9, 0]]];
        $language = [];
        $result = '0123456789';
        $this->args($content, $vars, $language)->result($result)->test();
    }
    public function testParForeach2() : void
    {
        $content = '<{foreach $arr as $key => $value}><{$key}>:<{foreach $value as $v}><{$v}><{endforeach}><{endforeach}>';
        $vars = [
            'arr'=>[
                'cache'=>true,
                'value'=>[
                    'a'=>['A','B','C'],
                    'b'=>['D','E','F']
                ]
            ]
        ];
        $language = [];
        $result = 'a:ABCb:DEF';
        $this->args($content, $vars, $language)->result($result)->test();
    }
    public function testIf() : void
    {
        $content = '<{if $a === \'a\'}><{$a}><{endif}>';
        $vars = ['a'=>['value'=>'a','cache'=>true]];
        $language = [];
        $result = 'a';
        $this->args($content, $vars, $language)->result($result)->test();
    }
    public function testIf2() : void
    {
        $content = '<{if $a === \'a\'}>nihao<{endif}>';
        $vars = ['a'=>['value'=>'a','cache'=>true]];
        $language = [];
        $result = 'nihao';
        $this->args($content, $vars, $language)->result($result)->test();
    }
    public function testIf3() : void
    {
        $content = '<{if $a === \'a\'}><{$a}><{endif}>';
        $vars = ['a'=>['value'=>'a','cache'=>false]];
        $language = [];
        $result = '<?php if($a===\'a\') : ?><?php echo $a;?><?php endif;?>';
        $this->args($content, $vars, $language)->result($result)->test();
    }
    public function testIf4() : void
    {
        $content = '<{if isset($a)}><{$a}><{endif}>';
        $vars = ['a'=>['value'=>'a','cache'=>false]];
        $language = [];
        $result = '<?php if(isset($a)) : ?><?php echo $a;?><?php endif;?>';
        $this->args($content, $vars, $language)->result($result)->test();
    }
}