<?php

namespace Randak\Personal\PortfolioBundle\Model\om;

use \BaseObject;
use \BasePeer;
use \Criteria;
use \DateTime;
use \Exception;
use \PDO;
use \Persistent;
use \Propel;
use \PropelCollection;
use \PropelDateTime;
use \PropelException;
use \PropelObjectCollection;
use \PropelPDO;
use Randak\Personal\PortfolioBundle\Model\Category;
use Randak\Personal\PortfolioBundle\Model\CategoryQuery;
use Randak\Personal\PortfolioBundle\Model\Project;
use Randak\Personal\PortfolioBundle\Model\ProjectCategory;
use Randak\Personal\PortfolioBundle\Model\ProjectCategoryQuery;
use Randak\Personal\PortfolioBundle\Model\ProjectImage;
use Randak\Personal\PortfolioBundle\Model\ProjectImageQuery;
use Randak\Personal\PortfolioBundle\Model\ProjectPeer;
use Randak\Personal\PortfolioBundle\Model\ProjectQuery;

abstract class BaseProject extends BaseObject implements Persistent
{
    /**
     * Peer class name
     */
    const PEER = 'KristianRandall\\Bundle\\PortfolioBundle\\Model\\ProjectPeer';

    /**
     * The Peer class.
     * Instance provides a convenient way of calling static methods on a class
     * that calling code may not be able to identify.
     * @var        ProjectPeer
     */
    protected static $peer;

    /**
     * The flag var to prevent infinit loop in deep copy
     * @var       boolean
     */
    protected $startCopy = false;

    /**
     * The value for the id field.
     * @var        int
     */
    protected $id;

    /**
     * The value for the slug field.
     * @var        string
     */
    protected $slug;

    /**
     * The value for the name field.
     * @var        string
     */
    protected $name;

    /**
     * The value for the content field.
     * @var        string
     */
    protected $content;

    /**
     * The value for the date field.
     * @var        string
     */
    protected $date;

    /**
     * @var        PropelObjectCollection|ProjectImage[] Collection to store aggregation of ProjectImage objects.
     */
    protected $collProjectImages;
    protected $collProjectImagesPartial;

    /**
     * @var        PropelObjectCollection|ProjectCategory[] Collection to store aggregation of ProjectCategory objects.
     */
    protected $collProjectCategories;
    protected $collProjectCategoriesPartial;

    /**
     * @var        PropelObjectCollection|Category[] Collection to store aggregation of Category objects.
     */
    protected $collCategories;

    /**
     * Flag to prevent endless save loop, if this object is referenced
     * by another object which falls in this transaction.
     * @var        boolean
     */
    protected $alreadyInSave = false;

    /**
     * Flag to prevent endless validation loop, if this object is referenced
     * by another object which falls in this transaction.
     * @var        boolean
     */
    protected $alreadyInValidation = false;

    /**
     * Flag to prevent endless clearAllReferences($deep=true) loop, if this object is referenced
     * @var        boolean
     */
    protected $alreadyInClearAllReferencesDeep = false;

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $categoriesScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $projectImagesScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $projectCategoriesScheduledForDeletion = null;

