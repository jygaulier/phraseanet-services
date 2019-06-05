<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\FormSchema;
use Doctrine\ORM\EntityManagerInterface;

class FormSchemaManager
{
    const FALLBACK_SCHEMA_FILE = __DIR__.'/../../config/liform-schema.json';

    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function loadSchema(?string $locale): array
    {
        $formSchema = $this
            ->em
            ->getRepository(FormSchema::class)
            ->getSchemaForLocale($locale);

        if (null === $formSchema) {
            return json_decode(file_get_contents(self::FALLBACK_SCHEMA_FILE), true);
        }

        return $formSchema->getData();
    }

    public function persistSchema(?string $locale, array $schema): void
    {
        $this
            ->em
            ->getRepository(FormSchema::class)
            ->persistSchema($locale, $schema);
    }
}
