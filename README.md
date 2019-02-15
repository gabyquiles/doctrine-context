#Behat Doctrine Context
This package tries to help when testing with doctrine. Its main goal is to drop 
and recreate a database based on Doctrine Metadata while testing with Behat.

## Configuration
In order to enable it in behat you need to have a similar behat.yml
```
default:
  suites:
    default:
      contexts:
     ...
      - GabyQuiles\Behat\Context\DoctrineContext:
          entityManager: '@doctrine.orm.entity_manager'
```

## Usage
When creating your Behat scenarios 
- Add `@createSchema` in your first scenario in order to create a new database
- Add `@dropSchema` in your last scenario in order to drop your database