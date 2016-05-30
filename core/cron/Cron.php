<?php declare(strict_types = 1);
namespace msqphp\core\cron;

use msqphp\base;
use msqphp\traits;

final class Cron
{
    use traits\Instance;

    private $path = '';
    private $file_path = '';
    private $cron = [];
    private $changed = false;
    const DELETE_FILE = 1;

    private function __construct()
    {
        $this->path = \msqphp\Environment::getPath('storage').'framework'.DIRECTORY_SEPARATOR.'cron'.DIRECTORY_SEPARATOR;
        $this->file_path = $this->path.'cron.php';
        $this->load();
        $this->run();
        $this->writeLog(date('Y-m-d H:i:s').'运行过一次定时任务');
    }
    public function exists(string $name) : bool
    {
        foreach ($this->cron as $value) {
            if ($name === $value['name']){
                return true;
            }
        }
        return false;
    }
    public function get(string $name) : array
    {
        foreach ($this->cron as $key => $value) {
            if ($name === $value['name']) {
                return ['key'=>$key,'value'=>$value];
            }
        }
        throw new CronException($name.'任务不存在');
    }
    public function add(string $name, $value, int $time)
    {
        if ($this->exists($name)) {
            throw new CronException($name.'任务已存在');
        }
        if (is_string($value)) {
            $this->cron[] = ['name'=>$name,'value'=>$value,'time'=>$time];
        } elseif(is_array($value)) {
            $this->cron[] = ['name'=>$name,'value'=>['code'=>$value[0],'value'=>$value[1]],'time'=>$time];
        } else {
            throw new CronException(var_export($value).'未知的事件');
        }
        $this->changed = true;
    }
    public function update(string $name, $value = null, int $time = 0)
    {
        foreach ($this->cron as $key => $event) {
            if ($name === $event['name']) {
                $old = $this->cron[$key];
                $value = is_null($value) ? $old['value'] : $value;
                $time  = $time === 0 ? $old['time'] : $time;
                if (is_string($value)) {
                    $this->cron[$key] = ['name'=>$name,'value'=>$value,'time'=>$time];
                } elseif(is_array($value)) {
                    $this->cron[$key] = ['name'=>$name,'value'=>['code'=>$value[0],'value'=>$value[1]],'time'=>$time];
                } else {
                    throw new CronException(var_export($value).'未知的事件');
                }
            }
        }
        $this->changed = true;
    }
    public function set(string $name, $value, int $time)
    {
        if ($this->exists($name)) {
            $this->update($name, $value, $time);
        } else {
            $this->add($name, $value, $time);
        }
        $this->changed = true;
    }
    public function writeLog(string $content)
    {
        $log = $this->path.'cron.log';
        base\file\File::append($log, $content.PHP_EOL, true);
    }
    public function run()
    {
        $now = time();
        $cron = $this->cron;
        while(isset($cron[0]) && $now > $cron[0]['time']) {
            $event = $cron[0];
            $log_content = date('Y-m-d H:i:s')."\t".'[任务名:]'.$event['name']."\t\t".'[值:]';
            if (is_string($event['value'])) {
                eval($event['value']);
                $log_content .= $event['value'];
            } elseif(is_array($event['value'])) {
                switch ($event['value']['code']) {
                    case static::DELETE_FILE:
                        base\file\File::delete($event['value']['value'], true);
                        $log_content .= '删除文件:'.$event['value']['value'];
                        break;
                    default:
                        throw new CronException($event['value']['code'].'未知的事件code码');
                }
            } else {
                throw new CronException(var_export($value, true).'不正确的定时任务');
            }
            $this->writeLog($log_content);
            $this->changed = true;
            array_shift($cron);
        }
        $this->cron = $cron;
    }
    public function load()
    {
        $this->cron = is_file($this->file_path) ? unserialize(base\file\File::get($this->file_path)) : [];
    }
    public function save()
    {
        if ($this->changed) {
            $this->sort();
            base\file\File::write($this->file_path, serialize($this->cron), true);
        }
    }
    public function sort()
    {
        $cron = $this->cron;
        for ($i = 0, $l = count($cron); $i < $l; ++$i) {
            for ($j = $i; $j < $l; ++ $j) {
                if ($cron[$j]['time'] < $cron[$i]['time']) {
                    list($cron[$j], $cron[$i]) = [$cron[$i], $cron[$j]];
                }
            }
        }
        $this->cron = $cron;
    }
    public function file()
    {

    }
    public function cli()
    {

    }
    public function user()
    {

    }
    public function __destruct()
    {
        $this->save();
    }
}