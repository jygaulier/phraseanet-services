# Report bundle

This bundle provide [report-sdk](../report-sdk/README.md) as an extended service using Symfony security.

If user is authenticated with a RemoteAuthToken (see [remote-auth-bundle](../remote-auth-bundle/README.md)), then user ID will be provided automatically.

## Configuration

```yaml
# config/packages/alchemy_acl.yml

alchemy_acl:
  app_name: my-app-name
```

## Usage

### In a HTTP request context

```php
<?php
namespace App\Controller;

use Alchemy\AclBundle\ReportUserService;
use Alchemy\ReportSDK\LogActionInterface;
use Symfony\Component\HttpFoundation\Request;

final class MyController
{
    public function handle(string $id, ReportUserService $reportClient, Request $request)
    {
        // ...
        $reportClient->pushHttpRequestLog(
            $request,
            LogActionInterface::ASSET_DOWNLOAD, // action
            $id, // item ID
            [
                'some' => [
                    'extra' => 'data',
                ],
            ], // payload
        );
        // ...
    }
}
```

## In a worker context

```php
<?php
namespace App;

use Alchemy\AclBundle\ReportUserService;
use Alchemy\ReportSDK\LogActionInterface;

final class MyService
{
    /**
     * @var ReportUserService
     */
    private $reportClient;

    public function __construct(ReportUserService $reportClient)
    {
        $this->reportClient = $reportClient;
    }

    public function handle(string $id)
    {
        // ...
        $this->reportClient->pushLog(
            LogActionInterface::ASSET_DOWNLOAD, // action
            $id, // item ID
            [
                'some' => [
                    'extra' => 'data',
                ],
            ], // payload
        );
        // ...
    }
}
```