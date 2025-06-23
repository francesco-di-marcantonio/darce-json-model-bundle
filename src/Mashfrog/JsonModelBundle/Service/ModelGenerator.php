<?php declare(strict_types=1);

namespace App\Darce\JsonModelBundle\Service;

use App\Darce\JsonModelBundle\Utils\DateUtil;
use App\Darce\JsonModelBundle\Utils\StringUtil;
use DateTime;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\PhpNamespace;

class ModelGenerator
{
    /**
     * @var ClassType[]
     */
    private $generatedClass;

    public function __construct()
    {
        $this->generatedClass = [];
    }

    /**
     * @param string $namespace
     * @return PhpNamespace
     */
    public function generateNameSpace(string $namespace): PhpNamespace
    {
        $phpNamespace = new PhpNamespace($namespace);
        $phpNamespace->addUse('JMS\Serializer\Annotation', 'Serializer');

        return $phpNamespace;
    }

    /**
     * @param PhpNamespace $phpNamespace
     * @param string $className
     * @param string $json
     * @return ClassType
     */
    public function generateClass(PhpNamespace $phpNamespace, string $className, string $json): ClassType
    {
        $class = new ClassType($className, $phpNamespace);
        $jsonObject = json_decode($json, false);
        foreach ($jsonObject as $key => $value) {

            $type = gettype($value);
            if ($type === 'NULL') {
                $type = 'string';
            }

            if ($type === 'string' && $value !== null) {
                $datetime = DateUtil::recognizeString($value);
                if ($datetime !== null) {
                    $type = DateTime::class;
                    $phpNamespace->addUse(DateTime::class);
                }
            }

            if(is_array($value)){
                if(count($value) === 0){
                    $class->addComment(sprintf('@see Argument "%s" skipped because is an empty array', $key));
                    continue;
                }

                if(count($value) > 0 && is_object($value[0]) === false){
                    $this->addProperty($class, $key, $type, $type);
                }else{
                    $this->addArrayOfObjectAsProperty($phpNamespace, $class, $key, json_encode($value[0]));
                }
            } elseif (!is_object($value)) {
                $this->addProperty($class, $key, $type, $type);
            } else {

                $this->addObjectAsProperty($phpNamespace, $class, $key, json_encode($value));
            }
        }

        $this->addGeneratedClass($class);

        return $class;
    }

    /**
     * @return ClassType[]
     */
    public function getGeneratedClass(): array
    {
        return $this->generatedClass;
    }

    /**
     * @param ClassType $classType
     */
    private function addGeneratedClass(ClassType $classType): void
    {
        $this->generatedClass[] = $classType;
    }

    /**
     * @param ClassType $class
     * @param string $key
     * @param string $className
     * @param string $fullClassName
     */
    private function addProperty(ClassType $class, string $key, string $className, string $fullClassName): void
    {
        $class
            ->addProperty(StringUtil::toCamelCase($key))
            ->setVisibility('private')
            ->addComment(sprintf('@var %s', $className))
            ->addComment(sprintf('@Serializer\Type("%s")', $fullClassName))
            ->addComment(sprintf('@Serializer\SerializedName("%s")', $key));

        $class
            ->addMethod(sprintf('get%s', StringUtil::methodNameToCamelCase($key)))
            ->addBody(sprintf('return $this->%s;',StringUtil::toCamelCase($key)))
            ->addComment(sprintf('@return %s', $className))
        ;
    }

    /**
     * @param PhpNamespace $phpNamespace
     * @param ClassType $class
     * @param string $propertyName
     * @param string $jsonValue
     */
    private function addObjectAsProperty(PhpNamespace $phpNamespace, ClassType $class, string $propertyName, string $jsonValue): void
    {
        $innerClassType = $this->generateClass($phpNamespace, ucfirst(StringUtil::toCamelCase($propertyName)), $jsonValue);
        $innerClassName = $innerClassType->getName();
        $fullInnerClassName = $phpNamespace->getName() . '\\' . $innerClassName;
        $this->addProperty($class, $propertyName, $innerClassName, $fullInnerClassName);
    }

    /**
     * @param PhpNamespace $phpNamespace
     * @param ClassType $class
     * @param string $propertyName
     * @param string $jsonValue
     */
    private function addArrayOfObjectAsProperty(PhpNamespace $phpNamespace, ClassType $class, string $propertyName, string $jsonValue): void
    {
        $innerClassType = $this->generateClass($phpNamespace, ucfirst(StringUtil::toCamelCase(StringUtil::singularize($propertyName))), $jsonValue);
        $innerClassName = $innerClassType->getName();
        $fullInnerClassName = $phpNamespace->getName() . '\\' . $innerClassName;
        $this->addProperty($class, $propertyName, sprintf('%s[]', $innerClassName), sprintf('array<%s>', $fullInnerClassName));
    }
}