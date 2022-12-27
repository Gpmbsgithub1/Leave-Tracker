<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use App\RemoteJob;
use Validator;
use App\User;
use App\Japi;
use App\Subscribe;
use App\Unsubscribe;
use Carbon\Carbon;
use App\RemoteSkill;
use App\RemoteJobskill;
use App\Plan;
use App\EmailSubmit;
use Artisan;
use App\Profile;
use App\Profile_file;
use Illuminate\Support\Facades\Mail;
class RemoteController extends Controller
{
    public function index(Request $request)
    {
        $q =  $request->get('search');
        if($q!=''){
            $rem = RemoteJob::orderBy('publication_date', 'DESC')->where('not_active','<>','1')->where('job_title','LIKE','%'.$q.'%')->paginate(100);
        } else {
            $rem = RemoteJob::orderBy('publication_date', 'DESC')->where('not_active','<>','1')->paginate(100);
        }
        return view('admin.remotejob.index', compact('rem'))->with('i', (request()->input('page', 1) - 1) * 100);
    }

    public function dashboard()
    {
        // $myString = "Spark : Amazing Customer Support &amp; Project Manager Needed for Marketing Automation Software";
// $myArray = explode(':', $myString);
// $park=$myArray[0];
// $park = $park.replace("o","Spark : ");
// $job=$myArray[0];
// dd($job);
        $jobs = RemoteJob::count();
            $month = RemoteJob::where('created_at', '>=', Carbon::now()->startOfMonth())->count();
	        $week = RemoteJob::where('created_at', '>=', Carbon::now()->startOfWeek())->count();
            $today = RemoteJob::where('created_at', '>=', Carbon::now()->startOfDay())->count();
        return view('admin.remotejob.dashboard', compact('jobs','month','week','today'));
    }

    public function create($id='')
    {
        $jobitem['id'] = '';
        $jobitem['job_id'] = '';
        $jobitem['url'] = '';
        $jobitem['company_slug'] = '';
        $jobitem['job_title'] = '';
        $jobitem['company_name'] = ''; 
        $jobitem['company_logo'] = '';
        $jobitem['category_name'] = '';
        $jobitem['job_type'] = '';
        $jobitem['tags'] =''; 
        $jobitem['required_location'] = '';
        $jobitem['salary'] = '';
        $jobitem['description'] = '';
        $jobitem['publication_date'] = '';
        $jobitem['job_resource'] =''; 

        if($id!=''){
            $job = RemoteJob::findOrFail($id);
            $jobitem['id'] = $job->id;;
            $jobitem['job_id'] = $job->job_id;
            $jobitem['url'] =$job->url;
            $jobitem['company_slug'] =$job->company_slug;
            $jobitem['job_title'] =$job->job_title;
            $jobitem['company_name'] = $job->company_name; 
            $jobitem['company_logo'] = $job->company_logo;
            $jobitem['category_name'] = $job->category_name;
            $jobitem['job_type'] =$job->job_type;
            $jobitem['tags'] =$job->tags; 
            $jobitem['required_location'] = $job->required_location;
            $jobitem['salary'] = $job->salary;
            $jobitem['description'] = $job->description;
            $jobitem['publication_date'] = $job->publication_date;
            $jobitem['job_resource'] =$job->job_resource; 

        }
        return view('admin.remotejob.create', compact('jobitem'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'job_id' => 'required',
            'url' => 'required',
            'job_title' => 'required',
            'category_name' => 'required',
            'job_type' => 'required',
            'category_name' => 'required',
            'description' => 'required',
            'publication_date' => 'required',
            'job_resource' => 'required',
  
        ]);

    
        if($request->input('id')==''){
            $RJob = new RemoteJob;
        }
        else{
            $RJob= RemoteJob::findOrFail($request->input('id'));
           
        }
   
        $RJob->job_id =$request->input('job_id');
        $RJob->url = $request->input('url');
        $RJob->slug = $request->input('company_slug');
        $RJob->job_title = $request->input('job_title');
        $RJob->company_name = $request->input('company_name'); 
        $RJob->company_slug = $request->input('company_slug'); 
        $RJob->company_logo = $request->input('company_logo'); 
        $RJob->category_name = $request->input('category_name');
        $RJob->job_type = $request->input('job_type');
        $RJob->tags =$request->input('tags');
        $RJob->required_location = $request->input('required_location');
        $RJob->salary = $request->input('salary');
        $RJob->description = $request->input('description');
        $date='';
        if($request->input('publication_date')!=''){
            $date = date('Y-m-d H:i:s', strtotime($request->input('publication_date')));   
        }
        $RJob->publication_date = $date;
        $RJob->job_resource =$request->input('job_resource');; 
        $RJob->save();  
    
       
        return redirect()->route('admin.remote.index')
            ->with('success', 'Job created successfully.');
    }


