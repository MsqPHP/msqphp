<?php declare(strict_types = 1);
namespace msqphp\core\cron;

final class Queue
{
    private $_queue = array();
    protected $cache = null;
    protected $queuecachename;

    /**
    * 构造方法
    * @param string $queuename 队列名称
    */
    function __construct($queuename)
    {
        $this->cache = Cache::instance();
        $this->queuecachename = 'queue_' . $queuename;

        $result = $this->cache->get($this->queuecachename);
        if (is_array($result)) {
        $this->_queue = $result;
        }
    }

    /**
    * 将一个单元单元放入队列末尾
    * @param mixed $value
    */
    function enQueue($value)
    {
        $this->_queue[] = $value;
        $this->cache->set($this->queuecachename, $this->_queue);

        return $this;
    }

    /**
    * 将队列开头的一个或多个单元移出
    * @param int $num
    */
    function sliceQueue($num = 1)
    {
        if (count($this->_queue) < $num) {
        $num = count($this->_queue);
        }
        $output = array_splice($this->_queue, 0, $num);
        $this->cache->set($this->queuecachename, $this->_queue);

        return $output;
    }

    /**
    * 将队列开头的单元移出队列
    */
    function deQueue()
    {
        $entry = array_shift($this->_queue);
        $this->cache->set($this->queuecachename, $this->_queue);
        return $entry;
    }

    /**
    * 返回队列长度
    */
    function size()
    {
        return count($this->_queue);
    }

    /**
    * 返回队列中的第一个单元
    */
    function peek()
    {
        return $this->_queue[0];
    }

    /**
    * 返回队列中的一个或多个单元
    * @param int $num
    */
    function peeks($num)
    {
        if (count($this->_queue) < $num) {
        $num = count($this->_queue);
        }
        return array_slice($this->_queue, 0, $num);
    }

    /**
    * 消毁队列
    */
    function destroy()
    {
        $this->cache->remove($this->queuecachename);
    }
}