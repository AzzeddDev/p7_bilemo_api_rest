<?php

namespace App\Serializer;

use App\Entity\Customer;
use App\Entity\User;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Exception\CircularReferenceException;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Exception\LogicException;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class UserNormalizer implements NormalizerInterface
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
        $data['href']['self'] = $this->urlGenerator->generate('detailCustomer', ['id'=>$object->getId()]);
        $data['href']['update'] = $this->urlGenerator->generate('updateCustomer', ['id'=>$object->getId()]);
        $data['href']['delete'] = $this->urlGenerator->generate('deleteCustomer', ['id'=>$object->getId()]);

        return $data;
    }

    public function supportsNormalization($data, string $format = null)
    {
        return $data instanceof Customer;
    }
}