    public function destroy_job($id)
    {
        $job = RemoteJob::findOrFail($id);
        $job->delete();
        return redirect()->route('admin.remote.index')
            ->with('success', 'Job Delete successfully.'); 
    }


    protected function getRelatedSlugs($slug, $id = 0)
    {
        return RemoteCompany::select('slug')->where('slug', 'like', $slug.'%')
        ->where('id', '<>', $id)
        ->get();
    }

    public function subscribers()
    {
        $subscribers = Subscribe::orderBy('created_at','DESC')->paginate(30);
        return view('admin.remotejob.subscribers', compact('subscribers'))->with('i', (request()->input('page', 1) - 1) * 30);
    }

    public function subscribers_create($sid='')
    {
        $jobitem['id'] = '';
        $jobitem['email'] = '';
        $jobitem['category'] = '';
        $jobitem['country'] = '';
        $jobitem['interval'] = '';
        $jobitem['token'] = ''; 
        $jobitem['verify'] = '';
        $jobitem['source'] = '';

        if($sid!=''){
            $sub = Subscribe::findOrFail($sid);
            $jobitem['id'] = $sub->id;;
            $jobitem['email'] = $sub->email;
            $jobitem['category'] =$sub->category;
            $jobitem['country'] =$sub->country;
            $jobitem['interval'] =$sub->interval;
            $jobitem['token'] = $sub->token; 
            $jobitem['verify'] = $sub->verify; 
            $jobitem['source'] = $sub->source;
        }
        return view('admin.remotejob.subscribers_edit', compact('jobitem'));
    }

    // public function subscribers_edit($sid)
    // {
    //     $subscribers = Subscribe::findOrFail($sid);
    //     return view('admin.remotejob.subscribers_edit', compact('subscribers'));
    // }

