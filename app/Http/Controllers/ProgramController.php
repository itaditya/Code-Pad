<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Session;
use Redirect;
use App\programRecord;
use App\program_details;
use Validator;
use View;
use Illuminate\Support\Facades\Input;

class ProgramController extends Controller
{
    
    public function record(Request $request)
    {
    	$this->validate($request,[
            'name' => 'required|max:255|',
            'code' => 'required|max:20|unique:compiler_record',
            'uploaded_by'=>'required'
        ]);

        $record=array('name'=>Input::get('name'),
        	'code'=>Input::get('code'),
        	'uploaded_by'=>Input::get('uploaded_by'));

        // return $record;

        $rec = new ProgramRecord;
        $rec->name=$record['name'];
        $rec->code=$record['code'];
        $rec->uploaded_by=$record['uploaded_by'];
        if($rec->save())
        {
        	$result=ProgramRecord::where('code',$record['code'])->first();
        	Session::put('record_id',$result->id);
        	return Redirect::to('program_input')->with('message','Record is successfully saved');
        }

        return Redirect::back()->with('message','Record is failed');
    }

    public function update_data(Request $request)
    {
        $this->validate($request,[
            'name' => 'required|max:255|',
            'code' => 'required|max:20|',
            'uploaded_by'=>'required'
        ]);

        $update=array('name'=>Input::get('name'),
            'code'=>Input::get('code'),
            'uploaded_by'=>Input::get('uploaded_by'));

        // return $record;

        $result=ProgramRecord::where('code',$update['code'])->first();
        if($result)
        {
            if($result['uploaded_by']==$update['uploaded_by'])
            {
                Session::put('record_id',$result->id);
                return view('program.update');
            }
            return Redirect::back()->with('error','You are not authorized to update this event');
        }
        
        return Redirect::back()->with('error','Incorrect Program Code');
    }

    public function program_details(Request $request)
    {
        $this->validate($request,[
            'program_name' => 'required|max:255|',
            'program_statement' => 'required|',
            'testcases_input' =>'required',
            'testcases_output' => 'required'
        ]);

        $pg=array('program_name'=>Input::get('program_name'),
            'program_statement'=>Input::get('program_statement'),
            'sample_input'=>Input::get('sample_input'),
            'sample_output'=>Input::get('sample_output'),
            'testcases_input'=>Input::get('testcases_input'),
            'testcases_output'=>Input::get('testcases_output')
        );

        $prg= new Program_Details;
        $prg->program_name = $pg['program_name'];
        $prg->program_statement = $pg['program_statement'];
        $prg->sample_input = $pg['sample_input'];
        $prg->sample_output = $pg['sample_output'];
        $prg->testcases_input = $pg['testcases_input'];
        $prg->testcases_output = $pg['testcases_output'];
        $prg->record_id=Session::get('record_id');
        if($prg->save())
        {
            return Redirect::back()->with('message','Program uploaded');
        }

        return Redirect::back()->with('message','Program failed to upload');
    }

    public function snippet()
    {
        $snippet=Input::get('program');
        Session::put('snippet',$snippet);
        return View('program.program');
    }

    public function updateProgram($id)
    {
        $result=Program_Details::find($id);
        // echo $result;
        // var_dump(json_decode($result));
        // $result=json_decode($result);
        // var_dump($result);
        // Session::put(old('program_name'),$result->program_name);
        // $result=array_merge($result,['id'=>$id]);
        return View('program.updateProgram')->with('data',$result);
    }

    public function ProgramUpdateDone()
    {
        $result=Input::all();
        $prg= Program_Details::find($result['id']);
        $prg->program_name = $result['program_name'];
        $prg->program_statement = $result['program_statement'];
        $prg->sample_input = $result['sample_input'];
        $prg->sample_output = $result['sample_output'];
        $prg->testcases_input = $result['testcases_input'];
        $prg->testcases_output = $result['testcases_output'];
        $prg->record_id=Session::get('record_id');
        if($prg->save())
        {
            // return Redirect::to('program')->with('message','Program is updated!');
            return view('program.update')->with('message','Program is updated!');
        }
        else
        {
            return Redirect::back()->with('message','Error in updating program, Try Again');
        }

    }
}
