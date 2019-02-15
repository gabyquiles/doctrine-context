<?php

namespace GabyQuiles\Behat\Doctrine\Context;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;

class DoctrineContext implements Context
{
    use \Behatch\Asserter;

    /** @var EntityManagerInterface */
    private $manager;
    /** @var SchemaTool */
    private $schemaTool;
    /** @var ClassMetadata[] */
    private $classes;

    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->manager = $entityManager;
        $this->classes = $this->manager->getMetadataFactory()->getAllMetadata();
        $this->schemaTool = new SchemaTool($this->manager);
    }

    /**
     * @BeforeScenario @createSchema
     *
     * @throws \Doctrine\ORM\Tools\ToolsException
     */
    public function createDatabase()
    {
        $this->dropDatabase();
        $this->schemaTool->createSchema($this->classes);
    }

    /**
     * @AfterScenario @dropSchema
     */
    public function dropDatabase()
    {
        $this->schemaTool->dropSchema($this->classes);
        $this->manager->clear();
    }

    /**
     * @Then /^no entity "([^"]*)" exists with properties:$/
     */
    public function checkNoEntityExistWithProperties($entityName, TableNode $propertiesTable)
    {
        $this->checkNEntitiesExistWithProperties(0, $entityName, $propertiesTable);
    }

    /**
     * @Then /^an entity "([^"]*)" exists with properties:$/
     */
    public function checkEntityExistWithProperties($entityName, TableNode $propertiesTable)
    {
        $this->checkNEntitiesExistWithProperties(1, $entityName, $propertiesTable);
    }

    /**
     * @Then /^"([0-9]+)" entity "([^"]*)" exists with properties:$/
     */
    public function checkNEntitiesExistWithProperties($numberOfEntities, $entityName, TableNode $propertiesTable)
    {
        $rows = $propertiesTable->getHash();
        $entityProperties = array();
        foreach ($rows as $row) {
            $entityProperties[$row['property']] = $row['value'];
        }
        $entities = $this->manager->getRepository($entityName)->findBy($entityProperties);
        $this->assertCount($numberOfEntities, $entities);
    }
}