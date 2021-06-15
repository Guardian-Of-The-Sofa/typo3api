<?php

namespace Typo3Api\Tca;

use Typo3Api\Builder\Context\TableBuilderContext;
use Typo3Api\Builder\Context\TcaBuilderContext;

/**
 * You probably don't need this.
 *
 * This is a basic configuration for fields every table should have.
 * Because of this, it is added to every newly created table automatically.
 */
class BaseConfiguration implements TcaConfigurationInterface
{
    public function modifyCtrl(array &$ctrl, TcaBuilderContext $tcaBuilder)
    {
        $ctrl['delete'] = 'deleted';
        $ctrl['tstamp'] = 'tstamp';
        $ctrl['crdate'] = 'crdate';
        $ctrl['cruser_id'] = 'cruser_id';
        $ctrl['origUid'] = 'origUid';
        $ctrl['label'] = 'uid';
    }

    public function getColumns(TcaBuilderContext $tcaBuilder): array
    {
        return [];
    }

    public function getPalettes(TcaBuilderContext $tcaBuilder): array
    {
        return [];
    }

    public function getShowItemString(TcaBuilderContext $tcaBuilder): string
    {
        return '';
    }

    public function getDbTableDefinitions(TableBuilderContext $tableBuilder): array
    {
        return [
            $tableBuilder->getTableName() => [
                "uid int(11) NOT NULL auto_increment",
                "PRIMARY KEY (uid)",
                "pid INT(11) NOT NULL DEFAULT '0'",
                "INDEX pid (pid)", // i'm not sure if every table should have an index on pid

                "deleted TINYINT(1) DEFAULT '0' NOT NULL",
                "tstamp INT(11) DEFAULT '0' NOT NULL",
                "crdate INT(11) DEFAULT '0' NOT NULL",
                "cruser_id INT(11) DEFAULT '0' NOT NULL",
                "origUid INT(11) DEFAULT '0' NOT NULL",
            ]
        ];
    }
}
