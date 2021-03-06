<?php

declare(strict_types=1);

namespace App\Tests\Asset;

use Alchemy\ApiTest\ApiTestCase;
use App\Entity\Asset;
use App\Entity\Commit;
use App\Storage\AssetManager;
use App\Storage\FileStorageManager;
use Doctrine\ORM\EntityManagerInterface;
use League\Flysystem\FileExistsException;

abstract class AbstractAssetTest extends ApiTestCase
{
    const SAMPLE_FILE = __DIR__.'/../fixtures/32x32.jpg';
    protected $assetId;

    protected function commitAsset(string $token = 'secret_token')
    {
        $commit = new Commit();
        $commit->setToken($token);
        $commit->setUserId('a_user_id');
        $commit->setFormData(['foo' => 'bar']);
        $commit->setTotalSize(42);

        /** @var EntityManagerInterface $em */
        $em = self::$container->get(EntityManagerInterface::class);
        $em->persist($commit);
        $em->flush();
        $em
            ->getRepository(Asset::class)
            ->attachCommit([$this->assetId], $commit->getId());

        $asset = $em->find(Asset::class, $this->assetId);
        $em->refresh($asset);
    }

    private function createAsset(): Asset
    {
        /** @var AssetManager $assetManager */
        $assetManager = self::$container->get(AssetManager::class);
        $storageManager = self::$container->get(FileStorageManager::class);
        $realPath = self::SAMPLE_FILE;
        $path = 'test/foo.jpg';
        $asset = $assetManager->createAsset($path, 'image/jpeg', 'foo.jpg', 846, 'user_id');

        $stream = fopen($realPath, 'r+');
        try {
            $storageManager->storeStream($path, $stream);
        } catch (FileExistsException $e) {
            $storageManager->delete($path);
            $storageManager->storeStream($path, $stream);
        }
        fclose($stream);

        return $asset;
    }

    protected function setUp()
    {
        parent::setUp();

        $asset = $this->createAsset();
        $this->assetId = $asset->getId();
    }
}
