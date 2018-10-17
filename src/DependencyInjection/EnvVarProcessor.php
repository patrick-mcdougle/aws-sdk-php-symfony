<?php

namespace Aws\Symfony\DependencyInjection;

use Aws\SecretsManager\Exception\SecretsManagerException;
use Aws\SecretsManager\SecretsManagerClient;
use Symfony\Component\DependencyInjection\EnvVarProcessorInterface;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

class EnvVarProcessor implements EnvVarProcessorInterface
{
    /** @var SecretsManagerClient */
    private $secretsClient;

    /**
     * EnvVarProcessor constructor.
     * @param SecretsManagerClient $client
     */
    public function __construct(SecretsManagerClient $client)
    {
        $this->secretsClient = $client;
    }


    /**
     * Returns the value of the given variable as managed by the current instance.
     *
     * @param string $prefix The namespace of the variable
     * @param string $name The name of the variable within the namespace
     * @param \Closure $getEnv A closure that allows fetching more env vars
     *
     * @return mixed
     *
     * @throws RuntimeException on error
     */
    public function getEnv($prefix, $name, \Closure $getEnv)
    {
        try {
            $secretValue = $this->secretsClient->getSecretValue([
                'SecretId' => $name,
            ]);
        } catch(SecretsManagerException $exception) {
            throw new RuntimeException('An Aws error occurred!', 0, $exception);
        }
        return $secretValue['SecretString'];
    }

    /**
     * @return string[] The PHP-types managed by getEnv(), keyed by prefixes
     */
    public static function getProvidedTypes()
    {
        return ['awsSecret' => 'string'];
    }
}
