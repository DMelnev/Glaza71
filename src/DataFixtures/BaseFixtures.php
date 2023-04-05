<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;

abstract class BaseFixtures extends Fixture
{
    protected Generator $faker;
    protected ObjectManager $manager;

    public function load(ObjectManager $manager): void
    {
        $this->faker = Factory::create('ru_RU');
        $this->manager = $manager;
        $this->loadData($manager);
    }

    abstract function loadData(ObjectManager $manager);

    protected function create(string $className, callable $factory)
    {
        $entity = new $className;
        $factory($entity);
        $this->manager->persist($entity);
        return $entity;
    }

    protected function createMany(string $className, int $count, callable $factory)
    {
        for ($i = 0; $i < $count; $i++) {
            $entity = $this->create($className, $factory);
            $this->addReference("$className|$i", $entity);
        }
    }

    private array $referencesIndex = [];

    protected function getRandomReference($className): object
    {
        if (!isset($this->referencesIndex[$className])) {
            $this->referencesIndex[$className] = array_keys($this->referenceRepository->getReferencesByClass()[$className]);
        }
        if (empty($this->referencesIndex[$className])) {
            throw new \Exception("Class's links aren't found: " . $className);
        }
        return $this->getReference($this->faker->randomElement($this->referencesIndex[$className]));
    }
}