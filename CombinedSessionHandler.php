<?php

require 'vendor/autoload.php';

class CombinedSessionHandler extends SessionHandler
{
    private $dbSessionService = null;

    public function __construct()
    {
        try {
            $this->dbSessionService = new \JamieCressey\SessionHandler\SessionHandler();
            $this->dbSessionService->setDbConnection(new \PDOWrapper\DB('localhost', 'upwork-php-session', 'upwork', '12345678'));
            $this->dbSessionService->setDbTable('sessions');
        } catch (Exception $e) {
            error_log("Unable to acquire DB connection");
        }
    }

    public function open(string $path, string $name): bool
    {
        $this->dbSessionService->open($path, $name);

        return parent::open($path, $name);
    }

    public function close(): bool
    {
        $this->dbSessionService->close();
        return parent::close();
    }

    public function read(string $id): string|false
    {
        $sessionData = parent::read($id);
        $dbData = $this->dbSessionService->read($id);
        if ($dbData !== $sessionData) {
            error_log('DB session data is different than default file session data');
        }
        return $sessionData;
    }

    public function write(string $id, string $data): bool
    {
        $this->dbSessionService->write($id, $data);
        return parent::write($id, $data);
    }

    public function gc(int $max_lifetime): int|false
    {
        $this->dbSessionService->gc($max_lifetime);

        return parent::gc($max_lifetime);
    }

    public function destroy(string $id): bool
    {
        $this->dbSessionService->destroy($id);

        return parent::destroy($id);
    }

}