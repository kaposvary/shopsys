<?php

namespace Shopsys\ShopBundle\Component\Image;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Shopsys\ShopBundle\Component\Image\Config\ImageConfig;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ImageDeleteDoctrineListener
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    private $container;

    /**
     * @var \Shopsys\ShopBundle\Component\Image\Config\ImageConfig
     */
    private $imageConfig;

    public function __construct(
        ContainerInterface $container,
        ImageConfig $imageConfig
    ) {
        $this->container = $container;
        $this->imageConfig = $imageConfig;
    }

    /**
     * Prevent ServiceCircularReferenceException (DoctrineListener cannot be dependent on the EntityManager)
     *
     * @return \Shopsys\ShopBundle\Component\Image\ImageFacade
     */
    private function getImageFacade()
    {
        return $this->container->get('shopsys.shop.component.image.image_facade');
    }

    /**
     * @param \Doctrine\ORM\Event\LifecycleEventArgs $args
     */
    public function preRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($this->imageConfig->hasImageConfig($entity)) {
            $this->deleteEntityImages($entity, $args->getEntityManager());
        } elseif ($entity instanceof Image) {
            $this->getImageFacade()->deleteImageFiles($entity);
        }
    }

    /**
     * @param object $entity
     * @param \Doctrine\ORM\EntityManager $em
     * @param \Shopsys\ShopBundle\Component\Image\ImageFacade $imageFacade
     */
    private function deleteEntityImages($entity, EntityManager $em)
    {
        $images = $this->getImageFacade()->getAllImagesByEntity($entity);
        foreach ($images as $image) {
            $em->remove($image);
        }
    }
}