    public function subscribers_store(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'category' => 'required',
            'interval' => 'required',
        ]);

        if($request->input('id')==''){
            $subscribers = new Subscribe;
        }
        else{
            $subscribers= Subscribe::findOrFail($request->input('id'));
           
        }
        $token=md5(uniqid(rand(), true));
        $subscribers->email = $request->input('email');
        $subscribers->category = $request->input('category');
        $subscribers->country = $request->input('country');
        $subscribers->interval = $request->input('interval');
        $subscribers->interval = $request->input('interval');
        $subscribers->token = $token;
        $subscribers->verify = $request->input('status');
        $subscribers->source = $request->input('source');
        $subscribers->save();
        return redirect()->route('admin.subscribers.index')
            ->with('success', 'Job Alert Updated successfully.');
    }

    public function subscribers_delete($sid)
    {
        $subscribers = Subscribe::findOrFail($sid);
        $subscribers->delete();
        return redirect()->route('admin.subscribers.index')
            ->with('success', 'Job Alert Deleted successfully.'); 
    }


    public function api()
    {
        $api = Japi::paginate(30);
        return view('admin.remotejob.api', compact('api'))->with('i', (request()->input('page', 1) - 1) * 30);
    }

    public function create_api($id='')
    {
        $jobitem['name']= '';
        $jobitem['api'] = '';
        $jobitem['prameter'] = '';
        $jobitem['id'] = '';
        $jobitem['job_id'] = '';
        $jobitem['url'] = '';
        $jobitem['company_slug'] = '';
        $jobitem['job_title'] = '';
        $jobitem['company_name'] = ''; 
        $jobitem['company_logo'] = '';
        $jobitem['category_name'] = '';
        $jobitem['job_type'] = '';
        $jobitem['tags'] =''; 
        $jobitem['required_location'] = '';
        $jobitem['salary'] = '';
        $jobitem['description'] = '';
        $jobitem['publication_date'] = '';
        $jobitem['job_resource'] =''; 

        if($id!=''){
            $job = Japi::findOrFail($id);
            $jobitem['name'] = $job->name;
            $jobitem['api'] = $job->api;
            $jobitem['prameter'] = $job->prameter;
            $jobitem['id'] = $job->id;
            $jobitem['job_id'] = $job->job_id;
            $jobitem['url'] =$job->url;
            $jobitem['company_slug'] =$job->company_slug;
            $jobitem['job_title'] =$job->job_title;
            $jobitem['company_name'] = $job->company_name; 
            $jobitem['company_logo'] = $job->company_logo;
            $jobitem['category_name'] = $job->category_name;
            $jobitem['job_type'] =$job->job_type;
            $jobitem['tags'] =$job->tags; 
            $jobitem['required_location'] = $job->required_location;
            $jobitem['salary'] = $job->salary;
            $jobitem['description'] = $job->description;
            $jobitem['publication_date'] = $job->publication_date;
            $jobitem['job_resource'] =$job->job_resource; 

        }
        return view('admin.remotejob.create_api', compact('jobitem'));
    }



    public function store_api(Request $request)
    {
     /*   $request->validate([
            'api' => 'required',
            'job_id' => 'required',
            'url' => 'required',
            'job_title' => 'required',
            'company_logo' => 'required',
            'category_name' => 'required',
            'job_type' => 'required',
            'category_name' => 'required',
            'description' => 'required',
            'publication_date' => 'required',
            'job_resource' => 'required',
  
        ]);
*/
    
        if($request->input('id')==''){
            $RJob = new Japi;
        }
        else{
            $RJob= Japi::findOrFail($request->input('id'));
           
        }
        $RJob->api =$request->input('api');
        $RJob->name =$request->input('name');
        $RJob->prameter =$request->input('prameter');
        $RJob->job_id =$request->input('job_id');
        $RJob->url = $request->input('url');;
        $RJob->slug = $request->input('company_slug');
        $RJob->job_title = $request->input('job_title');
        $RJob->company_name = $request->input('company_name'); 
        $RJob->company_slug = $request->input('company_slug'); 
        $RJob->company_logo = $request->input('company_logo'); 
        $RJob->category_name = $request->input('category_name');
        $RJob->job_type = $request->input('job_type');
        $RJob->tags =$request->input('tags');
        $RJob->required_location = $request->input('required_location');
        $RJob->salary = $request->input('salary');
        $RJob->description = $request->input('description');
        $RJob->publication_date = $request->input('publication_date');
        $RJob->job_resource =$request->input('job_resource'); 
        $RJob->save();  
    
       
        return redirect()->route('admin.api.index')
            ->with('success', 'API created successfully.');
    }


    public function load_api($id)
    {
       
       $api= Japi::findOrFail($id);
       $ch =  curl_init($api->api);
       curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
       curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
       curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
       curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
       curl_setopt($ch, CURLOPT_TIMEOUT, 3);
       curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json'));
       $getapi = curl_exec($ch);
       $getapi = json_decode($getapi,true);
        if($api->prameter!=''){
           $prsvr =  $getapi[$api->prameter];  
        }
        else{
            $prsvr= $getapi;
        }
        // dd($prsvr);
        // exit();
        // $prsvr = $prsvr->where('created_at','=', Carbon::yesterday())->orWhere('created_at','=', Carbon::today())->orWhere('pub_date','=', Carbon::yesterday())->orWhere('pub_date','=', Carbon::today())->orWhere('publication_date','=', Carbon::yesterday())->orWhere('publication_date','=', Carbon::today())->orWhere('pubDate','=', Carbon::yesterday())->orWhere('pubDate','=', Carbon::today())->get();
        if($prsvr==null){
            Artisan::call('config:cache');
        }
        foreach($prsvr as $apiitem){
           if(@$apiitem[$api->job_title]!=''){
            $CJob= RemoteJob::where('job_id','=',$apiitem[$api->job_id])->count();
            if($CJob<1){
                $RJob = new RemoteJob;
            }
            else{
                $RJob= RemoteJob::where('job_id','=',$apiitem[$api->job_id])->first();   
            }
            $RJob->job_id = @$apiitem[$api->job_id];
            $RJob->api_id = @$api->id;
            $RJob->api_name = @$api->name;
            $RJob->url =  @$apiitem[$api->url];
            if($api->name=='Weworkremotely'){
                $title=explode(":", $apiitem[$api->job_title]);
                $RJob->job_title = $title[1];
                $RJob->company_name = $title[0];
            }else{
            $RJob->job_title =@$apiitem[$api->job_title];
            $RJob->company_name =  @$apiitem[$api->company_name];
            }
            $RJob->company_slug = @$apiitem[$api->company_slug];
            $RJob->company_logo = @$apiitem[$api->company_logo];
            if($api->name=='Weworkremotely'){
                $RJob->category_name = $api->category_name;
            } else{
            $RJob->category_name = @$apiitem[$api->category_name];
            }
            $RJob->job_type = @$apiitem[$api->job_type];
            $RJob->tags =@$apiitem[$api->tags];
            $RJob->required_location = @$apiitem[$api->required_location];
            $RJob->salary = @$apiitem[$api->salary];
            $RJob->description = @$apiitem[$api->description];
            $date='';
            if(@$apiitem[$api->publication_date]!=''){
                $date = date('Y-m-d H:i:s', strtotime( $apiitem[$api->publication_date]));   
            }
            $RJob->publication_date = @$date;
            $RJob->job_resource =@$api->job_resource;
            $RJob->save();  
        }
        }
        $api->last_fetch = date('d M Y H:i:s', strtotime(Carbon::now()));
        $api->save();
        return redirect()->route('admin.api.index')
            ->with('success', 'API executed successfully.');

    }

    public function delete_api($id)
    {
        $job = Japi::findOrFail($id);
        $job->delete();
        return redirect()->route('admin.api.index')
            ->with('success', 'API Delete successfully.');
    }

    public function report()
    {
        $api = Japi::paginate(10);
        // dd($api);
        foreach($api as $k => $apis){
            $jobs = RemoteJob::where('api_id', '=', $apis->id)->count();
            $month = RemoteJob::where('api_id', '=', $apis->id)->where('created_at', '>=', Carbon::now()->startOfMonth())->count();
	        $week = RemoteJob::where('api_id', '=', $apis->id)->where('created_at', '>=', Carbon::now()->startOfWeek())->count();
            $today = RemoteJob::where('api_id', '=', $apis->id)->where('created_at', '>=', Carbon::now()->startOfDay())->count();
            $apis->jobs = $jobs;
	         $apis->week = $week;
	         $apis->today = $today;
	         $apis->month = $month;
        }
        return view('admin.report.report', compact('api'))->with('i', (request()->input('page', 1) - 1) * 10);
    }

    public function jobalertreport()
    {
        $alert = Subscribe::count();
        // dd($api);
        $verified = Subscribe::where('verify', '=', 1)->orWhere('verify', '=', '1')->count();
	        $notverified = Subscribe::whereNull('verify')->orWhere('verify', '=', '0')->orWhere('verify', '=', 0)->count();
            $unsubscribed = Subscribe::where('verify', '=', 2)->orWhere('verify', '=', '2')->count();
        return view('admin.report.jobalert', compact('alert','verified','notverified','unsubscribed'));
    }


