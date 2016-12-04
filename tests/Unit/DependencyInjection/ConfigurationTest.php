<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2016 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\SonataAdminIntegrationBundle\Tests\Unit\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionConfigurationTestCase;
use Symfony\Cmf\Bundle\SonataAdminIntegrationBundle\DependencyInjection\CmfSonataAdminIntegrationExtension;
use Symfony\Cmf\Bundle\SonataAdminIntegrationBundle\DependencyInjection\Configuration;
use Symfony\Cmf\Bundle\SonataAdminIntegrationBundle\DependencyInjection\Factory;

/**
 * This test will try to cover all configs.
 *
 * Means check if all available formats are equal.
 *
 * @author Maximilian Berghoff <Maximilian.Berghoff@gmx.de>
 */
class ConfigurationTest extends AbstractExtensionConfigurationTestCase
{
    protected function getContainerExtension()
    {
        return new CmfSonataAdminIntegrationExtension();
    }

    protected function getConfiguration()
    {
        return new Configuration([
            'seo' => new Factory\SeoAdminFactory(),
        ]);
    }

    public function testDefaultsForAllConfigFormats()
    {
        $expectedConfiguration = [
            'bundles' => [
                'seo' => [
                    'enabled' => true,
                    'form_group' => 'seo_form',
                ],
            ],
            'extensions' => [
                'publishable' => ['form_group' => 'form.group_publish_workflow'],
                'publish_time' => ['form_group' => 'form.group_publish_workflow'],
                'translatable' => ['form_group' => 'form.group_general'],
            ]
        ];

        $sources = array_map(function ($path) {
            return __DIR__.'/../../Resources/Fixtures/'.$path;
        }, array(
            'config/config.yml',
            'config/config.php',
            'config/config.xml',
        ));

        $this->assertProcessedConfigurationEquals($expectedConfiguration, $sources);
    }
}
