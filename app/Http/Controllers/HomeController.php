<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Image;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }
    
    
    public function profileEdit($id)
    {
        $getUserDetails = \App\User::where('id','=',$id)->first();
        
        return view('auth.profile')->with([
            'getUserDetails' => $getUserDetails,
            'id' => $id
        ]);
        
    }
    
    public function profileUpdate(Request $request)
    {
        $getUserDetails = User::where('id','=',$request->profile_id)->first();
        
        if(!empty($getUserDetails))
        {
            if($request->file('profile_picture'))
            {
                $image = $request->file('profile_picture');

                $input['imagename'] = time().'.'.$image->getClientOriginalExtension();

                $destinationPath = public_path('/thumbnail');

                $img = Image::make($image->getRealPath());

                $img->resize(100, 100, function ($constraint) {

                    $constraint->aspectRatio();

                })->save($destinationPath.'/'.$input['imagename']);

                /*After Resize Add this Code to Upload Image*/
                $destinationPath = public_path('/');

                $image->move($destinationPath, $input['imagename']);
            }
            
            $getUserDetails->first_name = $request->first_name;
            $getUserDetails->last_name = $request->last_name;
            $getUserDetails->phone_number =  $request->phone_number;
            $getUserDetails->profile_picture = !empty($input['imagename']) ? $input['imagename'] : NULL;
            $getUserDetails->save();
            
            return redirect()->back()->with('message', 'Updated successfully!');
        }
        
        return redirect()->back()->with('message', 'Error!');
    }
}
