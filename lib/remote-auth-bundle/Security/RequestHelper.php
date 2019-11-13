<?php

declare(strict_types=1);

namespace Alchemy\RemoteAuthBundle\Security;

use Symfony\Component\HttpFoundation\Request;

abstract class RequestHelper
{
    public static function getAuthorizationFromRequest(Request $request, $authType = 'Bearer', bool $allowGET = true, string $getParam = 'access_token'): ?string
    {
        if (
            (null !== $accessToken = $request->headers->get('Authorization'))
            && 0 === strpos($accessToken, $authType.' ')
        ) {
            return preg_replace('#^'.$authType.'\s+#', '', $accessToken);
        } elseif ($allowGET) {
            return $request->get($getParam);
        }

        return null;
    }
}
