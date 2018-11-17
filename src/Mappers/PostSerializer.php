<?php


namespace App\Mappers;

use App\Entity\Post;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\VarDumper\VarDumper;

/**
 * Ideja je da se ova klasa koristi za
 * serijalizaciju i deserializaciju korisnika,
 * kao i da ovde definisemo mapiranje.
 * Mapiranje nam je bitno da sve u vezi usera izdvojimo u klase, jer je
 * to dobra praksa objektnog programiranja. Dakle, za svaki entitet
 * imacemo enitet, repository (gde cemo pisati upite), i serializer.
 */
class PostSerializer
{

    public static $POSTS_MAPPING = array(
        'attributes' =>
            array(
                'id',
                'title',
                'slug',
                'summary',
                'content',
                'publishedAt',
                'author'=> ['id', 'fullName', 'username'],
                'tags'=> ['id', 'name'],
                'comments'=> ['id', 'content', 'publishedAt',
                    'author'=> ['id', 'fullName']
                ]
            )
    );

    public static $POST_NEW_MAPPING = array(
        'attributes' =>
            array(
                'id',
                'title',
                'slug',
                'summary',
                'content',
                'author'=> ['id'],
                'tags' => ['name']
            )
    );
    /**
     * Ovde kreiramo serializer.
     * Svaki serializer se sastoji od normalizera i enddodera.
     * Vise na @link https://symfony.com/doc/current/components/serializer.html
     *
     * @return Serializer
     */
    public static function createSerializer()
    {
        /**
         * 1. kreiramo ObjectNormalizer
         */
        $normalizer = new ObjectNormalizer(null, null, null, new ReflectionExtractor());

        /**
         * 2. Kreiramo encodere, hocemo i xml i json
         */
        $jsonEncoder = new JsonEncoder();
        $xmlEncoder = new XmlEncoder();
        /**
         * Trebaju nam 3 normalizera jer imamo datume npr. publishedAt
         * Hocemo da radimo i sa vezama gde su vise pa nam treba i ArrayDenormalizer, npr. post ima vise komentara
         */
        $normalizers = array(new DateTimeNormalizer(), $normalizer, new ArrayDenormalizer());

        $serializer = new Serializer($normalizers, array($jsonEncoder, $xmlEncoder));

        return $serializer;
    }

    public static function serializePosts($posts, $format)
    {
        return self::createSerializer()->serialize($posts, $format, self::$POSTS_MAPPING);

    }

    public static function deserializeNewPost($data, $format)
    {
        return self::createSerializer()->deserialize($data, Post::class, $format, self::$POST_NEW_MAPPING);

    }

    public static function deserializeEditPost($data, $format, Post $postForEdit)
    {
        $editMapping = self::$POST_NEW_MAPPING;
        $editMapping['attributes']['tags'] = ['id', 'name'];
        $editMapping['object_to_populate'] = $postForEdit;
        return self::createSerializer()->deserialize($data, Post::class, $format, $editMapping);

    }
}
