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

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Symfony\Cmf\Bundle\SonataAdminIntegrationBundle\DependencyInjection\CmfSonataAdminIntegrationExtension;

class CmfSonataAdminExtensionTest extends AbstractExtensionTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function getContainerExtensions()
    {
        return array(
            new CmfSonataAdminIntegrationExtension(),
        );
    }

    public function testSeoBundle()
    {
        $this->container->setParameter(
            'kernel.bundles',
            array(
                'CmfSeoBundle' => true,
                'CmfRoutingBundle' => true,
                'SonataDoctrinePHPCRAdminBundle' => true,
                'DoctrinePHPCRBundle' => true,
                'BurgovKeyValueFormBundle' => true,
            )
        );

        $this->load([
            'bundles' => [
                'seo' => [
                    'enabled' => true,
                    'form_group' => 'seo_form',
                ],
            ],
        ]);

        $this->assertContainerBuilderHasParameter('cmf_sonata_admin_integration.seo.form_group', 'seo_form');
    }

    public function testBlockBundle()
    {
        $this->container->setParameter(
            'kernel.bundles',
            [
                'CmfBlockBundle' => true,
                'CmfMenuBundle' => true,
                'CmfRoutingBundle' => true,
                'SonataDoctrinePHPCRAdminBundle' => true,
                'DoctrinePHPCRBundle' => true,
            ]
        );

        $this->load([
            'bundles' => [
                'block' => [
                    'enabled' => true,
                    'use_imagine' => true,
                    'basepath' => 'basepath_value',
                    'menu_basepath' => 'menu_basepath_value',
                ],
            ],
        ]);

        $this->assertContainerBuilderHasParameter(
            'cmf_sonata_admin_integration.block.persistence.phpcr.basepath',
            'basepath_value'
        );
        $this->assertContainerBuilderHasParameter(
            'cmf_sonata_admin_integration.block.persistence.phpcr.menu_basepath',
            'menu_basepath_value'
        );

        $keys = [
            'persistence.phpcr.basepath' => 'basepath_value',
            'persistence.phpcr.menu_basepath' => 'menu_basepath_value',
        ];

        foreach ($keys as $suffix => $value) {
            $this->assertContainerBuilderHasParameter(
                'cmf_sonata_admin_integration.block.'.$suffix,
                $value
            );
        }
    }

    public function testCoreDefaults()
    {
        $this->container->setParameter(
            'kernel.bundles',
            array(
                'CmfRoutingBundle' => true,
                'SonataDoctrinePHPCRAdminBundle' => true,
                'DoctrinePHPCRBundle' => true,
                'CmfCoreBundle' => true,
            )
        );

        $this->load([
            'bundles' => [
                'core' => [
                    'enabled' => true,
                    'form_group' => 'core_form',
                    'extensions' => [
                        'publishable' => ['form_group' => 'publishable_form'],
                        'publish_time' => ['form_group' => 'publish_time_form'],
                        'translatable' => ['form_group' => 'translatable_form'],
                    ],
                ],
            ],
        ]);

        $this->assertContainerBuilderHasParameter('cmf_sonata_admin_integration.core.form_group', 'core_form');
        $this->assertContainerBuilderHasParameter(
            'cmf_sonata_admin_integration.core.publishable.form_group',
            'publishable_form'
        );
        $this->assertContainerBuilderHasParameter(
            'cmf_sonata_admin_integration.core.publish_time.form_group',
            'publish_time_form'
        );
        $this->assertContainerBuilderHasParameter(
            'cmf_sonata_admin_integration.core.translatable.form_group',
            'translatable_form'
        );

        $this->assertContainerBuilderHasService(
            'cmf_sonata_admin_integration.core.extension.publish_workflow.time_period'
        );
        $this->assertContainerBuilderHasService(
            'cmf_sonata_admin_integration.core.extension.publish_workflow.publishable'
        );
        $this->assertContainerBuilderHasService(
            'cmf_sonata_admin_integration.core.extension.child'
        );
    }
}
