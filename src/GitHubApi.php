<?php namespace Publish;

use Illuminate\Support\Facades\Http;

class GitHubApi
{
    public function __construct(
        private string $owner,
        private string $repository,
        private JWT $jwt
    ) {
    }

    public function getInstallation(): array
    {
        $response = Http::withToken($this->jwt->createToken())->get(
            "https://api.github.com/app/installations"
        );

        if ($response->status() >= 400) {
            throw Exception::gitHubError(
                $response->status(),
                $response->json("message") ?? $response->reason()
            );
        }

        foreach ($response->json() as $installation) {
            if ($installation["account"]["login"] === $this->owner) {
                return $installation;
            }
        }

        throw Exception::gitHubAppNotInstalled($this->owner);
    }

    public function getAccessToken(string $accessTokenUrl): array
    {
        $response = Http::withToken($this->jwt->createToken())->post(
            $accessTokenUrl
        );

        if ($response->status() >= 400) {
            throw Exception::gitHubError(
                $response->status(),
                $response->json("message") ?? $response->reason()
            );
        }

        return $response->json();
    }

    public function getRuns(string $accessToken, string $workflow): array
    {
        $response = Http::withBasicAuth("x-access-token", $accessToken)->get(
            "https://api.github.com/repos/{$this->owner}/{$this->repository}/actions/workflows/{$workflow}/runs"
        );

        if ($response->status() >= 400) {
            throw Exception::gitHubError(
                $response->status(),
                $response->json("message") ?? $response->reason()
            );
        }

        return $response->json("workflow_runs");
    }

    public function dispatchWorkflow(
        string $accessToken,
        string $workflow,
        string $ref
    ): bool {
        $body = json_encode(["ref" => $ref]);
        if ($body === false) {
            throw new Exception(
                500,
                "Failed to encode body for Github workflow dispatch endpoint."
            );
        }
        $response = Http::withBasicAuth("x-access-token", $accessToken)->post(
            "https://api.github.com/repos/{$this->owner}/{$this->repository}/actions/workflows/{$workflow}/dispatches",
            ["ref" => $ref]
        );

        if ($response->status() >= 400) {
            throw Exception::gitHubError(
                $response->status(),
                $response->json("message") ?? $response->reason()
            );
        }

        return true;
    }
}
