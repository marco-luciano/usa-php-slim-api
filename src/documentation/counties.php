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
 * @OA\Put(
 *     path="/counties",
 *     tags={"Counties"},
 *     @OA\Response(response="200", 
 * description="Updates county population")
 * )
 */


 /**
 * @OA\Delete(
 *     path="/counties",
 *     tags={"Counties"},
 *     @OA\Response(response="200", 
 * description="Deletes a county")
 * )
 */