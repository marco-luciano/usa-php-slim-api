<?php

/**
 *@OA\Tag(name="Counties", description="")
 */

/**
 * @OA\Get(
 *     path="/counties/{county_id}",
 *     tags={"Counties"},
 *     summary="County population",
 *     description="Returns the population and other data of a given US county",
 *     security={{"bearerAuth" : {}}},
 *     @OA\Parameter(
 *       name="county_id",
 *       description="Numeric ID of the county to get",
 *       in="path",
 *       @OA\Schema(type="integer"),
 *       required="true",
 *       example=1
 *     ),
 *     @OA\Response(
 *      response="200", 
 *      description="Returns the population of a county"
 *    ),
 *     @OA\Response(
 *      response="400", 
 *      description="County ID not valid"
 *    ),
 *     @OA\Response(
 *      response="401", 
 *      description="Unauthorized. Invalid authorization header"
 *    ),
 *     @OA\Response(
 *      response="500", 
 *      description="Internal server error"
 *    )
 *  )
 **/

/**
 * @OA\Post(
 *     tags={"Counties"},
 *     path="/counties",
 *     summary="Creates a new county",
 *     description="Creates a new county",
 *     security={{"bearerAuth" : {}}},
 *     @OA\RequestBody(
 *         @OA\MediaType(
 *             mediaType="application/json",
 *             @OA\Schema(
 *                 type="object",
 *                 @OA\Property(
 *                     property="name",
 *                     description="County name",
 *                     example="Magisano",
 *                     type="string"
 *                 ),
 *                 @OA\Property(
 *                     property="state_id",
 *                     description="State ID",
 *                     example="16",
 *                     type="integer"
 *                 ),
 *                 @OA\Property(
 *                     property="population",
 *                     description="County population",
 *                     example="42",
 *                     type="integer"
 *                 )
 *             )
 *          )
 *     ),
 *     @OA\Response(
 *      response="200", 
 *      description="County added",
 *      @OA\MediaType(
 *        mediaType="application/json",
 *             @OA\Schema(
 *                 type="object",
 *                 @OA\Property(
 *                     property="county_id",
 *                     description="County name",
 *                     example="3145",
 *                     type="integer"
 *                 ),
 *                 @OA\Property(
 *                     property="name",
 *                     description="County name",
 *                     example="Magisano",
 *                     type="string"
 *                 ),
 *                 @OA\Property(
 *                     property="state_id",
 *                     description="State ID",
 *                     example="16",
 *                     type="integer"
 *                 ),
 *                 @OA\Property(
 *                     property="population",
 *                     description="County population",
 *                     example="42",
 *                     type="integer"
 *                 ),
 *                 @OA\Property(
 *                     property="date",
 *                     description="County population",
 *                     example="2022-03-23 00:31:09.115827+00",
 *                     type="date"
 *                 )
 *             )
 *      )
 *    ),
 *     @OA\Response(
 *      response="400", 
 *      description="Bad Request"
 *    ),
 *     @OA\Response(
 *      response="401", 
 *      description="Unauthorized. Invalid authorization header"
 *    ),
 *     @OA\Response(
 *      response="500", 
 *      description="Internal server error"
 *    )
 *  )
 **/

/**
 * @OA\Put(
 *     tags={"Counties"},
 *     path="/counties/{county_id}",
 *     summary="Updates county population",
 *     description="Updates the population of a county",
 *     security={{"bearerAuth" : {}}},
 *     @OA\Parameter(
 *       name="county_id",
 *       description="Numeric ID of the county to update population",
 *       in="path",
 *       @OA\Schema(type="integer"),
 *       required="true",
 *       example=3140
 *     ),
 *     @OA\RequestBody(
 *         @OA\MediaType(
 *             mediaType="application/json",
 *             @OA\Schema(
 *                 type="object",
 *                 @OA\Property(
 *                     property="population",
 *                     description="New county population",
 *                     example="150000",
 *                     type="integer"
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *      response="200", 
 *      description="County updated",
 *      @OA\MediaType(
 *        mediaType="application/json",
 *             @OA\Schema(
 *                 type="object",
 *                 @OA\Property(
 *                     property="county_id",
 *                     description="County name",
 *                     example="3145",
 *                     type="integer"
 *                 ),
 *                 @OA\Property(
 *                     property="name",
 *                     description="County name",
 *                     example="Magisano",
 *                     type="string"
 *                 ),
 *                 @OA\Property(
 *                     property="state_id",
 *                     description="State ID",
 *                     example="16",
 *                     type="integer"
 *                 ),
 *                 @OA\Property(
 *                     property="population",
 *                     description="County population",
 *                     example="42",
 *                     type="integer"
 *                 ),
 *                 @OA\Property(
 *                     property="date",
 *                     description="County population",
 *                     example="42",
 *                     type="date"
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *      response="400", 
 *      description="Bad Request"
 *    ),
 *     @OA\Response(
 *      response="401", 
 *      description="Unauthorized. Invalid authorization header"
 *    ),
 *     @OA\Response(
 *      response="500", 
 *      description="Internal server error"
 *    )
 *  )
 * )
 */

/**
 * @OA\Delete(
 *     tags={"Counties"},
 *     path="/counties/{county_id}",
 *     summary="Delete county",
 *     description="Deletes a county",
 *     security={{"bearerAuth" : {}}},
 *     @OA\Parameter(
 *         name="county_id",
 *         description="Numeric ID of the county to delete",
 *         in="path",
 *         @OA\Schema(type="integer"),
 *         required="true",
 *         example=3140
 *     ),
 *     @OA\Response(
 *      response="204", 
 *      description="County deleted",
 *      @OA\MediaType(
 *        mediaType="application/json",
 *             @OA\Schema(
 *                 type="object",
 *                 @OA\Property(
 *                     property="status",
 *                     description="Request Status",
 *                     example="ok",
 *                     type="string"
 *                 ),
 *                 @OA\Property(
 *                     property="description",
 *                     description="Description",
 *                     example="county_id 3140 deleted successfully",
 *                     type="string"
 *                 )
 *             )
 *          ),
 *     ),
 *     @OA\Response(
 *      response="400", 
 *      description="Bad Request"
 *    ),
 *     @OA\Response(
 *      response="401", 
 *      description="Unauthorized. Invalid authorization header"
 *    ),
 *     @OA\Response(
 *      response="500", 
 *      description="Internal server error"
 *    )
 *  )
 * )
 *
 **/
