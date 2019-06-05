<?php

declare(strict_types=1);

namespace App\Consumer;

use App\Model\Commit;
use App\Storage\AssetManager;
use App\Storage\FileStorageManager;
use GuzzleHttp\Client;
use Mimey\MimeTypes;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;

class DownloadConsumer extends AbstractConsumer
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var FileStorageManager
     */
    private $storageManager;

    /**
     * @var AssetManager
     */
    private $assetManager;

    /**
     * @var ProducerInterface
     */
    private $commitProducer;

    public function __construct(
        FileStorageManager $storageManager,
        Client $client,
        AssetManager $assetManager,
        ProducerInterface $commitProducer
    ) {
        $this->client = $client;
        $this->storageManager = $storageManager;
        $this->assetManager = $assetManager;
        $this->commitProducer = $commitProducer;
    }

    protected function doExecute(array $message): int
    {
        $url = $message['url'];
        $userId = $message['user_id'];
        $formData = $message['form_data'];
        $response = $this->client->request('GET', $url);
        $headers = $response->getHeaders();
        $contentType = $headers['Content-Type'][0] ?? 'application/octet-stream';

        $originalName = basename(explode('?', $url, 2)[0]);
        if (isset($headers['Content-Disposition'][0])) {
            $contentDisposition = $headers['Content-Disposition'][0];
            if (preg_match('#\s+filename="(.+?)"#', $contentDisposition, $regs)) {
                $originalName = $regs[1];
            }
        }

        $extension = pathinfo($originalName, PATHINFO_EXTENSION);
        $extension = !empty($extension) ? $extension : null;
        if (!$extension && 'application/octet-stream' !== $contentType) {
            $mimes = new MimeTypes();
            $extension = $mimes->getExtension($contentType);
            $originalName .= '.'.$extension;
        }

        $path = $this->storageManager->generatePath($extension);

        $this->storageManager->store($path, $response->getBody()->getContents());

        $asset = $this->assetManager->createAsset(
            $path,
            $contentType,
            $originalName,
            $response->getBody()->getSize()
        );

        $commit = new Commit();
        $commit->setFormData($formData);
        $commit->setUserId($userId);
        $commit->setFiles([$asset->getId()]);
        $message = json_encode($commit->toArray());
        $this->commitProducer->publish($message);

        return ConsumerInterface::MSG_ACK;
    }
}
