<?php declare(strict_types = 1);
namespace Core\Test\Base\Str;
class StrTest extends \Core\Test\Test
{
    public function testStart()
    {
        $this->testThis();
    }
    public function testRandomString()
    {
        $len = rand(0,1000);
        $rand = rand(1,7);
        $preg = array(
            '',
            '/^[0-9]{'.$len.'}$/',
            '/^[0-9a-z]{'.$len.'}$/',
            '/^[0-9a-zA-Z]{'.$len.'}$/',
            '/^[0-9a-zA-Z\~\`\!\@\#\$\%\^\&\*\(\)\_\+\-\=\[\]\{\}\:\|\<\>\?\,\.\/\\\\\;\'\"]{'.$len.'}$/',
            '/^[a-zA-Z]{'.$len.'}$/',
            '/^[a-zA-Z\-\_]{'.$len.'}$/',
            '/^[a-zA-Z\~\`\!\@\#\$\%\^\&\*\(\)\_\+\-\=\[\]\{\}\:\|\<\>\?\,\.\/\;\'\"\\\\]{'.$len.'}$/',
        );
        $this->testStaticMethod('\Core\Base\Str\Str::randomString',array($len,$rand),function($result) use ($preg,$rand) {
            return true && preg_match($preg[$rand],$result);
        });
        return true;
    }
    public function testRandomBytes()
    {
        $len = rand(0,1000);
        $preg = '/^[0-9a-z]{'.$len.'}$/';
        $this->testStaticMethod('\Core\Base\Str\Str::randomBytes',array($len),function($result) use ($len) {
            return strlen($result) === $len;
        });
        return true;
    }
    public function testRandom()
    {
        $len = rand(0,1000);
        $preg = '/^[0-9a-zA-Z]{'.$len.'}$/';
        $this->testStaticMethod('\Core\Base\Str\Str::random',array($len),function($result) use ($preg) {
            return true && preg_match($preg,$result);
        });
        return true;
    }
    public function testQuickRandom()
    {
        $len = rand(0,1000);
        $preg = '/^[0-9a-zA-Z]{'.$len.'}$/';
        $this->testStaticMethod('\Core\Base\Str\Str::quickRandom',array($len),function($result) use ($preg) {
            return true && preg_match($preg,$result);
        });
        return true;
    }
    public function testContains()
    {
        $string = 'hello world this is a test';
        $this->testStaticMethod('\Core\Base\Str\Str::contains',array($string,'hello'),true);
        $this->testStaticMethod('\Core\Base\Str\Str::contains',array($string,array('nihao','is')),true);
        $this->testStaticMethod('\Core\Base\Str\Str::contains',array($string,'buhao'),false);
        $this->testStaticMethod('\Core\Base\Str\Str::contains',array($string,'a'),true);
        $this->testStaticMethod('\Core\Base\Str\Str::contains',array($string,'test'),true);

        return true;
    }
    public function testStartsWith()
    {
        $string = 'test 1';
        $this->testStaticMethod('\Core\Base\Str\Str::startsWith',array($string,'test'),true);
        $string = 'test 1';
        $this->testStaticMethod('\Core\Base\Str\Str::startsWith',array($string,'atest'),false);
        $string = 'nna';
        $this->testStaticMethod('\Core\Base\Str\Str::startsWith',array($string,array('a','nna')),true);
        return true;
    }
    public function testEndsWith()
    {
        $string = 'test 1';
        $this->testStaticMethod('\Core\Base\Str\Str::endsWith',array($string,'1'),true);
        $string = 'test 1';
        $this->testStaticMethod('\Core\Base\Str\Str::endsWith',array($string,'0'),false);
        $string = 'nna';
        $this->testStaticMethod('\Core\Base\Str\Str::endsWith',array($string,array('a','nna')),true);
        return true;
    }
    public function testGetSize()
    {
        $this->testStaticMethod('\Core\Base\Str\Str::getSize',array(0,true),'0 Bytes');
        $this->testStaticMethod('\Core\Base\Str\Str::getSize',array(1024,true),'1 KB');
        $this->testStaticMethod('\Core\Base\Str\Str::getSize',array(0.2,false),'0.2 Bytes');
        return true;
    }
}
