<?php declare(strict_types=1);

namespace App\Darce\JsonModelBundle\Service;

use Nette\PhpGenerator\ClassType;
use Symfony\Component\Filesystem\Filesystem;

class FileGenerator
{
    /**
     * @var Filesystem
     */
    private $fs;

    /**
     * @var string
     */
    private $projectDir;

    public function __construct(Filesystem $fs, string $projectDir)
    {
        $this->fs = $fs;
        $this->projectDir = $projectDir;
    }

    /**
     * @param ClassType $classType
     * @param string $className
     * @param string $relativePath
     * @param bool $useStrictType
     */
    public function generateClassFile(
        ClassType $classType,
        string $className,
        string $relativePath,
        bool $useStrictType = true
    ): void
    {
        $this->createDirIfNotExist($relativePath);
        $content = $this->getContent($classType, $useStrictType);

        $this
            ->fs
            ->dumpFile(
                sprintf(
                    '%s/%s/%s.php',
                    $this->projectDir,
                    $relativePath,
                    $className
                ),
                $content);
    }

    /**
     * @param string $relativePath
     * @return string
     */
    public function getAbsolutePath(string $relativePath): string
    {
        return sprintf('%s%s%s', $this->projectDir, DIRECTORY_SEPARATOR, $relativePath);
    }

    /**
     * @param string $relativePath
     */
    private function createDirIfNotExist(string $relativePath): void
    {
        $absolutePath = $this->getAbsolutePath($relativePath);
        $this->fs->mkdir($absolutePath);
    }

    /**
     * @param ClassType $classType
     * @param bool $useStrictType
     * @return string
     */
    private function getContent(ClassType $classType, bool $useStrictType): string
    {
        //Concatenare il namespace della classe alla classe stessa è necessario per produrre file php contenenti entrambi.
        $namespace = $classType->getNamespace();

        if($useStrictType === true && PHP_MAJOR_VERSION >= 7){
            return sprintf('<?php declare(strict_types=1);%s%s%s%s', PHP_EOL, PHP_EOL, $namespace, $classType);
        }

        return sprintf('<?php %s%s%s', PHP_EOL, $namespace, $classType);
    }
}