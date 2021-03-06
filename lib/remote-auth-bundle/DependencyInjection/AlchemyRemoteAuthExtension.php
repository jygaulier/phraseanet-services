<?php

namespace Alchemy\RemoteAuthBundle\DependencyInjection;

use Alchemy\RemoteAuthBundle\Security\LoginFormAuthenticator;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class AlchemyRemoteAuthExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yaml');

        if ('test' === $container->getParameter('kernel.environment')) {
            $loader->load('services_test.yaml');
        }

        foreach ($config['login_forms'] as $name => $loginForm) {
            $def = new ChildDefinition(LoginFormAuthenticator::class);
            $def->setArgument('$routeName', $loginForm['route_name']);
            $def->setAbstract(false);
            $def->setPublic(true);
            $def->setArgument('$defaultTargetPath', $loginForm['default_target_path']);
            $container->setDefinition('alchemy_remote.login_form.'.$name, $def);
        }
    }
}
