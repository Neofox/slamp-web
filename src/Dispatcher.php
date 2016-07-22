<?php

namespace Slamp\Web;


use Aerys\{Request, Response};
use function Amp\resolve;
use Slamp\Web\Authentication\{AuthenticationException, Authentication};
use Slamp\Web\Helper\Response as ApiResponse;
use Slamp\Web\Helper\{ResponseError, StandardRequest};
use stdClass;

class Dispatcher
{
    /** @var Bot  */
    private $bot;

    /** @var Authentication  */
    private $authentication;

    public function __construct(Authentication $authentication, Bot $bot)
    {
        $this->authentication = $authentication;
        $this->bot = $bot;
    }


    private function writeResponse(Request $request, Response $response, ApiResponse $result)
    {
        $response->setStatus($result->getStatus());
        $response->setHeader("content-type", "application/json");

        foreach ($result->getLinks() as $rel => $params) {
            $uri = strtok($request->getUri(), "?");
            $uri .= "?" . http_build_query($params);
            $elements[] = "<{$uri}>; rel=\"{$rel}\"";
        }

        if (isset($elements)) {
            $response->addHeader("link", implode(", ", $elements));
        }

        $response->end(json_encode($result->getData(), JSON_PRETTY_PRINT));
    }

    public function handle(Request $request, Response $response, array $args)
    {
        $endpoint = $request->getLocalVar("slamp.bot.endpoint");
        $user = $request->getLocalVar("slamp.bot.user");

        if (!$endpoint || !$user) {
            // if this happens, something's really wrong, e.g. wrong order of callables
            $response->setStatus(500);
            $response->end("The dispatcher can't handle the request");
        }
        foreach ($args as $key => $arg) {
            if (is_numeric($arg)) {
                $args[$key] = (int)$arg;
            }
        }

        foreach ($request->getAllParams() as $key => $value) {
            // Don't allow overriding URL parameters
            if (isset($args[$key])) {
                continue;
            }

            if (is_numeric($value)) {
                $args[$key] = (int)$value;

            } else {
                if (is_string($value)) {
                    $args[$key] = $value;

                } else {
                    $result = new ResponseError("bad_request", "invalid query parameter types", 400);
                    $this->writeResponse($request, $response, $result);

                    return;
                }
            }
        }

        $args = $args ? (object)$args : new stdClass();
        $body = yield $request->getBody();
        $payload = $body ? json_decode($body) : null;

        $result = yield $this->bot->process(new StandardRequest($endpoint, $args, $payload), $user);

        $this->writeResponse($request, $response, $result);
    }

    public function authorize(Request $request, Response $response) {
        if (!$request->getHeader("authorization")) {
            $response->setStatus(401);
            $response->end("Token needed.");
            return;
        }
        $authorization = $request->getHeader("authorization");
        $authorization = explode(" ", $authorization, 1);

        try {
            $user = yield resolve($this->authentication->authenticateWithToken($authorization[0]));
            $request->setLocalVar("slamp.bot.user", $user);
        } catch (AuthenticationException $e) {
            $result = new ResponseError("bad_authentication", $e->getMessage(), 403);
            $this->writeResponse($request, $response, $result);
        }
    }

    public function fallback(Request $request, Response $response)
    {
        $this->writeResponse($request, $response, new ResponseError("not_found", "No route found.", 404));
    }
}