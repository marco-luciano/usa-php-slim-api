<?php

/**
 *@OA\Tag(name="States", description="")
 */

/**
 * @OA\Get(
 *     path="/states",
 *     tags={"States"},
 *     summary="Get all US states with population",
 *     description="List of all US states with their total population",
 *     security={{"bearerAuth" : {}}},
 *     @OA\Response(
 *      response="200", 
 *      description="Returns a list of all states with their total population"
 *    ),
 *    @OA\Response(
 *      response="400", 
 *      description="Invalid state ID"
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
 * @OA\Get(
 *     path="/states/{state_id}/counties",
 *     tags={"States"},
 *     summary="Get counties for a state",
 *     description="List of all counties with their total population",
 *     security={{"bearerAuth" : {}}},
 *     @OA\Parameter(
 *       name="state_id",
 *       description="Numeric ID of the state to get",
 *       in="path",
 *       @OA\Schema(type="integer"),
 *       required="true",
 *       example=1
 *     ),
 *     @OA\Response(
 *      response="200", 
 *      description="Returns a list of all counties in a state"
 *    ),
 *     @OA\Response(
 *      response="400", 
 *      description="State ID not valid"
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
