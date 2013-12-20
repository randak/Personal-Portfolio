<?php

namespace Randak\Personal\PortfolioBundle\Model\map;

use \RelationMap;
use \TableMap;


/**
 * This class defines the structure of the 'project_image' table.
 *
 *
 * This class was autogenerated by Propel 1.6.9 on:
 *
 * Thu Dec 19 10:25:32 2013
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 * @package    propel.generator.src.KristianRandall.Bundle.PortfolioBundle.Model.map
 */
class ProjectImageTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'src.KristianRandall.Bundle.PortfolioBundle.Model.map.ProjectImageTableMap';

    /**
     * Initialize the table attributes, columns and validators
     * Relations are not initialized by this method since they are lazy loaded
     *
     * @return void
     * @throws PropelException
     */
    public function initialize()
    {
        // attributes
        $this->setName('project_image');
        $this->setPhpName('ProjectImage');
        $this->setClassname('KristianRandall\\Bundle\\PortfolioBundle\\Model\\ProjectImage');
        $this->setPackage('src.KristianRandall.Bundle.PortfolioBundle.Model');
        $this->setUseIdGenerator(true);
        // columns
        $this->addPrimaryKey('id', 'Id', 'INTEGER', true, null, null);
        $this->addForeignKey('project_id', 'ProjectId', 'INTEGER', 'project', 'id', true, null, null);
        $this->addColumn('file', 'File', 'VARCHAR', true, 20, null);
        $this->addColumn('title', 'Title', 'VARCHAR', true, 100, null);
        $this->addColumn('description', 'Description', 'LONGVARCHAR', false, null, null);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('Project', 'KristianRandall\\Bundle\\PortfolioBundle\\Model\\Project', RelationMap::MANY_TO_ONE, array('project_id' => 'id', ), null, null);
    } // buildRelations()

} // ProjectImageTableMap