    /**
     * Get the [id] column value.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get the [slug] column value.
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Get the [name] column value.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get the [content] column value.
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Get the [optionally formatted] temporal [date] column value.
     *
     *
     * @param string $format The date/time format string (either date()-style or strftime()-style).
     *				 If format is null, then the raw DateTime object will be returned.
     * @return mixed Formatted date/time value as string or DateTime object (if format is null), null if column is null, and 0 if column value is 0000-00-00
     * @throws PropelException - if unable to parse/validate the date/time value.
     */
    public function getDate($format = null)
    {
        if ($this->date === null) {
            return null;
        }

        if ($this->date === '0000-00-00') {
            // while technically this is not a default value of null,
            // this seems to be closest in meaning.
            return null;
        }

        try {
            $dt = new DateTime($this->date);
        } catch (Exception $x) {
            throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->date, true), $x);
        }

        if ($format === null) {
            // Because propel.useDateTimeClass is true, we return a DateTime object.
            return $dt;
        }

        if (strpos($format, '%') !== false) {
            return strftime($format, $dt->format('U'));
        }

        return $dt->format($format);

    }

    /**
     * Set the value of [id] column.
     *
     * @param int $v new value
     * @return Project The current object (for fluent API support)
     */
    public function setId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->id !== $v) {
            $this->id = $v;
            $this->modifiedColumns[] = ProjectPeer::ID;
        }


        return $this;
    } // setId()

    /**
     * Set the value of [slug] column.
     *
     * @param string $v new value
     * @return Project The current object (for fluent API support)
     */
    public function setSlug($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->slug !== $v) {
            $this->slug = $v;
            $this->modifiedColumns[] = ProjectPeer::SLUG;
        }


        return $this;
    } // setSlug()

    /**
     * Set the value of [name] column.
     *
     * @param string $v new value
     * @return Project The current object (for fluent API support)
     */
    public function setName($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->name !== $v) {
            $this->name = $v;
            $this->modifiedColumns[] = ProjectPeer::NAME;
        }


        return $this;
    } // setName()

    /**
     * Set the value of [content] column.
     *
     * @param string $v new value
     * @return Project The current object (for fluent API support)
     */
    public function setContent($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->content !== $v) {
            $this->content = $v;
            $this->modifiedColumns[] = ProjectPeer::CONTENT;
        }


        return $this;
    } // setContent()

    /**
     * Sets the value of [date] column to a normalized version of the date/time value specified.
     *
     * @param mixed $v string, integer (timestamp), or DateTime value.
     *               Empty strings are treated as null.
     * @return Project The current object (for fluent API support)
     */
    public function setDate($v)
    {
        $dt = PropelDateTime::newInstance($v, null, 'DateTime');
        if ($this->date !== null || $dt !== null) {
            $currentDateAsString = ($this->date !== null && $tmpDt = new DateTime($this->date)) ? $tmpDt->format('Y-m-d') : null;
            $newDateAsString = $dt ? $dt->format('Y-m-d') : null;
            if ($currentDateAsString !== $newDateAsString) {
                $this->date = $newDateAsString;
                $this->modifiedColumns[] = ProjectPeer::DATE;
            }
        } // if either are not null


        return $this;
    } // setDate()

    /**
     * Indicates whether the columns in this object are only set to default values.
     *
     * This method can be used in conjunction with isModified() to indicate whether an object is both
     * modified _and_ has some values set which are non-default.
     *
     * @return boolean Whether the columns in this object are only been set with default values.
     */
    public function hasOnlyDefaultValues()
    {
        // otherwise, everything was equal, so return true
        return true;
    } // hasOnlyDefaultValues()

    /**
     * Hydrates (populates) the object variables with values from the database resultset.
     *
     * An offset (0-based "start column") is specified so that objects can be hydrated
     * with a subset of the columns in the resultset rows.  This is needed, for example,
     * for results of JOIN queries where the resultset row includes columns from two or
     * more tables.
     *
     * @param array $row The row returned by PDOStatement->fetch(PDO::FETCH_NUM)
     * @param int $startcol 0-based offset column which indicates which restultset column to start with.
     * @param boolean $rehydrate Whether this object is being re-hydrated from the database.
     * @return int             next starting column
     * @throws PropelException - Any caught Exception will be rewrapped as a PropelException.
     */
    public function hydrate($row, $startcol = 0, $rehydrate = false)
    {
        try {

            $this->id = ($row[$startcol + 0] !== null) ? (int) $row[$startcol + 0] : null;
            $this->slug = ($row[$startcol + 1] !== null) ? (string) $row[$startcol + 1] : null;
            $this->name = ($row[$startcol + 2] !== null) ? (string) $row[$startcol + 2] : null;
            $this->content = ($row[$startcol + 3] !== null) ? (string) $row[$startcol + 3] : null;
            $this->date = ($row[$startcol + 4] !== null) ? (string) $row[$startcol + 4] : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }
            $this->postHydrate($row, $startcol, $rehydrate);
            return $startcol + 5; // 5 = ProjectPeer::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException("Error populating Project object", $e);
        }
    }

    /**
     * Checks and repairs the internal consistency of the object.
     *
     * This method is executed after an already-instantiated object is re-hydrated
     * from the database.  It exists to check any foreign keys to make sure that
     * the objects related to the current object are correct based on foreign key.
     *
     * You can override this method in the stub class, but you should always invoke
     * the base method from the overridden method (i.e. parent::ensureConsistency()),
     * in case your model changes.
     *
     * @throws PropelException
     */
    public function ensureConsistency()
    {

    } // ensureConsistency

    /**
     * Reloads this object from datastore based on primary key and (optionally) resets all associated objects.
     *
     * This will only work if the object has been saved and has a valid primary key set.
     *
     * @param boolean $deep (optional) Whether to also de-associated any related objects.
     * @param PropelPDO $con (optional) The PropelPDO connection to use.
     * @return void
     * @throws PropelException - if this object is deleted, unsaved or doesn't have pk match in db
     */
    public function reload($deep = false, PropelPDO $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("Cannot reload a deleted object.");
        }

        if ($this->isNew()) {
            throw new PropelException("Cannot reload an unsaved object.");
        }

        if ($con === null) {
            $con = Propel::getConnection(ProjectPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $stmt = ProjectPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
        $row = $stmt->fetch(PDO::FETCH_NUM);
        $stmt->closeCursor();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->collProjectImages = null;

            $this->collProjectCategories = null;

            $this->collCategories = null;
        } // if (deep)
    }

    /**
     * Removes this object from datastore and sets delete attribute.
     *
     * @param PropelPDO $con
     * @return void
     * @throws PropelException
     * @throws Exception
     * @see        BaseObject::setDeleted()
     * @see        BaseObject::isDeleted()
     */
    public function delete(PropelPDO $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("This object has already been deleted.");
        }

        if ($con === null) {
            $con = Propel::getConnection(ProjectPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $con->beginTransaction();
        try {
            $deleteQuery = ProjectQuery::create()
                ->filterByPrimaryKey($this->getPrimaryKey());
            $ret = $this->preDelete($con);
            if ($ret) {
                $deleteQuery->delete($con);
                $this->postDelete($con);
                $con->commit();
                $this->setDeleted(true);
            } else {
                $con->commit();
            }
        } catch (Exception $e) {
            $con->rollBack();
            throw $e;
        }
    }

    /**
     * Persists this object to the database.
     *
     * If the object is new, it inserts it; otherwise an update is performed.
     * All modified related objects will also be persisted in the doSave()
     * method.  This method wraps all precipitate database operations in a
     * single transaction.
     *
     * @param PropelPDO $con
     * @return int             The number of rows affected by this insert/update and any referring fk objects' save() operations.
     * @throws PropelException
     * @throws Exception
     * @see        doSave()
     */
    public function save(PropelPDO $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("You cannot save an object that has been deleted.");
        }

        if ($con === null) {
            $con = Propel::getConnection(ProjectPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $con->beginTransaction();
        $isInsert = $this->isNew();
        try {
            $ret = $this->preSave($con);
            if ($isInsert) {
                $ret = $ret && $this->preInsert($con);
            } else {
                $ret = $ret && $this->preUpdate($con);
            }
            if ($ret) {
                $affectedRows = $this->doSave($con);
                if ($isInsert) {
                    $this->postInsert($con);
                } else {
                    $this->postUpdate($con);
                }
                $this->postSave($con);
                ProjectPeer::addInstanceToPool($this);
            } else {
                $affectedRows = 0;
            }
            $con->commit();

            return $affectedRows;
        } catch (Exception $e) {
            $con->rollBack();
            throw $e;
        }
    }

    /**
     * Performs the work of inserting or updating the row in the database.
     *
     * If the object is new, it inserts it; otherwise an update is performed.
     * All related objects are also updated in this method.
     *
     * @param PropelPDO $con
     * @return int             The number of rows affected by this insert/update and any referring fk objects' save() operations.
     * @throws PropelException
     * @see        save()
     */
    protected function doSave(PropelPDO $con)
    {
        $affectedRows = 0; // initialize var to track total num of affected rows
        if (!$this->alreadyInSave) {
            $this->alreadyInSave = true;

            if ($this->isNew() || $this->isModified()) {
                // persist changes
                if ($this->isNew()) {
                    $this->doInsert($con);
                } else {
                    $this->doUpdate($con);
                }
                $affectedRows += 1;
                $this->resetModified();
            }

            if ($this->categoriesScheduledForDeletion !== null) {
                if (!$this->categoriesScheduledForDeletion->isEmpty()) {
                    $pks = array();
                    $pk = $this->getPrimaryKey();
                    foreach ($this->categoriesScheduledForDeletion->getPrimaryKeys(false) as $remotePk) {
                        $pks[] = array($pk, $remotePk);
                    }
                    ProjectCategoryQuery::create()
                        ->filterByPrimaryKeys($pks)
                        ->delete($con);
                    $this->categoriesScheduledForDeletion = null;
                }

                foreach ($this->getCategories() as $category) {
                    if ($category->isModified()) {
                        $category->save($con);
                    }
                }
            } elseif ($this->collCategories) {
                foreach ($this->collCategories as $category) {
                    if ($category->isModified()) {
                        $category->save($con);
                    }
                }
            }

            if ($this->projectImagesScheduledForDeletion !== null) {
                if (!$this->projectImagesScheduledForDeletion->isEmpty()) {
                    ProjectImageQuery::create()
                        ->filterByPrimaryKeys($this->projectImagesScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->projectImagesScheduledForDeletion = null;
                }
            }

            if ($this->collProjectImages !== null) {
                foreach ($this->collProjectImages as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->projectCategoriesScheduledForDeletion !== null) {
                if (!$this->projectCategoriesScheduledForDeletion->isEmpty()) {
                    ProjectCategoryQuery::create()
                        ->filterByPrimaryKeys($this->projectCategoriesScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->projectCategoriesScheduledForDeletion = null;
                }
            }

            if ($this->collProjectCategories !== null) {
                foreach ($this->collProjectCategories as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            $this->alreadyInSave = false;

        }

        return $affectedRows;
    } // doSave()

    /**
     * Insert the row in the database.
     *
     * @param PropelPDO $con
     *
     * @throws PropelException
     * @see        doSave()
     */
    protected function doInsert(PropelPDO $con)
    {
        $modifiedColumns = array();
        $index = 0;

        $this->modifiedColumns[] = ProjectPeer::ID;
        if (null !== $this->id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key (' . ProjectPeer::ID . ')');
        }

         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(ProjectPeer::ID)) {
            $modifiedColumns[':p' . $index++]  = '`id`';
        }
        if ($this->isColumnModified(ProjectPeer::SLUG)) {
            $modifiedColumns[':p' . $index++]  = '`slug`';
        }
        if ($this->isColumnModified(ProjectPeer::NAME)) {
            $modifiedColumns[':p' . $index++]  = '`name`';
        }
        if ($this->isColumnModified(ProjectPeer::CONTENT)) {
            $modifiedColumns[':p' . $index++]  = '`content`';
        }
        if ($this->isColumnModified(ProjectPeer::DATE)) {
            $modifiedColumns[':p' . $index++]  = '`date`';
        }

        $sql = sprintf(
            'INSERT INTO `project` (%s) VALUES (%s)',
            implode(', ', $modifiedColumns),
            implode(', ', array_keys($modifiedColumns))
        );

        try {
            $stmt = $con->prepare($sql);
            foreach ($modifiedColumns as $identifier => $columnName) {
                switch ($columnName) {
                    case '`id`':
                        $stmt->bindValue($identifier, $this->id, PDO::PARAM_INT);
                        break;
                    case '`slug`':
                        $stmt->bindValue($identifier, $this->slug, PDO::PARAM_STR);
                        break;
                    case '`name`':
                        $stmt->bindValue($identifier, $this->name, PDO::PARAM_STR);
                        break;
                    case '`content`':
                        $stmt->bindValue($identifier, $this->content, PDO::PARAM_STR);
                        break;
                    case '`date`':
                        $stmt->bindValue($identifier, $this->date, PDO::PARAM_STR);
                        break;
                }
            }
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute INSERT statement [%s]', $sql), $e);
        }

        try {
            $pk = $con->lastInsertId();
        } catch (Exception $e) {
            throw new PropelException('Unable to get autoincrement id.', $e);
        }
        $this->setId($pk);

        $this->setNew(false);
    }

    /**
     * Update the row in the database.
     *
     * @param PropelPDO $con
     *
     * @see        doSave()
     */
    protected function doUpdate(PropelPDO $con)
    {
        $selectCriteria = $this->buildPkeyCriteria();
        $valuesCriteria = $this->buildCriteria();
        BasePeer::doUpdate($selectCriteria, $valuesCriteria, $con);
    }

    /**
     * Array of ValidationFailed objects.
     * @var        array ValidationFailed[]
     */
    protected $validationFailures = array();

    /**
     * Gets any ValidationFailed objects that resulted from last call to validate().
     *
     *
     * @return array ValidationFailed[]
     * @see        validate()
     */
    public function getValidationFailures()
    {
        return $this->validationFailures;
    }

    /**
     * Validates the objects modified field values and all objects related to this table.
     *
     * If $columns is either a column name or an array of column names
     * only those columns are validated.
     *
     * @param mixed $columns Column name or an array of column names.
     * @return boolean Whether all columns pass validation.
     * @see        doValidate()
     * @see        getValidationFailures()
     */
    public function validate($columns = null)
    {
        $res = $this->doValidate($columns);
        if ($res === true) {
            $this->validationFailures = array();

            return true;
        }

        $this->validationFailures = $res;

        return false;
    }

    /**
     * This function performs the validation work for complex object models.
     *
     * In addition to checking the current object, all related objects will
     * also be validated.  If all pass then <code>true</code> is returned; otherwise
     * an aggreagated array of ValidationFailed objects will be returned.
     *
     * @param array $columns Array of column names to validate.
     * @return mixed <code>true</code> if all validations pass; array of <code>ValidationFailed</code> objets otherwise.
     */
    protected function doValidate($columns = null)
    {
        if (!$this->alreadyInValidation) {
            $this->alreadyInValidation = true;
            $retval = null;

            $failureMap = array();


            if (($retval = ProjectPeer::doValidate($this, $columns)) !== true) {
                $failureMap = array_merge($failureMap, $retval);
            }


                if ($this->collProjectImages !== null) {
                    foreach ($this->collProjectImages as $referrerFK) {
                        if (!$referrerFK->validate($columns)) {
                            $failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
                        }
                    }
                }

                if ($this->collProjectCategories !== null) {
                    foreach ($this->collProjectCategories as $referrerFK) {
                        if (!$referrerFK->validate($columns)) {
                            $failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
                        }
                    }
                }


            $this->alreadyInValidation = false;
        }

        return (!empty($failureMap) ? $failureMap : true);
    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(ProjectPeer::DATABASE_NAME);

        if ($this->isColumnModified(ProjectPeer::ID)) $criteria->add(ProjectPeer::ID, $this->id);
        if ($this->isColumnModified(ProjectPeer::SLUG)) $criteria->add(ProjectPeer::SLUG, $this->slug);
        if ($this->isColumnModified(ProjectPeer::NAME)) $criteria->add(ProjectPeer::NAME, $this->name);
        if ($this->isColumnModified(ProjectPeer::CONTENT)) $criteria->add(ProjectPeer::CONTENT, $this->content);
        if ($this->isColumnModified(ProjectPeer::DATE)) $criteria->add(ProjectPeer::DATE, $this->date);

        return $criteria;
    }

    /**
     * Builds a Criteria object containing the primary key for this object.
     *
     * Unlike buildCriteria() this method includes the primary key values regardless
     * of whether or not they have been modified.
     *
     * @return Criteria The Criteria object containing value(s) for primary key(s).
     */
    public function buildPkeyCriteria()
    {
        $criteria = new Criteria(ProjectPeer::DATABASE_NAME);
        $criteria->add(ProjectPeer::ID, $this->id);

        return $criteria;
    }

    /**
     * Returns the primary key for this object (row).
     * @return int
     */
    public function getPrimaryKey()
    {
        return $this->getId();
    }

    /**
     * Generic method to set the primary key (id column).
     *
     * @param  int $key Primary key.
     * @return void
     */
    public function setPrimaryKey($key)
    {
        $this->setId($key);
    }

    /**
     * Returns true if the primary key for this object is null.
     * @return boolean
     */
    public function isPrimaryKeyNull()
    {

        return null === $this->getId();
    }

    /**
     * Sets contents of passed object to values from current object.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param object $copyObj An object of Project (or compatible) type.
     * @param boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setSlug($this->getSlug());
        $copyObj->setName($this->getName());
        $copyObj->setContent($this->getContent());
        $copyObj->setDate($this->getDate());

        if ($deepCopy && !$this->startCopy) {
            // important: temporarily setNew(false) because this affects the behavior of
            // the getter/setter methods for fkey referrer objects.
            $copyObj->setNew(false);
            // store object hash to prevent cycle
            $this->startCopy = true;

            foreach ($this->getProjectImages() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addProjectImage($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getProjectCategories() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addProjectCategory($relObj->copy($deepCopy));
                }
            }

            //unflag object copy
            $this->startCopy = false;
        } // if ($deepCopy)

        if ($makeNew) {
            $copyObj->setNew(true);
            $copyObj->setId(NULL); // this is a auto-increment column, so set to default value
        }
    }

    /**
     * Makes a copy of this object that will be inserted as a new row in table when saved.
     * It creates a new object filling in the simple attributes, but skipping any primary
     * keys that are defined for the table.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @return Project Clone of current object.
     * @throws PropelException
     */
    public function copy($deepCopy = false)
    {
        // we use get_class(), because this might be a subclass
        $clazz = get_class($this);
        $copyObj = new $clazz();
        $this->copyInto($copyObj, $deepCopy);

        return $copyObj;
    }

    /**
     * Returns a peer instance associated with this om.
     *
     * Since Peer classes are not to have any instance attributes, this method returns the
     * same instance for all member of this class. The method could therefore
     * be static, but this would prevent one from overriding the behavior.
     *
     * @return ProjectPeer
     */
    public function getPeer()
    {
        if (self::$peer === null) {
            self::$peer = new ProjectPeer();
        }

        return self::$peer;
    }


    /**
     * Initializes a collection based on the name of a relation.
     * Avoids crafting an 'init[$relationName]s' method name
     * that wouldn't work when StandardEnglishPluralizer is used.
     *
     * @param string $relationName The name of the relation to initialize
     * @return void
     */
    public function initRelation($relationName)
    {
        if ('ProjectImage' == $relationName) {
            $this->initProjectImages();
        }
        if ('ProjectCategory' == $relationName) {
            $this->initProjectCategories();
        }
    }

    /**
     * Clears out the collProjectImages collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return Project The current object (for fluent API support)
     * @see        addProjectImages()
     */
    public function clearProjectImages()
    {
        $this->collProjectImages = null; // important to set this to null since that means it is uninitialized
        $this->collProjectImagesPartial = null;

        return $this;
    }

    /**
     * reset is the collProjectImages collection loaded partially
     *
     * @return void
     */
    public function resetPartialProjectImages($v = true)
    {
        $this->collProjectImagesPartial = $v;
    }

    /**
     * Initializes the collProjectImages collection.
     *
     * By default this just sets the collProjectImages collection to an empty array (like clearcollProjectImages());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initProjectImages($overrideExisting = true)
    {
        if (null !== $this->collProjectImages && !$overrideExisting) {
            return;
        }
        $this->collProjectImages = new PropelObjectCollection();
        $this->collProjectImages->setModel('ProjectImage');
    }

    /**
     * Gets an array of ProjectImage objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this Project is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|ProjectImage[] List of ProjectImage objects
     * @throws PropelException
     */
    public function getProjectImages($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collProjectImagesPartial && !$this->isNew();
        if (null === $this->collProjectImages || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collProjectImages) {
                // return empty collection
                $this->initProjectImages();
            } else {
                $collProjectImages = ProjectImageQuery::create(null, $criteria)
                    ->filterByProject($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collProjectImagesPartial && count($collProjectImages)) {
                      $this->initProjectImages(false);

                      foreach($collProjectImages as $obj) {
                        if (false == $this->collProjectImages->contains($obj)) {
                          $this->collProjectImages->append($obj);
                        }
                      }

                      $this->collProjectImagesPartial = true;
                    }

                    $collProjectImages->getInternalIterator()->rewind();
                    return $collProjectImages;
                }

                if($partial && $this->collProjectImages) {
                    foreach($this->collProjectImages as $obj) {
                        if($obj->isNew()) {
                            $collProjectImages[] = $obj;
                        }
                    }
                }

                $this->collProjectImages = $collProjectImages;
                $this->collProjectImagesPartial = false;
            }
        }

        return $this->collProjectImages;
    }

    /**
     * Sets a collection of ProjectImage objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $projectImages A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return Project The current object (for fluent API support)
     */
    public function setProjectImages(PropelCollection $projectImages, PropelPDO $con = null)
    {
        $projectImagesToDelete = $this->getProjectImages(new Criteria(), $con)->diff($projectImages);

        $this->projectImagesScheduledForDeletion = unserialize(serialize($projectImagesToDelete));

        foreach ($projectImagesToDelete as $projectImageRemoved) {
            $projectImageRemoved->setProject(null);
        }

        $this->collProjectImages = null;
        foreach ($projectImages as $projectImage) {
            $this->addProjectImage($projectImage);
        }

        $this->collProjectImages = $projectImages;
        $this->collProjectImagesPartial = false;

        return $this;
    }

    /**
     * Returns the number of related ProjectImage objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related ProjectImage objects.
     * @throws PropelException
     */
    public function countProjectImages(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collProjectImagesPartial && !$this->isNew();
        if (null === $this->collProjectImages || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collProjectImages) {
                return 0;
            }

            if($partial && !$criteria) {
                return count($this->getProjectImages());
            }
            $query = ProjectImageQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByProject($this)
                ->count($con);
        }

        return count($this->collProjectImages);
    }

    /**
     * Method called to associate a ProjectImage object to this object
     * through the ProjectImage foreign key attribute.
     *
     * @param    ProjectImage $l ProjectImage
     * @return Project The current object (for fluent API support)
     */
    public function addProjectImage(ProjectImage $l)
    {
        if ($this->collProjectImages === null) {
            $this->initProjectImages();
            $this->collProjectImagesPartial = true;
        }
        if (!in_array($l, $this->collProjectImages->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddProjectImage($l);
        }

        return $this;
    }

    /**
     * @param	ProjectImage $projectImage The projectImage object to add.
     */
    protected function doAddProjectImage($projectImage)
    {
        $this->collProjectImages[]= $projectImage;
        $projectImage->setProject($this);
    }

    /**
     * @param	ProjectImage $projectImage The projectImage object to remove.
     * @return Project The current object (for fluent API support)
     */
    public function removeProjectImage($projectImage)
    {
        if ($this->getProjectImages()->contains($projectImage)) {
            $this->collProjectImages->remove($this->collProjectImages->search($projectImage));
            if (null === $this->projectImagesScheduledForDeletion) {
                $this->projectImagesScheduledForDeletion = clone $this->collProjectImages;
                $this->projectImagesScheduledForDeletion->clear();
            }
            $this->projectImagesScheduledForDeletion[]= clone $projectImage;
            $projectImage->setProject(null);
        }

        return $this;
    }

    /**
     * Clears out the collProjectCategories collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return Project The current object (for fluent API support)
     * @see        addProjectCategories()
     */
    public function clearProjectCategories()
    {
        $this->collProjectCategories = null; // important to set this to null since that means it is uninitialized
        $this->collProjectCategoriesPartial = null;

        return $this;
    }

    /**
     * reset is the collProjectCategories collection loaded partially
     *
     * @return void
     */
    public function resetPartialProjectCategories($v = true)
    {
        $this->collProjectCategoriesPartial = $v;
    }

    /**
     * Initializes the collProjectCategories collection.
     *
     * By default this just sets the collProjectCategories collection to an empty array (like clearcollProjectCategories());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initProjectCategories($overrideExisting = true)
    {
        if (null !== $this->collProjectCategories && !$overrideExisting) {
            return;
        }
        $this->collProjectCategories = new PropelObjectCollection();
        $this->collProjectCategories->setModel('ProjectCategory');
    }

    /**
     * Gets an array of ProjectCategory objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this Project is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|ProjectCategory[] List of ProjectCategory objects
     * @throws PropelException
     */
    public function getProjectCategories($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collProjectCategoriesPartial && !$this->isNew();
        if (null === $this->collProjectCategories || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collProjectCategories) {
                // return empty collection
                $this->initProjectCategories();
            } else {
                $collProjectCategories = ProjectCategoryQuery::create(null, $criteria)
                    ->filterByProject($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collProjectCategoriesPartial && count($collProjectCategories)) {
                      $this->initProjectCategories(false);

                      foreach($collProjectCategories as $obj) {
                        if (false == $this->collProjectCategories->contains($obj)) {
                          $this->collProjectCategories->append($obj);
                        }
                      }

                      $this->collProjectCategoriesPartial = true;
                    }

                    $collProjectCategories->getInternalIterator()->rewind();
                    return $collProjectCategories;
                }

                if($partial && $this->collProjectCategories) {
                    foreach($this->collProjectCategories as $obj) {
                        if($obj->isNew()) {
                            $collProjectCategories[] = $obj;
                        }
                    }
                }

                $this->collProjectCategories = $collProjectCategories;
                $this->collProjectCategoriesPartial = false;
            }
        }

        return $this->collProjectCategories;
    }

    /**
     * Sets a collection of ProjectCategory objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $projectCategories A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return Project The current object (for fluent API support)
     */
    public function setProjectCategories(PropelCollection $projectCategories, PropelPDO $con = null)
    {
        $projectCategoriesToDelete = $this->getProjectCategories(new Criteria(), $con)->diff($projectCategories);

        $this->projectCategoriesScheduledForDeletion = unserialize(serialize($projectCategoriesToDelete));

        foreach ($projectCategoriesToDelete as $projectCategoryRemoved) {
            $projectCategoryRemoved->setProject(null);
        }

        $this->collProjectCategories = null;
        foreach ($projectCategories as $projectCategory) {
            $this->addProjectCategory($projectCategory);
        }

        $this->collProjectCategories = $projectCategories;
        $this->collProjectCategoriesPartial = false;

        return $this;
    }

    /**
     * Returns the number of related ProjectCategory objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related ProjectCategory objects.
     * @throws PropelException
     */
    public function countProjectCategories(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collProjectCategoriesPartial && !$this->isNew();
        if (null === $this->collProjectCategories || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collProjectCategories) {
                return 0;
            }

            if($partial && !$criteria) {
                return count($this->getProjectCategories());
            }
            $query = ProjectCategoryQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByProject($this)
                ->count($con);
        }

        return count($this->collProjectCategories);
    }

    /**
     * Method called to associate a ProjectCategory object to this object
     * through the ProjectCategory foreign key attribute.
     *
     * @param    ProjectCategory $l ProjectCategory
     * @return Project The current object (for fluent API support)
     */
    public function addProjectCategory(ProjectCategory $l)
    {
        if ($this->collProjectCategories === null) {
            $this->initProjectCategories();
            $this->collProjectCategoriesPartial = true;
        }
        if (!in_array($l, $this->collProjectCategories->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddProjectCategory($l);
        }

        return $this;
    }

    /**
     * @param	ProjectCategory $projectCategory The projectCategory object to add.
     */
    protected function doAddProjectCategory($projectCategory)
    {
        $this->collProjectCategories[]= $projectCategory;
        $projectCategory->setProject($this);
    }

    /**
     * @param	ProjectCategory $projectCategory The projectCategory object to remove.
     * @return Project The current object (for fluent API support)
     */
    public function removeProjectCategory($projectCategory)
    {
        if ($this->getProjectCategories()->contains($projectCategory)) {
            $this->collProjectCategories->remove($this->collProjectCategories->search($projectCategory));
            if (null === $this->projectCategoriesScheduledForDeletion) {
                $this->projectCategoriesScheduledForDeletion = clone $this->collProjectCategories;
                $this->projectCategoriesScheduledForDeletion->clear();
            }
            $this->projectCategoriesScheduledForDeletion[]= clone $projectCategory;
            $projectCategory->setProject(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Project is new, it will return
     * an empty collection; or if this Project has previously
     * been saved, it will retrieve related ProjectCategories from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Project.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|ProjectCategory[] List of ProjectCategory objects
     */
    public function getProjectCategoriesJoinCategory($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = ProjectCategoryQuery::create(null, $criteria);
        $query->joinWith('Category', $join_behavior);

        return $this->getProjectCategories($query, $con);
    }

    /**
     * Clears out the collCategories collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return Project The current object (for fluent API support)
     * @see        addCategories()
     */
    public function clearCategories()
    {
        $this->collCategories = null; // important to set this to null since that means it is uninitialized
        $this->collCategoriesPartial = null;

        return $this;
    }

    /**
     * Initializes the collCategories collection.
     *
     * By default this just sets the collCategories collection to an empty collection (like clearCategories());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @return void
     */
    public function initCategories()
    {
        $this->collCategories = new PropelObjectCollection();
        $this->collCategories->setModel('Category');
    }

    /**
     * Gets a collection of Category objects related by a many-to-many relationship
     * to the current object by way of the project_category cross-reference table.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this Project is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria Optional query object to filter the query
     * @param PropelPDO $con Optional connection object
     *
     * @return PropelObjectCollection|Category[] List of Category objects
     */
    public function getCategories($criteria = null, PropelPDO $con = null)
    {
        if (null === $this->collCategories || null !== $criteria) {
            if ($this->isNew() && null === $this->collCategories) {
                // return empty collection
                $this->initCategories();
            } else {
                $collCategories = CategoryQuery::create(null, $criteria)
                    ->filterByProject($this)
                    ->find($con);
                if (null !== $criteria) {
                    return $collCategories;
                }
                $this->collCategories = $collCategories;
            }
        }

        return $this->collCategories;
    }

    /**
     * Sets a collection of Category objects related by a many-to-many relationship
     * to the current object by way of the project_category cross-reference table.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $categories A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return Project The current object (for fluent API support)
     */
    public function setCategories(PropelCollection $categories, PropelPDO $con = null)
    {
        $this->clearCategories();
        $currentCategories = $this->getCategories();

        $this->categoriesScheduledForDeletion = $currentCategories->diff($categories);

        foreach ($categories as $category) {
            if (!$currentCategories->contains($category)) {
                $this->doAddCategory($category);
            }
        }

        $this->collCategories = $categories;

        return $this;
    }

    /**
     * Gets the number of Category objects related by a many-to-many relationship
     * to the current object by way of the project_category cross-reference table.
     *
     * @param Criteria $criteria Optional query object to filter the query
     * @param boolean $distinct Set to true to force count distinct
     * @param PropelPDO $con Optional connection object
     *
     * @return int the number of related Category objects
     */
    public function countCategories($criteria = null, $distinct = false, PropelPDO $con = null)
    {
        if (null === $this->collCategories || null !== $criteria) {
            if ($this->isNew() && null === $this->collCategories) {
                return 0;
            } else {
                $query = CategoryQuery::create(null, $criteria);
                if ($distinct) {
                    $query->distinct();
                }

                return $query
                    ->filterByProject($this)
                    ->count($con);
            }
        } else {
            return count($this->collCategories);
        }
    }

    /**
     * Associate a Category object to this object
     * through the project_category cross reference table.
     *
     * @param  Category $category The ProjectCategory object to relate
     * @return Project The current object (for fluent API support)
     */
    public function addCategory(Category $category)
    {
        if ($this->collCategories === null) {
            $this->initCategories();
        }
        if (!$this->collCategories->contains($category)) { // only add it if the **same** object is not already associated
            $this->doAddCategory($category);

            $this->collCategories[]= $category;
        }

        return $this;
    }

    /**
     * @param	Category $category The category object to add.
     */
    protected function doAddCategory($category)
    {
        $projectCategory = new ProjectCategory();
        $projectCategory->setCategory($category);
        $this->addProjectCategory($projectCategory);
    }

    /**
     * Remove a Category object to this object
     * through the project_category cross reference table.
     *
     * @param Category $category The ProjectCategory object to relate
     * @return Project The current object (for fluent API support)
     */
    public function removeCategory(Category $category)
    {
        if ($this->getCategories()->contains($category)) {
            $this->collCategories->remove($this->collCategories->search($category));
            if (null === $this->categoriesScheduledForDeletion) {
                $this->categoriesScheduledForDeletion = clone $this->collCategories;
                $this->categoriesScheduledForDeletion->clear();
            }
            $this->categoriesScheduledForDeletion[]= $category;
        }

        return $this;
    }

    /**
     * Clears the current object and sets all attributes to their default values
     */
    public function clear()
    {
        $this->id = null;
        $this->slug = null;
        $this->name = null;
        $this->content = null;
        $this->date = null;
        $this->alreadyInSave = false;
        $this->alreadyInValidation = false;
        $this->alreadyInClearAllReferencesDeep = false;
        $this->clearAllReferences();
        $this->resetModified();
        $this->setNew(true);
        $this->setDeleted(false);
    }

    /**
     * Resets all references to other model objects or collections of model objects.
     *
     * This method is a user-space workaround for PHP's inability to garbage collect
     * objects with circular references (even in PHP 5.3). This is currently necessary
     * when using Propel in certain daemon or large-volumne/high-memory operations.
     *
     * @param boolean $deep Whether to also clear the references on all referrer objects.
     */
    public function clearAllReferences($deep = false)
    {
        if ($deep && !$this->alreadyInClearAllReferencesDeep) {
            $this->alreadyInClearAllReferencesDeep = true;
            if ($this->collProjectImages) {
                foreach ($this->collProjectImages as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collProjectCategories) {
                foreach ($this->collProjectCategories as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collCategories) {
                foreach ($this->collCategories as $o) {
                    $o->clearAllReferences($deep);
                }
            }

            $this->alreadyInClearAllReferencesDeep = false;
        } // if ($deep)

        if ($this->collProjectImages instanceof PropelCollection) {
            $this->collProjectImages->clearIterator();
        }
        $this->collProjectImages = null;
        if ($this->collProjectCategories instanceof PropelCollection) {
            $this->collProjectCategories->clearIterator();
        }
        $this->collProjectCategories = null;
        if ($this->collCategories instanceof PropelCollection) {
            $this->collCategories->clearIterator();
        }
        $this->collCategories = null;
    }

    /**
     * return the string representation of this object
     *
     * @return string The value of the 'name' column
     */
    public function __toString()
    {
        return (string) $this->getName();
    }

    /**
     * return true is the object is in saving state
     *
     * @return boolean
     */
    public function isAlreadyInSave()
    {
        return $this->alreadyInSave;
    }

}
