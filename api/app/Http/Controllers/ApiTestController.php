<?php

namespace App\Http\Controllers;

use App\Enums\PersonaTypeEnum;
use Illuminate\Http\Request;

/**
* @OA\Get(
*    tags={"Test"},
*    path="/api/v1/test",
*    description="Returns a simple Ok message for API Connectivity test.",
*    @OA\Response(
*       response=200,
*       description="API Communication Ok, You're ready to go",
*         @OA\JsonContent(
*            type="object",
*              @OA\Property(
*                 property="code",
*                 type="number",
*                 description="Result Code"
*              ),
*              @OA\Property(
*                 property="message",
*                 type="string",
*                 description="Result message"
*              )
*         )
*    ),
*    @OA\Response(response="404", description="Não encontrado")
* )
*/

class ApiTestController extends Controller
{

    /**
     * Return a JSON object with Ok message
    */
    public function makeTest() {
        return response()->json([
            'code' => 200,
            'message' => 'This is a Test v1',
        ]);
    }

}