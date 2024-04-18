<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class FormatErrorResponse
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if ($response->status() >= 400 && $response->status() < 600) {
            $content = json_decode($response->getContent(), true);

            $formattedError = [
                'status' => 'error',
                'message' => $this->formatErrorMessage($content),
            ];

            // Include detailed validation errors if they exist
            if (!empty($content['errors']) && is_array($content['errors'])) {
                $formattedError['errors'] = $content['errors'];
            }

            $response->setContent(json_encode($formattedError));
            $response->header('Content-Type', 'application/json');
        }

        return $response;
    }

    private function formatErrorMessage($content)
    {
        if (!empty($content['message'])) {
            return $content['message'];
        } elseif (!empty($content['errors'])) {
            if (is_array($content['errors'])) {
                // Join the first error message from each field
                return implode(', ', array_map(function ($errors) {
                    return is_array($errors) ? $errors[0] : $errors;
                }, $content['errors']));
            } else {
                return $content['errors'];
            }
        } else {
            return 'An error occurred.';
        }
    }
}
