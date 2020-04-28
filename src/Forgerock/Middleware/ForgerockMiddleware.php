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
use function Sabre\Event\Loop\instance;

class ForgerockMiddleware
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

            $token = null;
            $authorizationHeader = str_replace('bearer ', '', $authorization);
            $token = str_replace('Bearer ', '', $authorizationHeader);

            try {
                $sessionManagement = new SessionManagement();
                $decodedData = $sessionManagement->validateToken($token);
            } catch (SignatureInvalidException $e) {
                $this->logging('VERIFY_TOKEN', 'forge-rock', $e->getMessage());
                throw new FRSignatureInvalidException(["token" => [$e->getMessage()]], Response::HTTP_UNPROCESSABLE_ENTITY, "AUTH422");
            } catch (ExpiredException $e) {
                $this->logging('VERIFY_TOKEN', 'forge-rock', $e->getMessage());
                throw new ForgeRockExceptions(["token" => [$e->getMessage()]], Response::HTTP_UNAUTHORIZED, "AUTH401");
            } catch (\Exception $e) {
                $this->logging('VERIFY_TOKEN', 'forge-rock', $e->getMessage());
                throw new ForgeRockExceptions(["token" => [$e->getMessage()]], Response::HTTP_INTERNAL_SERVER_ERROR, "AUTH500");
            }

            $identityManagement = new IdentityManagement();
            try {
                $memberForgeRock = $identityManagement->getMe($token);
            } catch (\Exception $e) {
                $this->logging('VERIFY_TOKEN', 'forge-rock', $e->getMessage());
                throw new ForgeRockExceptions($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR, Response::HTTP_INTERNAL_SERVER_ERROR);
            }
            $request->request->add(['memberForgeRock' => $memberForgeRock]);
            return $next($request);
        } else {
            return response()->json([
                'status' => false,
                'code' => "AUTH401",
                'message' => null,
                'error_message' => [
                    "token" => [__("message.required")]
                ],
                'data' => null
            ], $responseCode);
        }
    }
}
