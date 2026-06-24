<?php

declare(strict_types=1);

namespace App\DAL\Schema;

/**
 * Leest één tabel-XML-bestand uit naar een eenvoudige array-structuur.
 * Geen validatie tegen een XSD — bewust simpel gehouden, dit is een
 * hulpmiddel om sneller tabellen te kunnen toevoegen, geen generieke ORM.
 */
final class SchemaParser
{
    public function parseBestand(string $pad): array
    {
        $xml = simplexml_load_file($pad);
        if ($xml === false) {
            throw new \RuntimeException("Kan XML niet lezen: {$pad}");
        }

        $kolommen = [];
        foreach ($xml->column as $kolom) {
            $kolommen[] = [
                'naam' => (string) $kolom['name'],
                'type' => (string) $kolom['type'],
                'length' => isset($kolom['length']) ? (string) $kolom['length'] : null,
                'values' => isset($kolom['values']) ? (string) $kolom['values'] : null,
                'nullable' => $this->boolAttribuut($kolom, 'nullable', false),
                'default' => isset($kolom['default']) ? (string) $kolom['default'] : null,
                'autoIncrement' => $this->boolAttribuut($kolom, 'autoIncrement', false),
                'primaryKey' => $this->boolAttribuut($kolom, 'primaryKey', false),
                'unique' => $this->boolAttribuut($kolom, 'unique', false),
                'onUpdateCurrentTimestamp' => $this->boolAttribuut($kolom, 'onUpdateCurrentTimestamp', false),
                'references' => isset($kolom['references']) ? (string) $kolom['references'] : null,
                'onDelete' => isset($kolom['onDelete']) ? (string) $kolom['onDelete'] : 'RESTRICT',
                'onUpdate' => isset($kolom['onUpdate']) ? (string) $kolom['onUpdate'] : 'RESTRICT',
            ];
        }

        $uniekeSets = [];
        foreach ($xml->unique as $unique) {
            $uniekeSets[] = array_map('trim', explode(',', (string) $unique['columns']));
        }

        return [
            'naam' => (string) $xml['name'],
            'kolommen' => $kolommen,
            'uniekeSets' => $uniekeSets,
        ];
    }

    private function boolAttribuut(\SimpleXMLElement $element, string $naam, bool $standaard): bool
    {
        if (!isset($element[$naam])) {
            return $standaard;
        }
        return (string) $element[$naam] === 'true';
    }
}
