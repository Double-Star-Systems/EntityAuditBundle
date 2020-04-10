<?php

namespace SimpleThings\EntityAudit;

use Doctrine\ORM\Mapping\ClassMetadataInfo;

class AuditConfiguration
{
    private $auditedEntityClasses = [];
    private $globalIgnoreColumns = [];
    private $tablePrefix = '';
    private $tableSuffix = '_audit';
    private $revisionTableName = 'revisions';
    private $revisionFieldName = 'rev';
    private $revisionTypeFieldName = 'revtype';
    private $revisionIdFieldType = 'integer';
    private $usernameCallable;

    /**
     * @param array $classes
     *
     * @return AuditConfiguration
     */
    public static function forEntities(array $classes)
    {
        $conf = new self;
        $conf->auditedEntityClasses = $classes;

        return $conf;
    }

    /**
     * @param ClassMetadataInfo $metadata
     *
     * @return string
     */
    public function getTableName(ClassMetadataInfo $metadata)
    {
        $tableName = $metadata->getTableName();

        //## Fix for doctrine/orm >= 2.5
        if (method_exists($metadata, 'getSchemaName') && $metadata->getSchemaName()) {
            $tableName = $metadata->getSchemaName() . '.' . $tableName;
        }

        return $this->getTablePrefix() . $tableName . $this->getTableSuffix();
    }

    public function getTablePrefix()
    {
        return $this->tablePrefix;
    }

    public function setTablePrefix($prefix)
    {
        $this->tablePrefix = $prefix;
    }

    public function getTableSuffix()
    {
        return $this->tableSuffix;
    }

    public function setTableSuffix($suffix)
    {
        $this->tableSuffix = $suffix;
    }

    public function getRevisionFieldName()
    {
        return $this->revisionFieldName;
    }

    public function setRevisionFieldName($revisionFieldName)
    {
        $this->revisionFieldName = $revisionFieldName;
    }

    public function getRevisionTypeFieldName()
    {
        return $this->revisionTypeFieldName;
    }

    public function setRevisionTypeFieldName($revisionTypeFieldName)
    {
        $this->revisionTypeFieldName = $revisionTypeFieldName;
    }

    public function getRevisionTableName()
    {
        return $this->revisionTableName;
    }

    public function setRevisionTableName($revisionTableName)
    {
        $this->revisionTableName = $revisionTableName;
    }

    public function setAuditedEntityClasses(array $classes)
    {
        $this->auditedEntityClasses = $classes;
    }

    public function getGlobalIgnoreColumns()
    {
        return $this->globalIgnoreColumns;
    }

    public function setGlobalIgnoreColumns(array $columns)
    {
        $this->globalIgnoreColumns = $columns;
    }

    public function createMetadataFactory()
    {
        return new Metadata\MetadataFactory($this->auditedEntityClasses);
    }

    /**
     * @deprecated
     * @param string|null $username
     */
    public function setCurrentUsername($username)
    {
        $this->setUsernameCallable(function () use ($username) {
            return $username;
        });
    }

    /**
     * @return string
     */
    public function getCurrentUsername()
    {
        $callable = $this->usernameCallable;

        return (string) ($callable ? $callable() : '');
    }

    public function setUsernameCallable($usernameCallable)
    {
        // php 5.3 compat
        if (null !== $usernameCallable && !is_callable($usernameCallable)) {
            throw new \InvalidArgumentException(sprintf(
                'Username Callable must be callable. Got: %s',
                is_object($usernameCallable) ? get_class($usernameCallable) : gettype($usernameCallable)
            ));
        }

        $this->usernameCallable = $usernameCallable;
    }

    /**
     * @return callable|null
     */
    public function getUsernameCallable()
    {
        return $this->usernameCallable;
    }

    public function setRevisionIdFieldType($revisionIdFieldType)
    {
        $this->revisionIdFieldType = $revisionIdFieldType;
    }

    public function getRevisionIdFieldType()
    {
        return $this->revisionIdFieldType;
    }
}
