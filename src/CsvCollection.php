<?php

namespace DutchCodingCompany\CsvCollection;

use Illuminate\Support\LazyCollection;

class CsvCollection extends LazyCollection
{
    /**
     * The collection's default options.
     *
     * @var array
     */
    public static array $defaults = [
        'delimiter' => ',',
        'enclosure' => '"',
        'escape' => '\\',
        'header' => true,
    ];

    /**
     * The collection's options.
     *
     * @var array
     */
    public array $options = [];

    /**
     * Create a new csv collection instance.
     *
     * @param mixed $source
     * @return void
     */
    public function __construct($source = null)
    {
        parent::__construct($source);

        $this->options(static::$defaults);
    }

    /**
     * Load the csv file items into a new collection.
     *
     * @param string $file
     * @param array $options
     * @return static
     */
    public function open(string $file, array $options = []): self
    {
        $options = array_merge(
            $this->options, $options
        );

        return static::make(static function () use ($file, $options) {
            $resource = fopen($file, 'r');

            $read = static fn() => fgetcsv(
                $resource, 0,
                $options['delimiter'],
                $options['enclosure'],
                $options['escape'],
            );

            $header = null;

            // Loop over the rows and yield them into a generator.
            while (($line = $read()) !== false) {
                if (! ($options['header'])) {
                    yield $line;
                    continue;
                }

                if (! $header) {
                    $header = $line;
                    continue;
                }

                yield array_combine($header, $line);
            }

            fclose($resource);
        });
    }

    /**
     * Save the collection items to the csv file.
     *
     * @param string|null $file
     * @param array $options
     * @return $this
     */
    public function save(string $file, array $options = []): self
    {
        $options = array_merge(
            $this->options, $options
        );

        $resource = fopen($file, 'w');

        $write = static fn(array $line) => fputcsv(
            $resource, $line,
            $options['delimiter'],
            $options['enclosure'],
            $options['escape'],
        );

        if ($options['header']) {
            $write(array_keys($this->first()));
        }

        $this->each($write);

        fclose($resource);

        return $this;
    }

    /**
     * Push an item into the collection and save the item to the csv file.
     *
     * @param string|null $file
     * @param array $line
     * @param array $options
     * @return \App\CsvCollection
     */
    public function push(string $file, array $line, array $options = []): self
    {
        $options = array_merge(
            $this->options, $options
        );

        $resource = fopen($file, 'a');

        // Lock the file.
        if (! flock($resource, LOCK_EX)) {
            throw new IOException("Could not lock file");
        }

        $write = static fn(array $line) => fputcsv(
            $resource, $line,
            $options['delimiter'],
            $options['enclosure'],
            $options['escape'],
        );

        if ($options['header'] && $this->open($file)->count() === 0) {
            $write(array_keys($line));
        }

        $write($line);

        // Unlock the file.
        flock($resource, LOCK_UN);
        fclose($resource);

        return $this->open($file, $options);
    }

    /**
     * @param string $file Path to the CSV file
     * @return string Delimiter
     */
    public static function detectDelimiter(string $file): string
    {
        $delimiters = [";" => 0, "," => 0, "\t" => 0, "|" => 0];

        $handle = fopen($file, "r");
        $firstLine = fgets($handle);
        fclose($handle);
        foreach ($delimiters as $delimiter => &$count) {
            $count = count(str_getcsv($firstLine, $delimiter));
        }

        return array_search(max($delimiters), $delimiters, true);
    }

    /**
     * Set the collection's options.
     *
     * @param array $options
     * @return static
     */
    public function options(array $options): self
    {
        $this->options = array_merge(
            $this->options, $options
        );

        return $this;
    }

    /**
     * Set the collection's default options.
     *
     * @param array $options
     * @return void
     */
    public static function defaults(array $options): void
    {
        static::$defaults = array_merge(
            static::$defaults, $options
        );
    }
}
