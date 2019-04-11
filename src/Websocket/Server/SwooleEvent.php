<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2019/4/5
 * Time: 16:05
 */

namespace Cool\Websocket\Server;


class SwooleEvent
{
    /**
     * Start
     */
    const START = 'start';

    /**
     * ManagerStart
     */
    const MANAGER_START = 'managerStart';

    /**
     * WorkerStart
     */
    const WORKER_START = 'workerStart';

    /**
     * HandShake
     */
    const HANDSHAKE = 'handshake';

    /**
     * Open
     */
    const OPEN = 'open';

    /**
     * Message
     */
    const MESSAGE = 'message';

    /**
     * Close
     */
    const CLOSE = 'close';

}