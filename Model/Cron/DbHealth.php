<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\CloudComponents\Model\Cron;

use Magento\CloudComponents\Model\ConstantList;
use Magento\Framework\App\DeploymentConfig;
use Magento\Framework\App\ResourceConnection;
use Psr\Log\LoggerInterface;

/**
 * Logs DB health status.
 */
class DbHealth
{
    /**
     * @var ResourceConnection
     */
    private $connection;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var DeploymentConfig
     */
    private $deploymentConfig;

    /**
     * @param ResourceConnection $resource
     * @param LoggerInterface $logger
     * @param DeploymentConfig $deploymentConfig
     */
    public function __construct(
        ResourceConnection $resource,
        LoggerInterface $logger,
        DeploymentConfig $deploymentConfig
    ) {
        $this->connection = $resource->getConnection();
        $this->logger = $logger;
        $this->deploymentConfig = $deploymentConfig;
    }

    /**
     * Execute the queries.
     */
    public function execute()
    {
        if (!$this->deploymentConfig->get(ConstantList::CONFIG_PATH_LOG_DB_HEALTH)) {
            return;
        }

        $this->logger->debug('Process list', [
            'data' => $this->connection->query('SHOW PROCESSLIST')->fetchAll(),
        ]);
        $this->logger->debug('InnoDB Status', [
            'data' => $this->connection->query('SHOW ENGINE INNODB STATUS')->fetchAll()
        ]);
    }
}