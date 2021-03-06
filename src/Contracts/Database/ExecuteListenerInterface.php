<?php

namespace Cool\Contracts\Database;

/**
 * Interface ExecuteListenerInterface
 * @package Cool\Contracts\Database
 */
interface ExecuteListenerInterface
{

    /**
     * 监听
     * @param array $data
     */
    public function listen($data);

}