public function dash()
    {
        $api = Japi::paginate(10);
        // dd($api);
        foreach($api as $k => $apis){
            $jobs = RemoteJob::where('api_id', '=', $apis->id)->count();
            $month = RemoteJob::where('api_id', '=', $apis->id)->where('created_at', '>=', Carbon::now()->startOfMonth())->count();
            $week = RemoteJob::where('api_id', '=', $apis->id)->where('created_at', '>=', Carbon::now()->startOfWeek())->count();
            $today = RemoteJob::where('api_id', '=', $apis->id)->where('created_at', '>=', Carbon::now()->startOfDay())->count();
            $apis->jobs = $jobs;
             $apis->week = $week;
             $apis->today = $today;
             $apis->month = $month;
        }
        return view('admin.dashboard', compact('api'))->with('i', (request()->input('page', 1) - 1) * 10);
    }


    public function jobalertemail(Request $request)
    {
        $sub = Subscribe::where('verify', '=', 1)->orWhere('verify', '=', '1')->get();
        foreach($sub as $k => $subs){
            if($subs->category == 'Programming'){
                $category = 'Programming';
                $source = 'email_newsletter_programming';
                $job = RemoteJob::orderBy('publication_date','DESC')->where('category_name','LIKE','%'.'Development'.'%')->orWhere('category_name','LIKE','%'.'Software Development'.'%')->orWhere('category_name','LIKE','%'.'Programming'.'%')->get();
            }elseif($subs->category == 'Design'){
                $category = 'Design';
                $source = 'email_newsletter_design';
                $job = RemoteJob::orderBy('publication_date','DESC')->where('category_name','LIKE','%'.'Design'.'%')->get();
            }elseif($subs->category == 'Content Writing'){
                $category = 'Content Writing';
                $source = 'email_newsletter_contentwriting';
                $job = RemoteJob::orderBy('publication_date','DESC')->where('category_name','LIKE','%'.'Writing'.'%')->orWhere('category_name','LIKE','%'.'Content Writing'.'%')->get();
            }elseif($subs->category == 'Sales & Marketing'){
                $category = 'Sales & Marketing';
                $source = 'email_newsletter_sales&marketing';
                $job = RemoteJob::orderBy('publication_date','DESC')->where('category_name','LIKE','%'.'Sales'.'%')->orWhere('category_name','LIKE','%'.'Marketing'.'%')->orWhere('category_name','LIKE','%'.'Sales & Marketing'.'%')->get();
            }elseif($subs->category == 'Customer Support'){
                $category = 'Customer Support';
                $source = 'email_newsletter_customersupport';
                $job = RemoteJob::orderBy('publication_date','DESC')->where('category_name','LIKE','%'.'Customer'.'%')->orWhere('category_name','LIKE','%'.'Customer Support'.'%')->get();
            }elseif($subs->category == 'HR & Recruitment'){
                $category = 'HR & Recruitment';
                $source = 'email_newsletter_hr&recruitment';
                $job = RemoteJob::orderBy('publication_date','DESC')->where('category_name','LIKE','%'.'Human Resource'.'%')->orWhere('category_name','LIKE','%'.'Recruitment'.'%')->orWhere('category_name','LIKE','%'.'HR & Recruitment'.'%')->get();
            }elseif($subs->category == 'Finance & Legal'){
                $category = 'Finance & Legal';
                $source = 'email_newsletter_finance&legal';
                $job = RemoteJob::orderBy('publication_date','DESC')->where('category_name','LIKE','%'.'Finance'.'%')->orWhere('category_name','LIKE','%'.'Legal'.'%')->orWhere('category_name','LIKE','%'.'Finance & Legal'.'%')->get();
            }elseif($subs->category == 'Product'){
                $category = 'Product';
                $source = 'email_newsletter_product';
                $job = RemoteJob::orderBy('publication_date','DESC')->where('category_name','LIKE','%'.'Product'.'%')->get();
            }elseif($subs->category == 'Business & Management'){
                $category = 'Business & Management';
                $source = 'email_newsletter_business&management';
                $job = RemoteJob::orderBy('publication_date','DESC')->where('category_name','LIKE','%'.'Management'.'%')->orWhere('category_name','LIKE','%'.'Business'.'%')->orWhere('category_name','LIKE','%'.'Business & Management'.'%')->get();
            }elseif($subs->category == 'System Administrator'){
                $category = 'System Administrator';
                $source = 'email_newsletter_systemadministrator';
                $job = RemoteJob::orderBy('publication_date','DESC')->where('category_name','LIKE','%'.'Administration'.'%')->orWhere('category_name','LIKE','%'.'Admin'.'%')->orWhere('category_name','LIKE','%'.'System Administrator'.'%')->get();
            }elseif($subs->category == 'All'){
                $category = 'All';
                $source = 'email_newsletter_all';
                $job = RemoteJob::orderBy('publication_date','DESC')->where('category_name','LIKE','%'.'All'.'%')->get();
                $prog = RemoteJob::orderBy('publication_date','DESC')->where('category_name','LIKE','%'.'Development'.'%')->orWhere('category_name','LIKE','%'.'Software Development'.'%')->orWhere('category_name','LIKE','%'.'Programming'.'%')->first();
                $design = RemoteJob::orderBy('publication_date','DESC')->where('category_name','LIKE','%'.'Design'.'%')->first();
                $cw = RemoteJob::orderBy('publication_date','DESC')->where('category_name','LIKE','%'.'Writing'.'%')->orWhere('category_name','LIKE','%'.'Content Writing'.'%')->first();
                $sales = RemoteJob::orderBy('publication_date','DESC')->where('category_name','LIKE','%'.'Sales'.'%')->orWhere('category_name','LIKE','%'.'Marketing'.'%')->orWhere('category_name','LIKE','%'.'Sales & Marketing'.'%')->first();
                $customer = RemoteJob::orderBy('publication_date','DESC')->where('category_name','LIKE','%'.'Customer'.'%')->orWhere('category_name','LIKE','%'.'Customer Support'.'%')->first();
                $hr = RemoteJob::orderBy('publication_date','DESC')->where('category_name','LIKE','%'.'Human Resource'.'%')->orWhere('category_name','LIKE','%'.'Recruitment'.'%')->orWhere('category_name','LIKE','%'.'HR & Recruitment'.'%')->first();
                $finance = RemoteJob::orderBy('publication_date','DESC')->where('category_name','LIKE','%'.'Finance'.'%')->orWhere('category_name','LIKE','%'.'Legal'.'%')->orWhere('category_name','LIKE','%'.'Finance & Legal'.'%')->first();
                $product = RemoteJob::orderBy('publication_date','DESC')->where('category_name','LIKE','%'.'Product'.'%')->first();
                $business = RemoteJob::orderBy('publication_date','DESC')->where('category_name','LIKE','%'.'Management'.'%')->orWhere('category_name','LIKE','%'.'Business'.'%')->orWhere('category_name','LIKE','%'.'Business & Management'.'%')->first();
                $admin = RemoteJob::orderBy('publication_date','DESC')->where('category_name','LIKE','%'.'Administration'.'%')->orWhere('category_name','LIKE','%'.'Admin'.'%')->orWhere('category_name','LIKE','%'.'System Administrator'.'%')->first();
            }
            $mail = $subs->email;
            $token = $subs->token;
            $surl=route("remote.subscribe.unsubscribe",['mail'=>$mail,'token'=>$token]);
            if($subs->category =='All'){
                $data = array("category"=>$category,"source"=>$source,"prog"=>$prog,"design"=>$design,"cw"=>$cw,"sales"=>$sales,"customer"=>$customer,"hr"=>$hr,"finance"=>$finance,"product"=>$product,"business"=>$business,"admin"=>$admin,"surl"=>$surl);
                Mail::send('jobalert.content', $data, function($message) use ($mail)
                {
                $message->to($mail)
                ->subject("🚀 Remote Jobs Weekly Update");
                });
            } else {
            $data = array("category"=>$category,"source"=>$source,"job"=>$job,"surl"=>$surl);
            Mail::send('jobalert.admin', $data, function($message) use ($mail)
            {
            $message->to($mail)
            ->subject("🚀 Remote Jobs Weekly Update");
            });
        }
        }
        return redirect()->route('admin.subscribers.index')->with('success', 'Email Send successfully.');
    }

    public function plan()
    {
        $plan = Plan::paginate(10);
        return view('admin.remotejob.plan', compact('plan'))->with('i', (request()->input('page', 1) - 1) * 10);
    }

    public function plan_create($pid='')
    {
        $planitem['id'] = '';
        $planitem['name'] = '';
        $planitem['price'] = '';

        if($pid!=''){
            $plan = Plan::findOrFail($pid);
            $planitem['id'] = $plan->id;
            $planitem['name'] = $plan->name;
            $planitem['price'] =$plan->price;
        }
        return view('admin.remotejob.plan_create', compact('planitem'));
    }

    public function plan_store(Request $prequest)
    {
        $prequest->validate([
        'name' => 'required',
        'price' => 'required',
        ]);

        if($prequest->input('id')==''){
            $plan = new Plan;
        }
        else{
            $plan= Plan::findOrFail($prequest->input('id'));
           
        }

        $plan->name =$prequest->input('name');
        $plan->price = $prequest->input('price');
        $plan->save();
        return redirect()->route('admin.plan.index')
            ->with('success', 'Plan updated successfully.');
    }

    public function plan_delete($sid)
    {
        $subscribers = Plan::findOrFail($sid);
        $subscribers->delete();
        return redirect()->route('admin.plan.index')
            ->with('success', 'Plan Deleted successfully.'); 
    }
    
    public function paid_job()
    {
        $rem = RemoteJob::orderBy('publication_date', 'DESC')->where('paid', '=', 1)->paginate(50);
        return view('admin.remotejob.paid_job', compact('rem'))->with('i', (request()->input('page', 1) - 1) * 50);
    }

    public function paid_company()
    {
        $rem = RemoteJob::orderBy('publication_date', 'DESC')->where('paid', '=', 1)->paginate(50);
        return view('admin.remotejob.paid_company', compact('rem'))->with('i', (request()->input('page', 1) - 1) * 50);
    }

    public function email_contact()
    {
        $mail = EmailSubmit::paginate(50);
        return view('email_contact.index', compact('mail'))->with('i', (request()->input('page', 1) - 1) * 50);
    }

    public function delete_econtact(Request $request, $id)
      {
        $contact = EmailSubmit::findOrFail($id);
        $contact->delete();
        return redirect('cr-pipeline')->with('success', 'Email Contact Updated successfully.');
      }

    public function profile(Request $request)
    {
        $q =  $request->get('search');
        if($q!=''){
            $user = Profile::orderBy('created_at', 'DESC')->where('full_name','LIKE','%'.$q.'%')->paginate(100);
        } else {
            $user = Profile::orderBy('created_at', 'DESC')->paginate(100);
        }
        foreach($user as $users)
        {
        	$us = User::where('_id', '=', $users->user_id)->first();
        	$users->us = $us;
        }
        return view('admin.users.index', compact('user'))->with('i', (request()->input('page', 1) - 1) * 100);
    }

    public function profile_details($id)
    {
        $user = Profile::findOrFail($id);
        $cv = Profile_file::where('profile_id', '=', $user->id)->first();
        return view('admin.users.show', compact('user','cv'));
    }


}
