<?php declare(strict_types=1);

namespace App\Darce\JsonModelBundle\Utils;

use InvalidArgumentException;

final class JsonUtil
{
    /**
     * Read the content of
     * @param string $jsonPath
     * @return string
     * @throws InvalidArgumentException
     */
    public static function obtainJsonFromFile(string $jsonPath): string
    {
        if(file_exists($jsonPath) === false){
            throw new InvalidArgumentException(sprintf('File not found in %s. Verify the paht and try again', $jsonPath));
        }

        $json = file_get_contents($jsonPath);
        if($json === false){
            throw new InvalidArgumentException(sprintf('An error occourred while reading the file %s', $jsonPath));
        }

        return $json;
    }

    /**
     * @param string $string
     * @throws InvalidArgumentException
     */
    public static function isValidJson(string $string): void
    {
        json_decode($string, true);
        if (json_last_error() !== JSON_ERROR_NONE){
            throw new InvalidArgumentException(sprintf('Json %s is not a valid json', $string));
        }
    }
}