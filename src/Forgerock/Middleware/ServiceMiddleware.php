<?php
/**
 * Samuelerwardi samuelerwardi@gmail.com
 */

namespace App\Forgerock\Middlewere;
use App\Forgerock\Exceptions\ForgeRockExceptions;
use App\Forgerock\Exceptions\FRSignatureInvalidException;
use App\Forgerock\IdentityManagement;
use App\Forgerock\MemberPimcore;
use App\Forgerock\SessionManagement;
use App\Forgerock\Traits\LoggingTrait;
use Closure;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class ServiceMiddleware
{
    use LoggingTrait;
    /**
     * Run the request filter.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @param string
     * @return mixed
     * @throws \Auth0\SDK\Exception\CoreException
     */
    public function handle($request, Closure $next)
    {
        $responseCode = 401;

        if ($request->hasHeader('Authorization')) {
            $authorization = $request->header('Authorization');
        }

        $token = null;
        $authorizationHeader = str_replace('bearer ', '', $authorization);
        $token = str_replace('Bearer ', '', $authorizationHeader);

        try {
            $sessionManagement = new SessionManagement();
            $decodedData = $sessionManagement->validateToken($token);
        } catch (SignatureInvalidException $e) {
            $this->logging('VERIFY_TOKEN', 'forge-rock', $e->getMessage());
            throw new FRSignatureInvalidException($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY, Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (ExpiredException $e) {
            $this->logging('VERIFY_TOKEN', 'forge-rock', $e->getMessage());
            throw new ForgeRockExceptions($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY, Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {
            $this->logging('VERIFY_TOKEN', 'forge-rock', $e->getMessage());
            throw new ForgeRockExceptions($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR, Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $identityManagement = new IdentityManagement();
        try {
            $memberForgeRock = $identityManagement->getMe($token);
        } catch (\Exception $e) {
            throw new ForgeRockExceptions($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        $memberPimcore = MemberPimcore::Instance($memberForgeRock);
        $request->request->add(['memberForgeRock' => $memberForgeRock]);
        $request->request->add(['memberPimcore' => $memberPimcore]);
        return $next($request);
    }

}
