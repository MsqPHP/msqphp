<?php declare(strict_types = 1);
namespace msqphp\core\cron;

use msqphp\base;

trait CronMethodTrait
{
    /**
     * @param array  $info = [
     *   'name'    => (string)
     *   'value'   => (string|??)
     *   'type'    => (string)
     *   'time'    => (int)
     * ]
     *
     * @throws CronException
     * @return void
     */

    private static function runMethod(array $info) : void
    {
        switch ($info['type']) {
            case 'deleteFile':
                static::deleteFile($info);
                break;
            case 'clearCache':
                static::clearCache();
                break;
            case 'clearView':
                static::clearView();
                break;
            default:
                throw new CronException($info[0]['type'].'未知的事件code码');
        }
    }
    // 删除文件
    private static function deleteFile(array $info) : void
    {
        try {
            base\file\File::delete($info['value'], true);
        } catch (base\file\FileException $e) {
            throw new CronException('定时任务执行失败,文件'.$info['value'].'无法删除,原因:'.$e->getMessage());
        }

        static::recordLog(date('Y-m-d H:i:s')."\t".'[任务名:]'.$info['name']."\t\t".'[值:]删除文件:'.$info['value']);
    }
}