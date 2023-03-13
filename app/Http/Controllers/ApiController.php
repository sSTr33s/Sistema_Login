<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;

use Illuminate\Support\Facades\Hash as FacadesHash;

class ApiController extends Controller
{
    public function users(Request $request){
        if($request->has('active')){
            $users=User::where('active',true)->get();
        }else{
            $users=User::all();
        }
        return response()->json($users);
    }

    public function login(Request $request){
        $responde=["status"=>"0","msg"=>""];

        $data=json_decode($request->getContent());

        $user=User::where('email',$data->email)->first();

        if($user){
            if(FacadesHash::check($data->password,$user->password)){
                //Genera el token de acceso
                //
                $token = $user->createToken("example");//Crea nombre y permisos
                $responde["status"]=1;
                $responde["msg"]=$token->plainTextToken;

            }else{
                $responde["msg"]="Invalid Password";
            }
        }else{
            $responde["msg"]="User not found";
        }

        return response()->json($responde);
    }

    }

