<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Nelmio\Alice\Loader\NativeLoader;
use Nelmio\Alice\Loader\SimpleFileLoader;

class LoadFixtures extends Fixture
{
    /**
     * @var SimpleFileLoader
     */
    private $simpleFileLoader;

    public function __construct(SimpleFileLoader $simpleFileLoader)
    {
        $this->simpleFileLoader = $simpleFileLoader;
    }
    public function load(ObjectManager $manager)
    {
        $loader = $this->simpleFileLoader;

        $objectSet = $loader->loadFile(__DIR__.'/fixtures.yml')->getObjects();

        foreach($objectSet as $object)
        {
            $manager->persist($object);
        }

        $manager->flush();
    }
}