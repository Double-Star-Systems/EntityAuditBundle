<?php

namespace SimpleThings\EntityAudit\Exception;

abstract class AuditException extends \Exception
{
    protected $className;

    protected $id;

    protected $revision;

    public function __construct($className, $id, $revision)
    {
        $this->className = $className;
        $this->id = $id;
        $this->revision = $revision;
    }
}