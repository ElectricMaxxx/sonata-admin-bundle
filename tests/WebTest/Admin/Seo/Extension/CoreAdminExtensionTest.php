<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2016 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\SonataAdminIntegrationBundle\Tests\WebTest\Admin\Seo\Extension;

use Symfony\Cmf\Bundle\SonataAdminIntegrationBundle\Tests\WebTest\Admin\TestCase;

/**
 * This test will cover all behavior with the provides admin extension.
 *
 * @author Maximilian Berghoff <Maximilian.Berghoff@gmx.de>
 */
class CoreAdminExtensionTest extends TestCase
{
    public function setUp()
    {
        $this->db('PHPCR')->loadFixtures(array(
            'Symfony\Cmf\Bundle\SonataAdminIntegrationBundle\Tests\Resources\DataFixtures\Phpcr\LoadCoreData',
        ));
        $this->client = $this->createClient();
    }

    public function testAdminExtensionExists()
    {
        $crawler = $this->client->request('GET', '/admin/cmf/core/extensions/list');

        $this->assertResponseSuccess($this->getClient()->getResponse());
        $this->assertCount(1, $crawler->filter('html:contains("with-extensions")'));
    }

    public function testItemEditView()
    {
        $crawler = $this->getClient()->request('GET', '/admin/cmf/core/extensions/test/core/with-extensions/edit');

        $this->assertResponseSuccess($this->getClient()->getResponse());

        $this->assertCount(1, $crawler->filter('html:contains("FORM_publish")'));
        $this->assertCount(1, $crawler->filter('html:contains("FORM_publish_time")'));
    }

    public function testItemCreate()
    {
        $crawler = $this->client->request('GET', '/admin/cmf/core/extensions/create');

        $this->assertResponseSuccess($this->getClient()->getResponse());

        $this->assertCount(1, $crawler->filter('html:contains("FORM_publish")'));
        $this->assertCount(1, $crawler->filter('html:contains("FORM_publish_time")'));
    }
}
