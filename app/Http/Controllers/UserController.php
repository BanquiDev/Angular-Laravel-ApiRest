<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\DB;
use App\Helpers\JwtAuth;

class UserController extends Controller
{
  //Registro

    public function register(Request $request){
      //Recoger post
      $json = $request->input('json', null);
      $params = json_decode($json);

      $email = (!is_null($json) && isset($params->email)) ? $params->email : null;
      $name = (!is_null($json) && isset($params->name)) ? $params->name : null;
      $surname = (!is_null($json) && isset($params->surname)) ? $params->surname : null;
      $role = 'ROLE_USER';
      $password = (!is_null($json) && isset($params->password)) ? $params->password : null;

      if (!is_null($email) && !is_null($name) && !is_null($password)) {

        $user = new User;
        $user->email = $email;
        // $user->password = $password;
        $user->name = $name;
        $user->surname = $surname;
        $user->role = $role;

        $pwd = hash('sha256', $password);
        $user->password = $pwd;

        //Comprobar usuario duplicado

        $isset_user = User::where('email', '=', $email)->take(1)->get();
        // dd($isset_user);
        if (count($isset_user) == 0) {
          //Guardar usuario
          $user->save();

          $data = array(
            'status' => 'success',
            'code' => 200,
            'message' => 'Usuario registrado correctamente'
          );

        }else{
          //No guardarlo xq existe
          $data = array(
            'status' => 'error',
            'code' => 400,
            'message' => 'Usuario duplicado, no puede registrarse'
          );
        }

      }else{
        $data = array(
          'status' => 'error',
          'code' => 400,
          'message' => 'Usuario no creado'
        );
      }
      return response()->json($data, 200);
    }

//Login

    public function login(Request $request){
      
      $jwtAuth = new JwtAuth();

      //Recibir Post

      $json = $request->input('json', null);
      $params = json_decode($json);
      
      $email = (!is_null($json) && isset($params->email)) ? $params->email : null;
      $password = (!is_null($json) && isset($params->password)) ? $params->password : null;
      $getToken= (!is_null($json) && isset($params->getToken)) ? $params->getToken : null;
      
      //Cifrar la Password
      
      $pwd = hash('sha256', $password);

      if(!is_null($email) && !is_null($password) && ($getToken == null || $getToken == 'false')){

        $signup = $jwtAuth->signup($email, $pwd);
                        
      }else if($getToken != null){

        $signup = $jwtAuth->signup($email, $pwd, $getToken);
                
      }else{
        $signup = array(
          'status' => 'error', 
          'message' => 'Envia tus datos por Post'
        );
      }
      return response()->json($signup, 200);
    }
}
