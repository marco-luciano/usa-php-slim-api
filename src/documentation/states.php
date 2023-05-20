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
