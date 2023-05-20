<?php

/**
 * @OA\Post(
 *     path="/user",
 *     tags={"Users"},
 *     summary="User registration",
 *     description="User registration",
 *     @OA\RequestBody(
 *         @OA\MediaType(
 *             mediaType="application/json",
 *             @OA\Schema(
 *                 type="object",
 * 
 *                 @OA\Property(
 *                     property="username",
 *                     description="User name",
 *                     example="marcomagisano",
 *                     type="string",
 *                 ),
 *                 @OA\Property(
 *                     property="password",
 *                     description="Password (at least 8 characters)",
 *                     example="marcomagisano",
 *                     type="string"
 *                 ),
*                 @OA\Property(
 *                     property="passwordConfirmation",
 *                     description="Password confirmation. Must match password.",
 *                     example="marcomagisano",
 *                     type="string"
 *                 )
 *             )
 *          )
 *     ),
 *      @OA\Response(
 *          response="201", 
 *          description="Creates a user",
 *          @OA\MediaType(
 *              mediaType="application/json"
 *          )
 *      ),
 *
 *     @OA\Response(
 *         response=400,
 *         description="Empty response",
 *         @OA\MediaType(
 *             mediaType="application/json"
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthorized, invalid credentials, incorrect username or password",
 *         @OA\MediaType(
 *             mediaType="application/json"
 *         )
 *     ),
 *     @OA\Response(
 *         response=409,
 *         description="User already registered"),
 *         @OA\MediaType(
 *            mediaType="application/json"
 *         )
 *     )
 * )
 */

/**
 * @OA\Post(
 *     tags={"Users"},
 *     path="/user/authentication",
 *     summary="User authentication with username and password",
 *     description="User authentication with username and password generated in <code> /user </code> ",
 *     @OA\RequestBody(
 *         @OA\MediaType(
 *             mediaType="application/json",
 *             @OA\Schema(
 *                 type="object",
 *                 @OA\Property(
 *                     property="username",
 *                     description="User name",
 *                     example="marcomagisano",
 *                     type="string",
 *                 ),
 *                 @OA\Property(
 *                     property="password",
 *                     description="Password (at least 8 characters)",
 *                     example="marcomagisano",
 *                     type="string"
 *                 )
 *             )
 *          )
 *     ),
 *     @OA\Response(
 *       response="200", 
 *       description="Authenticates a user returning a token",
 *       @OA\MediaType(
 *          mediaType="application/json"
 *       )
 *     ),
 *     @OA\Response(
 *       response="400", 
 *       description="Bad request",
 *       @OA\MediaType(
 *          mediaType="application/json"
 *       )
 *     ),
 *     @OA\Response(
 *       response="401", 
 *       description="Unauthorized, invalid credentials, incorrect username or password",
 *       @OA\MediaType(
 *           mediaType="application/json"
 *       )
 *     )
 * )
 **/

/**
 * @OA\Get(
 *     tags={"Users"},
 *     path="/user/authentication",
 *     summary="User authentication (testing)",
 *     description="User authentication for testing purposes",
 *     @OA\Response(
 *       response="200", 
 *       description="Returns a JWT token",
 *     )
 * )
 **/

