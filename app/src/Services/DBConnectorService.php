<?php
namespace TWB\Services;

use Illuminate\Database\Capsule\Manager as DatabaseManager;

class DBConnectorService
{
    /**
     * @var \Illuminate\Database\Connection
     */
    private $connector;

    private static $instance;

    /**
     * DBConnectorService constructor.
     * @param ConfigService $ConfigService
     * @param DatabaseManager $DatabaseManager
     */
    private function __construct(
        protected ?ConfigService $ConfigService = null,
        protected ?DatabaseManager $DatabaseManager = null
    ) {
        $this->ConfigService = $this->ConfigService ?: new ConfigService();
        $this->DatabaseManager = $this->DatabaseManager ?: new DatabaseManager();
        $this->connector = $this->connect();
    }

    public static function create(
        ?ConfigService $ConfigService = null,
        ?DatabaseManager $DatabaseManager = null
    )
    {
        if(self::$instance == null) {
            self::$instance = new DBConnectorService($ConfigService, $DatabaseManager);
        }
        return self::$instance;
    }

    /**
     * @return \Illuminate\Database\Connection
     */
    public function getConnector()
    {
        return $this->connector;
    }

    /**
     * @return bool
     */
    public function isConnect()
    {
        return !!$this->connector;
    }

    /**
     * @return \Illuminate\Database\Connection
     */
    private function connect()
    {
        $this->DatabaseManager->addConnection($this->ConfigService->getDatabaseConfigure());
        $this->DatabaseManager->setAsGlobal();
        return $this->DatabaseManager->getConnection();
    }
}