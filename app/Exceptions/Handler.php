<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Auth\AuthenticationException;
use App\Models\User;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }
    
    public function render($request, Throwable $exception)
    {    
        // Check if the request accepts JSON
        if($request->header('accept')=='application/json'){
             // Handle AuthenticationException for json requests
        if ($exception instanceof AuthenticationException) {
            return $this->handleAuthException($exception, $request);
            }
   
        }
        else{
             // Default handling for non-JSON requests
            return Parent::render($request, $exception);
        }

    }

    
    protected function handleAuthException(AuthenticationException $exception, $request)
    {

         // Check if the token is missing
            if ($exception->getMessage() === 'Unauthenticated.') {
            return response()->json([
                'status' => 'unauthenticated',
                'message' => 'Missing token'
            ], 401);

             // Check if the token is invalid
           if ($exception->getMessage() === 'Invalid token.') {
            return response()->json([
                'status' => 'unauthenticated',
                'message' => 'Invalid token'
            ], 401);
        }
        
        }
        
          // Check if the user is blocked
        $user = User::where('id', $request->id)->first();
        if ($user && $user->blocked) {
            return response()->json([
                'status' => 'unauthenticated',
                'message' => 'User is blocked'
            ], 401);
        }
    
         // Default response for other authentication issues
        return response()->json([
            'status' => 'unauthenticated',
            'message' => 'Unauthenticated'
        ], 401);
    }

}
