<?php

namespace SimpleThings\EntityAudit\EventListener;

use Doctrine\DBAL\Schema\Column;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\ORM\Tools\ToolEvents;
use SimpleThings\EntityAudit\AuditManager;
use Doctrine\ORM\Tools\Event\GenerateSchemaTableEventArgs;
use Doctrine\ORM\Tools\Event\GenerateSchemaEventArgs;
use Doctrine\Common\EventSubscriber;
use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Connection;

class CreateSchemaListener implements EventSubscriber
{
    /**
     * @var \SimpleThings\EntityAudit\AuditConfiguration
     */
    private $config;

    /**
     * @var \SimpleThings\EntityAudit\Metadata\MetadataFactory
     */
    private $metadataFactory;

    /**
     * @var Connection
     */
    private $connection;

    public function __construct(AuditManager $auditManager, Connection $connection)
    {
        $this->config = $auditManager->getConfiguration();
        $this->metadataFactory = $auditManager->getMetadataFactory();
        $this->connection = $connection;
    }

    public function getSubscribedEvents()
    {
        return [
            ToolEvents::postGenerateSchemaTable,
            ToolEvents::postGenerateSchema,
        ];
    }

    public function postGenerateSchemaTable(GenerateSchemaTableEventArgs $eventArgs)
    {
        $cm = $eventArgs->getClassMetadata();

        if (!$this->metadataFactory->isAudited($cm->name)) {
            $audited = false;
            if ($cm->isInheritanceTypeJoined() && $cm->rootEntityName == $cm->name) {
                foreach ($cm->subClasses as $subClass) {
                    if ($this->metadataFactory->isAudited($subClass)) {
                        $audited = true;
                    }
                }
            }
            if (!$audited) {
                return;
            }
        }

        $schema = $eventArgs->getSchema();
        $entityTable = $eventArgs->getClassTable();
        $revisionTable = $schema->createTable(
            $this->config->getTablePrefix() . $entityTable->getName() . $this->config->getTableSuffix()
        );

        foreach ($entityTable->getColumns() as $column) {
            $columnTypeName = $column->getType()->getName();
            $columnArrayOptions = $column->toArray();

            //ignore specific fields for table
            if ($this->config->isIgnoredField($entityTable->getName() . '.' . $column->getName())) {
                continue;
            }

            // change Enum type to String
            $sqlString = $column->getType()->getSQLDeclaration([], $this->connection->getDatabasePlatform());
            if ($this->config->convertEnumToString() && strpos($sqlString, 'ENUM') !== false) {
                $columnTypeName = Type::STRING;
                $columnArrayOptions['type'] = Type::getType($columnTypeName);
            }

            /* @var Column $column */
            $revisionTable->addColumn($column->getName(), $columnTypeName, array_merge(
                $columnArrayOptions,
                ['notnull' => false, 'autoincrement' => false]
            ));
        }
        $revisionTable->addColumn($this->config->getRevisionFieldName(), $this->config->getRevisionIdFieldType());
        $revisionTable->addColumn($this->config->getRevisionTypeFieldName(), 'string', ['length' => 4]);
        if (!in_array($cm->inheritanceType, [ClassMetadataInfo::INHERITANCE_TYPE_NONE, ClassMetadataInfo::INHERITANCE_TYPE_JOINED, ClassMetadataInfo::INHERITANCE_TYPE_SINGLE_TABLE])) {
            throw new \Exception(sprintf('Inheritance type "%s" is not yet supported', $cm->inheritanceType));
        }

        $pkColumns = $entityTable->getPrimaryKey()->getColumns();
        $pkColumns[] = $this->config->getRevisionFieldName();
        $revisionTable->setPrimaryKey($pkColumns);
        $revIndexName = $this->config->getRevisionFieldName() . '_' . md5($revisionTable->getName()) . '_idx';
        $revisionTable->addIndex([$this->config->getRevisionFieldName()],$revIndexName);
    }

    public function postGenerateSchema(GenerateSchemaEventArgs $eventArgs)
    {
        $schema = $eventArgs->getSchema();
        $revisionsTable = $schema->createTable($this->config->getRevisionTableName());
        $revisionsTable->addColumn('id', $this->config->getRevisionIdFieldType(), [
            'autoincrement' => true,
        ]);
        $revisionsTable->addColumn('timestamp', 'datetime');
        $revisionsTable->addColumn('username', 'string')->setNotnull(false);
        $revisionsTable->setPrimaryKey(['id']);
    }
}
