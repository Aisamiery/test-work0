<?php
/**
 * Created by PhpStorm.
 * Author: Shabalin Pavel
 * Email: aisamiery@gmail.com
 * Date: 24.02.2020
 */
declare(strict_types=1);

namespace Afonay\Ripe;


class Response
{
    protected $data;

    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    public function getObject()
    {
        return $this->data['objects']['object'] ?? [];
    }

    /**
     * @param array $data
     */
    public function setData(array $data): void
    {
        $this->data = $data;
    }
}