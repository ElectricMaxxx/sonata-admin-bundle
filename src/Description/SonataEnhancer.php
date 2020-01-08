<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2017 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\SonataPhpcrAdminIntegrationBundle\Description;

use Doctrine\Common\Util\ClassUtils;
use Sonata\AdminBundle\Admin\Pool;
use Symfony\Cmf\Component\Resource\Description\Description;
use Symfony\Cmf\Component\Resource\Description\DescriptionEnhancerInterface;
use Symfony\Cmf\Component\Resource\Description\Descriptor;
use Symfony\Cmf\Component\Resource\Puli\Api\PuliResource;
use Symfony\Cmf\Component\Resource\Repository\Resource\CmfResource;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Add links and meta-info from Sonata Admin.
 *
 * @author Daniel Leech <daniel@dantleech.com>
 *
 * @internal
 */
class SonataEnhancer implements DescriptionEnhancerInterface
{
    /**
     * @var Pool
     */
    private $pool;

    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    private $adminMap = [];

    private static $linkKeyMapping = [
        'list' => Descriptor::LINK_LIST_HTML,
        'create' => Descriptor::LINK_CREATE_HTML,
        'edit' => Descriptor::LINK_EDIT_HTML,
        'delete' => Descriptor::LINK_REMOVE_HTML,
        'show' => Descriptor::LINK_SHOW_HTML,
    ];

    /**
     * @param Pool                  $pool
     * @param UrlGeneratorInterface $urlGenerator
     */
    public function __construct(Pool $pool, UrlGeneratorInterface $urlGenerator)
    {
        $this->pool = $pool;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * {@inheritdoc}
     */
    public function enhance(Description $description)
    {
        $object = $description->getResource()->getPayload();

        // sonata has dependency on ClassUtils so this is fine.
        $class = ClassUtils::getClass($object);
        $admin = $this->getAdminByClass($class);

        $routeCollection = $admin->getRoutes();

        foreach ($routeCollection->getElements() as $code => $route) {
            $routeName = $route->getDefault('_sonata_name');
            $url = $this->urlGenerator->generate($routeName, [
                $admin->getIdParameter() => $admin->getUrlsafeIdentifier($object),
            ], true);

            $parts = explode('.', $code);
            $linkKey = end($parts);

            if (false !== $linkKey && array_key_exists($linkKey, self::$linkKeyMapping)) {
                $description->set(self::$linkKeyMapping[$linkKey], $url);
            }
        }

        $description->set(Descriptor::PAYLOAD_TITLE, $admin->toString($object));
        $description->set(Descriptor::TYPE_ALIAS, $admin->getLabel());
    }

    /**
     * {@inheritdoc}
     */
    public function supports(PuliResource $resource)
    {
        if (!$resource instanceof CmfResource) {
            return false;
        }

        $payload = $resource->getPayload();

        // sonata has dependency on ClassUtils so this is fine.
        $class = ClassUtils::getClass($payload);

        return null !== $this->getAdminByClass($class);
    }

    private function getAdminByClass($class)
    {
        if (array_key_exists($class, $this->adminMap)) {
            return $this->adminMap[$class];
        }

        $_class = $class;
        while ($_class && !$this->pool->hasAdminByClass($_class)) {
            $_class = get_parent_class($_class);
        }

        $this->adminMap[$class] = $_class ? $this->pool->getAdminByClass($_class) : null;

        return $this->adminMap[$class];
    }
}
