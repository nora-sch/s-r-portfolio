<?php

namespace App\Serializer;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerAwareInterface;
use Symfony\Component\Serializer\SerializerAwareTrait;

class UserAttributeNormalizer
implements ContextAwareNormalizerInterface, SerializerAwareInterface
{
    use SerializerAwareTrait;

    const USER_ATTRIBUTE_NORMALIZER_ALREADY_CALLED = 'USER_ATTRIBUTE_NORMALIZER_ALREADY_CALLED';

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }
    
    /**
     * @param array $context options that normalizers have access to
     */
    public function supportsNormalization(
        $data,
        $format = null,
        array $context = []
    ) {
        if (isset($context[self::USER_ATTRIBUTE_NORMALIZER_ALREADY_CALLED])) {
            return false;
        }

        return $data instanceof User;
    }

    /**
     * Normalizes an object unto a set of arrays/scalars.
     * @param mixed $object                 Object to normalize
     * @param string $format                Format the normalisation result will be encoded as
     * @param string $context               Context options for the normalizer
     * @return array|string|int|float|bool
     * @throws InvalidArgumentException     Occurs when the object given is not an attempted type for the normalizer
     * @throws CircularReferenceException   Occurs when the normalizer detects a circular reference when no circular reference handler can fix it
     * @throws LoficException               Occurs when the normalizer is not called in an expected context
     */
    public function normalize($object, $format = null, array $context = [])
    {
        if ($this->isUserHimself($object)) {
            $context['groups'][] = 'get-owner';
        }

        // Now continue with serialization
        return $this->passOn($object, $format, $context);
    }

    private function isUserHimself($object)
    {
        return $object->getUsername() === $this->tokenStorage->getToken()->getUser()->getUsername();
    }

    private function passOn($object, $format, $context)
    {
        if (!$this->serializer instanceof NormalizerInterface) {
            throw new \LogicException(
                sprintf(
                    'Cannot normalize object "%s" because the injected serializer is not a normalizer.',
                    $object
                )
            );
        }

        $context[self::USER_ATTRIBUTE_NORMALIZER_ALREADY_CALLED] = true;

        return $this->serializer->normalize($object, $format, $context);
    }
}
