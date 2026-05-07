<?php


  namespace App\Services;


  class ApiResponseService{


     static   function Response($status=200,$message="",$data=[]){
         
       $response= [
          "status"=>$status,
          "message"=>$message,
          "data"=>$data,
       ];

       

       return response()->json($response,$status);
     }

  }