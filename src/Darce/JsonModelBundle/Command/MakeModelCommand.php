<?php declare(strict_types=1);

namespace App\Darce\JsonModelBundle\Command;

use App\Darce\JsonModelBundle\Service\FileGenerator;
use App\Darce\JsonModelBundle\Service\ModelGenerator;
use App\Darce\JsonModelBundle\Utils\JsonUtil;
use InvalidArgumentException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class MakeModelCommand extends Command
{
    public const DEFAULT_RELATIVE_PATH = 'src/Model/Api';
    public const DEFAULT_NAMESPACE = 'App\\Model\\Api';

    /**
     * @var ModelGenerator
     */
    private $modelGenerator;

    /**
     * @var FileGenerator
     */
    private $fileGenerator;

    public function __construct(ModelGenerator $modelGenerator, FileGenerator $fileGenerator)
    {
        parent::__construct('darce-json-model:make-model');
        $this->modelGenerator = $modelGenerator;
        $this->fileGenerator = $fileGenerator;
    }

    protected function configure(): void
    {
        $this
            ->addArgument(
                'json',
                InputArgument::REQUIRED,
                'Absolute path of json file to parse'
            )
            ->addArgument(
                'class_name',
                InputArgument::REQUIRED,
                'Name of the root class created'
            )
            ->addArgument(
                'relative_path',
                InputArgument::OPTIONAL,
                'Relative path where the files are created',
                self::DEFAULT_RELATIVE_PATH
            )
            ->addArgument(
                'namespace',
                InputArgument::OPTIONAL,
                'Namespace of classes (Careful! Insert only one "\" as separator)',
                self::DEFAULT_NAMESPACE
            )
            ->addOption(
                'strict-type',
                'st',
                InputOption::VALUE_NONE
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $jsonPath = $input->getArgument('json');
        $className = $input->getArgument('class_name');
        $relativePath = $input->getArgument('relative_path');
        $namespace = $input->getArgument('namespace');

        $strictType = $input->getOption('strict-type');

        try{
            $json = JsonUtil::obtainJsonFromFile($jsonPath);
            JsonUtil::isValidJson($json);
        }catch (InvalidArgumentException $exception){
            $output->writeln($exception->getMessage());
            return 1;
        }

        $phpNamespace = $this->modelGenerator->generateNameSpace($namespace);
        $this->modelGenerator->generateClass($phpNamespace, $className, $json);

        $classes = $this->modelGenerator->getGeneratedClass();
        foreach ($classes as $class){
            $this
                ->fileGenerator
                ->generateClassFile(
                    $class,
                    $class->getName(),
                    $relativePath,
                    $strictType
                );
        }

        return 0;
    }
}