<?php
/**
 * Samuelerwardi samuelerwardi@gmail.com
 */

namespace App\Forgerock\Middlewere;
use App\Forgerock\IdentityManagement;
use App\Forgerock\MemberPimcore;
use App\Forgerock\SessionManagement;
use Closure;
use Illuminate\Http\JsonResponse;

class ServiceMiddleware
{
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
        } catch (\Exception $e) {
            var_dump($e->getMessage());
            die;
            return new JsonResponse([
                'status' => [
                    'code' => 1,
                    'message' => "No valid token",
                    'errorMessage' => 'Error, No valid token'
                ]
            ], $responseCode);
        } catch (\Exception $e) {
            return new JsonResponse([
                'status' => [
                    'code' => 1,
                    'message' => "Invalid token",
                    'errorMessage' => 'Invalid token'
                ]
            ], 401);
        } catch (\Exception $e) {
            return new JsonResponse([
                'status' => [
                    'code' => 1,
                    'message' => "Expired token",
                    'errorMessage' => 'Expired token'
                ]
            ], 401);
        } catch (\Exception $e) {
            return new JsonResponse([
                'status' => [
                    'code' => 1,
                    'message' => "Something wrong with server",
                    'errorMessage' => 'Something wrong with server'
                ]
            ], 500);
        }

        $identityManagement = new IdentityManagement();
        try {
            $memberForgeRock = $identityManagement->getMe($token);
            $memberPimcore = MemberPimcore::Instance($memberForgeRock);
        } catch (\Exception $e) {
            echo $e->getMessage();
            die;
            return new JsonResponse([
                'status' => [
                    'code' => 1,
                    'message' => "Something wrong with server",
                    'errorMessage' => 'Something wrong with server'
                ]
            ], 500);
        }
        $request->request->add(['memberForgeRock' => $memberForgeRock]);
        $request->request->add(['memberPimcore' => $memberPimcore]);
        return $next($request);
    }

}
