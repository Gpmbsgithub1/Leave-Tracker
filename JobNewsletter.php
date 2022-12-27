<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\RemoteJob;
use App\Subscribe;
use Illuminate\Support\Facades\Mail;

class JobNewsletter extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mail:newsletter';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Weekly Job Alert Newsletter emails';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // $sub = Subscribe::where('verify', '=', '1435564346edsgdfsgdgdfgdfg')->orWhere('verify', '=', '1rtretretrtr')->get();
        // foreach($sub as $k => $subs){
        //     if($subs->category == 'Programming'){
        //         $category = 'Programming';
        //         $source = 'email_newsletter_programming';
        //         $job = RemoteJob::orderBy('publication_date','DESC')->where('category_name','LIKE','%'.'Development'.'%')->orWhere('category_name','LIKE','%'.'Software Development'.'%')->orWhere('category_name','LIKE','%'.'Programming'.'%')->get();
        //     }elseif($subs->category == 'Design'){
        //         $category = 'Design';
        //         $source = 'email_newsletter_design';
        //         $job = RemoteJob::orderBy('publication_date','DESC')->where('category_name','LIKE','%'.'Design'.'%')->get();
        //     }elseif($subs->category == 'Content Writing'){
        //         $category = 'Content Writing';
        //         $source = 'email_newsletter_contentwriting';
        //         $job = RemoteJob::orderBy('publication_date','DESC')->where('category_name','LIKE','%'.'Writing'.'%')->orWhere('category_name','LIKE','%'.'Content Writing'.'%')->get();
        //     }elseif($subs->category == 'Sales & Marketing'){
        //         $category = 'Sales & Marketing';
        //         $source = 'email_newsletter_sales&marketing';
        //         $job = RemoteJob::orderBy('publication_date','DESC')->where('category_name','LIKE','%'.'Sales'.'%')->orWhere('category_name','LIKE','%'.'Marketing'.'%')->orWhere('category_name','LIKE','%'.'Sales & Marketing'.'%')->get();
        //     }elseif($subs->category == 'Customer Support'){
        //         $category = 'Customer Support';
        //         $source = 'email_newsletter_customersupport';
        //         $job = RemoteJob::orderBy('publication_date','DESC')->where('category_name','LIKE','%'.'Customer'.'%')->orWhere('category_name','LIKE','%'.'Customer Support'.'%')->get();
        //     }elseif($subs->category == 'HR & Recruitment'){
        //         $category = 'HR & Recruitment';
        //         $source = 'email_newsletter_hr&recruitment';
        //         $job = RemoteJob::orderBy('publication_date','DESC')->where('category_name','LIKE','%'.'Human Resource'.'%')->orWhere('category_name','LIKE','%'.'Recruitment'.'%')->orWhere('category_name','LIKE','%'.'HR & Recruitment'.'%')->get();
        //     }elseif($subs->category == 'Finance & Legal'){
        //         $category = 'Finance & Legal';
        //         $source = 'email_newsletter_finance&legal';
        //         $job = RemoteJob::orderBy('publication_date','DESC')->where('category_name','LIKE','%'.'Finance'.'%')->orWhere('category_name','LIKE','%'.'Legal'.'%')->orWhere('category_name','LIKE','%'.'Finance & Legal'.'%')->get();
        //     }elseif($subs->category == 'Product'){
        //         $category = 'Product';
        //         $source = 'email_newsletter_product';
        //         $job = RemoteJob::orderBy('publication_date','DESC')->where('category_name','LIKE','%'.'Product'.'%')->get();
        //     }elseif($subs->category == 'Business & Management'){
        //         $category = 'Business & Management';
        //         $source = 'email_newsletter_business&management';
        //         $job = RemoteJob::orderBy('publication_date','DESC')->where('category_name','LIKE','%'.'Management'.'%')->orWhere('category_name','LIKE','%'.'Business'.'%')->orWhere('category_name','LIKE','%'.'Business & Management'.'%')->get();
        //     }elseif($subs->category == 'System Administrator'){
        //         $category = 'System Administrator';
        //         $source = 'email_newsletter_systemadministrator';
        //         $job = RemoteJob::orderBy('publication_date','DESC')->where('category_name','LIKE','%'.'Administration'.'%')->orWhere('category_name','LIKE','%'.'Admin'.'%')->orWhere('category_name','LIKE','%'.'System Administrator'.'%')->get();
        //     }elseif($subs->category == 'All'){
        //         $category = 'All';
        //         $source = 'email_newsletter_all';
        //         $job = RemoteJob::orderBy('publication_date','DESC')->where('category_name','LIKE','%'.'All'.'%')->get();
        //         $prog = RemoteJob::orderBy('publication_date','DESC')->where('category_name','LIKE','%'.'Development'.'%')->orWhere('category_name','LIKE','%'.'Software Development'.'%')->orWhere('category_name','LIKE','%'.'Programming'.'%')->first();
        //         $design = RemoteJob::orderBy('publication_date','DESC')->where('category_name','LIKE','%'.'Design'.'%')->first();
        //         $cw = RemoteJob::orderBy('publication_date','DESC')->where('category_name','LIKE','%'.'Writing'.'%')->orWhere('category_name','LIKE','%'.'Content Writing'.'%')->first();
        //         $sales = RemoteJob::orderBy('publication_date','DESC')->where('category_name','LIKE','%'.'Sales'.'%')->orWhere('category_name','LIKE','%'.'Marketing'.'%')->orWhere('category_name','LIKE','%'.'Sales & Marketing'.'%')->first();
        //         $customer = RemoteJob::orderBy('publication_date','DESC')->where('category_name','LIKE','%'.'Customer'.'%')->orWhere('category_name','LIKE','%'.'Customer Support'.'%')->first();
        //         $hr = RemoteJob::orderBy('publication_date','DESC')->where('category_name','LIKE','%'.'Human Resource'.'%')->orWhere('category_name','LIKE','%'.'Recruitment'.'%')->orWhere('category_name','LIKE','%'.'HR & Recruitment'.'%')->first();
        //         $finance = RemoteJob::orderBy('publication_date','DESC')->where('category_name','LIKE','%'.'Finance'.'%')->orWhere('category_name','LIKE','%'.'Legal'.'%')->orWhere('category_name','LIKE','%'.'Finance & Legal'.'%')->first();
        //         $product = RemoteJob::orderBy('publication_date','DESC')->where('category_name','LIKE','%'.'Product'.'%')->first();
        //         $business = RemoteJob::orderBy('publication_date','DESC')->where('category_name','LIKE','%'.'Management'.'%')->orWhere('category_name','LIKE','%'.'Business'.'%')->orWhere('category_name','LIKE','%'.'Business & Management'.'%')->first();
        //         $admin = RemoteJob::orderBy('publication_date','DESC')->where('category_name','LIKE','%'.'Administration'.'%')->orWhere('category_name','LIKE','%'.'Admin'.'%')->orWhere('category_name','LIKE','%'.'System Administrator'.'%')->first();
        //     }
        //     $mail = $subs->email;
        //     $token = $subs->token;
        //     $surl=route("remote.subscribe.unsubscribe",['mail'=>$mail,'token'=>$token]);
        //     if($subs->category =='All'){
        //         $data = array("category"=>$category,"source"=>$source,"prog"=>$prog,"design"=>$design,"cw"=>$cw,"sales"=>$sales,"customer"=>$customer,"hr"=>$hr,"finance"=>$finance,"product"=>$product,"business"=>$business,"admin"=>$admin,"surl"=>$surl);
        //         Mail::send('jobalert.content', $data, function($message) use ($mail)
        //         {
        //         $message->to($mail)
        //         ->subject("🚀 Remote Jobs Weekly Update");
        //         });
        //     } else {
        //     $data = array("category"=>$category,"source"=>$source,"job"=>$job,"surl"=>$surl);
        //     Mail::send('jobalert.admin', $data, function($message) use ($mail)
        //     {
        //     $message->to($mail)
        //     ->subject("🚀 Remote Jobs Weekly Update");
        //     });
        // }
        // } 
    }
}
