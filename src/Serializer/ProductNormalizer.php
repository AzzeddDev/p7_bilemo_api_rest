<?php

namespace App\Serializer;

use App\Entity\Product;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Exception\CircularReferenceException;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Exception\LogicException;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class ProductNormalizer implements NormalizerInterface
{
    private UrlGeneratorInterface $urlGenerator;
    private ObjectNormalizer $normalizer;

    public function __construct(ObjectNormalizer $normalizer, UrlGeneratorInterface $urlGenerator)
    {
        $this->normalizer = $normalizer;
        $this->urlGenerator = $urlGenerator;
    }

    public function normalize($object, string $format = null, array $context = [])
    {
        $data = $this->normalizer->normalize($object, $format, $context);
        $data['href']['self'] = $this->urlGenerator->generate('detailProduct', ['id'=>$object->getId()]);

        return $data;
    }

    public function supportsNormalization($data, string $format = null)
    {
        return $data instanceof Product;
    }
}