<?php
/**
 * Created by PhpStorm.
 * User: WytheHuang
 * Date: 2018/12/28
 * Time: 22:46.
 */
declare(strict_types=1);

/*
 * This file is part of the uuk020/logistics.
 *
 * (c) WytheHuang<wythe.huangw@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Wythe\Logistics;

use Wythe\Logistics\Exceptions\InvalidArgumentException;
use Wythe\Logistics\Exceptions\Exception;
use Wythe\Logistics\Channel\Channel;

class Factory
{
    private $defaultChannel = 'kuaiDiBird';

    protected $channels = [];

    /**
     * 获取默认查询类名称.
     *
     * @throws \Wythe\Logistics\Exceptions\Exception
     */
    public function getDefault(): string
    {
        if (empty($this->defaultChannel)) {
            throw new Exception('No default query class name configured.');
        }

        return $this->defaultChannel;
    }

    /**
     * 设置默认查询类名称.
     */
    public function setDefault($name)
    {
        $this->defaultChannel = $name;
    }

    /**
     * 数组元素存储查询对象
     *
     * @return mixed
     *
     * @throws \Wythe\Logistics\Exceptions\InvalidArgumentException
     */
    public function createChannel(string $name = '')
    {
        $name = $name ?: $this->defaultChannel;
        if (!isset($this->channels[$name])) {
            $className = $this->formatClassName($name);
            if (!class_exists($className)) {
                throw new InvalidArgumentException(sprintf('Class "%s" not exists.', $className));
            }
            $instance = new $className();
            if (!($instance instanceof Channel)) {
                throw new InvalidArgumentException(sprintf('Class "%s" not inherited from %s.', $name, Channel::class));
            }
            $this->channels[$name] = $instance;
        }

        return $this->channels[$name];
    }

    /**
     * 格式化类的名称.
     */
    protected function formatClassName(string $name): string
    {
        if (class_exists($name)) {
            return $name;
        }
        $name = ucfirst(str_replace(['-', '_', ' '], '', $name));

        return __NAMESPACE__."\\Channel\\{$name}Channel";
    }
}
