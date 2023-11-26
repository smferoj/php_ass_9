<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    // subject add 
    public function addSubject(Request $request)
    {
        // return $request->all();
        try {

            Subject::insert([
                'subject'=>$request->subject
            ]);
            
            return response()->json(['success' => true, 'msg' => 'Subject added successfully!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => $e->getMessage()]);
        }
    }
}
