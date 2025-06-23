<?php declare(strict_types=1);

namespace App\Darce\JsonModelBundle\Tests\Functional\Command;

use App\Darce\JsonModelBundle\Command\MakeModelCommand;
use App\Darce\JsonModelBundle\Service\FileGenerator;
use App\Darce\JsonModelBundle\Service\ModelGenerator;
use ReflectionClass;
use ReflectionException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class MakeModelCommandTest extends KernelTestCase
{
    /**
     * @var ModelGenerator|object
     */
    private $generator;

    /**
     * @var FileGenerator|object
     */
    private $fileMaker;

    /**
     * @var string
     */
    private $relativePath;

    /**
     * @var string
     */
    private $namespace;

    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();
        $this->generator = self::$container->get(ModelGenerator::class);
        $this->fileMaker = self::$container->get(FileGenerator::class);
        $this->relativePath = 'src/Darce/JsonModelBundle/Tests/Model';
        $this->namespace = 'App\Darce\JsonModelBundle\Tests\Model';
    }

    public function testExecute(): void
    {
        $command = new MakeModelCommand($this->generator, $this->fileMaker);
        $commandTester = new CommandTester($command);

        $className = 'Bar';
        $commandTester->execute(
            [
                'json' => __DIR__ . '/../../data/valid.json',
                'class_name' => $className,
                'relative_path' => $this->relativePath,
                'namespace' => $this->namespace
            ],
            [
                'strict-type' => false
            ]
        );

        try{
            $class = $this->namespace . '\\' . $className;
            $reflectedClass = new ReflectionClass($class);

            $id = $reflectedClass->getProperty('id');
            $this->assertTrue($id->isPrivate());

        }catch (ReflectionException $exception){

        }
    }
}