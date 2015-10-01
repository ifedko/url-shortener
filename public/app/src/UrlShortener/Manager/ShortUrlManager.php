<?php

namespace UrlShortener\Manager;

use AppCore\Database\DatabaseAdapterInterface;
use AppCore\Config;
use UrlShortener\Entity\ShortUrl;
use UrlShortener\Manager\Exception\EntityManagerException;

class ShortUrlManager
{
    const TABLE_NAME = 'url_shortener';
    protected static $shortUrlChars = 'abcdefjhijklmnoprstuvwxyzABCDEFGHIJKLMNOPRSTUVWXYZ0123456789';

    /**
     * @var DatabaseAdapterInterface
     */
    private $dbAdapter;

    /**
     * @var string
     */
    private $baseUrl;

    /**
     * @var int
     */
    private $urlMaxLength;

    /**
     * @param DatabaseAdapterInterface $dbAdapter
     */
    public function __construct(DatabaseAdapterInterface $dbAdapter, Config $config)
    {
        $this->dbAdapter = $dbAdapter;
        $this->baseUrl = $config->getParameter('base_url', 'url_shortener');
        $this->urlMaxLength = $config->getParameter('url_max_length', 'url_shortener');
    }

    /**
     * @param array $properties
     * @return ShortUrl
     * @throws EntityManagerException
     */
    protected function createFromArray(array $properties)
    {
        $shortUrl = new ShortUrl();

        $this->validateProperties($properties);

        $shortUrl->setSourceUrl($properties['source_url']);

        if (isset($properties['short_code'])) {
            $shortUrl->setShortCode($properties['short_code']);
        }
        if (isset($properties['id'])) {
            $shortUrl->setId($properties['id']);
        }
        if (isset($properties['counter'])) {
            $shortUrl->setCounter($properties['counter']);
        }

        return $shortUrl;
    }

    /**
     * @param array $properties
     * @throws EntityManagerException
     */
    protected function validateProperties(array $properties)
    {
        $requiredProperties = ['source_url'];

        foreach ($requiredProperties as $property) {
            if (empty($properties[$property])) {
                throw new EntityManagerException(sprintf('There is no required property "%s"', $property));
            }
        }
    }

    /**
     * @param ShortUrl $shortUrl
     * @return int
     */
    public function insert(ShortUrl $shortUrl)
    {
        $connection = $this->dbAdapter->getConnection();
        $sql = "INSERT INTO " . self::TABLE_NAME . " (source_url) VALUES (:source_url)";
        $statement = $connection->prepare($sql);
        $statement->execute([
            ':source_url' => $shortUrl->getSourceUrl(),
        ]);

        return $connection->lastInsertId();
    }

    /**
     * @param ShortUrl $shortUrl
     * @return int
     */
    public function update(ShortUrl $shortUrl)
    {
        // @todo refactoring
        $fields = [];
        $values = [];
        if ($shortUrl->getSourceUrl() != null) {
            $fields[] = 'source_url=:source_url';
            $values[':source_url'] = $shortUrl->getSourceUrl();
        }
        if ($shortUrl->getShortCode() != null) {
            $fields[] = 'short_code=:short_code';
            $values[':short_code'] = $shortUrl->getShortCode();
        }
        if ($shortUrl->getCounter() != null) {
            $fields[] = 'counter=:counter';
            $values[':counter'] = $shortUrl->getCounter();
        }
        $values[':id'] = $shortUrl->getId();

        $connection = $this->dbAdapter->getConnection();
        $sql = "UPDATE " . self::TABLE_NAME . " SET " . implode(',', $fields) . " WHERE id = :id";
        $statement = $connection->prepare($sql);
        $statement->execute($values);

        return $connection->lastInsertId();
    }

    /**
     * @param string $url
     * @return ShortUrl|null
     */
    public function findBySourceUrl($url)
    {
        $connection = $this->dbAdapter->getConnection();
        $sql = "SELECT * FROM " . self::TABLE_NAME . " WHERE source_url = :source_url";
        $statement = $connection->prepare($sql);
        $statement->execute([':source_url' => $url]);

        $shortUrlRow = $statement->fetch();

        return (!empty($shortUrlRow)) ? $this->createFromArray($shortUrlRow) : null;
    }

    /**
     * @param string $code
     * @return ShortUrl|null
     */
    public function findByShortCode($code)
    {
        $connection = $this->dbAdapter->getConnection();
        $sql = "SELECT * FROM " . self::TABLE_NAME . " WHERE short_code = :short_code";
        $statement = $connection->prepare($sql);
        $statement->execute([':short_code' => $code]);

        $shortUrlRow = $statement->fetch();

        return (!empty($shortUrlRow)) ? $this->createFromArray($shortUrlRow) : null;
    }

    /**
     * @param string $sourceUrl
     * @return string
     * @throws EntityManagerException
     */
    public function createShortUrl($sourceUrl)
    {
        $sourceUrl = rtrim($sourceUrl, '/');

        if (filter_var($sourceUrl, FILTER_VALIDATE_URL) === false) {
            throw new EntityManagerException('Property "source_url" is not valid.');
        }

        if (strlen($sourceUrl) > $this->urlMaxLength) {
            throw new EntityManagerException(sprintf("Url is longer than %d characters", $this->urlMaxLength));
        }

        if ($this->isUrlShortenerUrl($sourceUrl)) {
            return $sourceUrl;
        }

        $shortUrl = $this->findBySourceUrl($sourceUrl);
        if (!$shortUrl) {
            /* @var ShortUrl $shortUrl */
            $shortUrl = $this->createFromArray(['source_url' => $sourceUrl]);
            $id = $this->insert($shortUrl);

            $shortCode = $this->generateShortCodeById($id);

            $shortUrl->setId($id);
            $shortUrl->setShortCode($shortCode);

            $this->update($shortUrl);
        }

        return $this->getShortUrlValue($shortUrl->getShortCode());
    }

    /**
     * @param int $id
     * @return string
     * @throws EntityManagerException
     */
    protected function generateShortCodeById($id)
    {
        $chars = str_split(self::$shortUrlChars);
        $length = count($chars);

        $code = '';
        while ($id > $length - 1) {
            $code .= $chars[fmod($id, $length)];
            $id = floor($id / $length);
        }

        $code = $chars[($id - 1)] . $code;

        if (strlen($code) <= 0) {
            throw new EntityManagerException('Cannot generate short code.');
        }

        return $code;
    }

    /**
     * @param string $shortCode
     * @return string
     */
    protected function getShortUrlValue($shortCode)
    {
        return  rtrim($this->baseUrl, '/') . '/' . $shortCode;
    }

    /**
     * @param string $shortCode
     * @return string
     */
    public function getSourceUrlByShortCode($shortCode)
    {
        $sourceUrl = '';
        $shortUrl = $this->findByShortCode($shortCode);

        if ($shortUrl) {
            $sourceUrl = $shortUrl->getSourceUrl();

            $counter = $shortUrl->getCounter();
            $counter++;
            $shortUrl->setCounter($counter);
            $this->update($shortUrl);
        }

        return $sourceUrl;
    }

    /**
     * @param string $sourceUrl
     * @return boolean
     */
    protected function isUrlShortenerUrl($sourceUrl)
    {
        $baseUrl = rtrim($this->baseUrl, '/');
        $pattern = "(" . $baseUrl . ")/([a-zA-Z0-9]{1,})([.])*";
        $pattern = "/^" . str_replace('/', '\/', $pattern) . "/";

        return preg_match($pattern, $sourceUrl);
    }
}

