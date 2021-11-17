<?php

/*
* api of cart, standalone application
* excepted things from the framework:
* (see below)
*/

assert_options(ASSERT_ACTIVE,1);//in case of
assert_options(ASSERT_CALLBACK,'see_assert');
assert_options(ASSERT_BAIL,0);
function see_assert($script,$line,$error){
echo 'error in '.$script.' at line '.$line.': '. $error;}

class cartapi{
static $private=0;
static $a=__CLASS__;
static $cb='mdb';
static $process=[];
static $errors=[];
static $types=['ecommerce_id'=>'int','customer_id'=>'int','item_list'=>'json','product_sku'=>'int','product_name'=>'var','filetype'=>'pdf-psd-ai-','quantity'=>'int','delivery_date'=>'date'];

#api/client

/*
* unuseful here
* it's the client side
* with an example
*/

//http://telex.ovh/api/cartapi/put&ecommerce_id=1&customer_id=1&item_list={"1":{"product_sku":1,"product_name":"prod1","filetype":"pdf","quantity":"101","delivery_date":"2021-11-20"},"2":{"product_sku":2,"product_name":"prod2","filetype":"ai","quantity":"499","delivery_date":"2021-11-19"}}&verbose=1
static function test(){
	$r['ecommerce_id']=1;
	$r['customer_id']=1;
	$rb[1]['product_sku']=1;
	$rb[1]['product_name']='prod1';
	$rb[1]['filetype']='pdf';
	$rb[1]['quantity']='101';
	$rb[1]['delivery_date']='2021-11-20';
	$rb[2]['product_sku']=2;
	$rb[2]['product_name']='prod2';
	$rb[2]['filetype']='ai';
	$rb[2]['quantity']='499';
	$rb[2]['delivery_date']='2021-11-19';
	$r['item_list']=json_encode($rb);
	$r['verbose']=0;
	return $r;}

static function getkey(){return 1234;}

static function apicall($r,$mode){
	$k=self::getkey();
	if(!$mode)$mode='get';//post,put,get,del
	$ubase='';//https://logic.ovh///unused
	$u=$ubase.'api/cartapi/'.$mode.'?'.mkprm($r).'&token='.$k;
	$d=@file_get_contents($u);
	return json_decode($d,true);}

/*
* this section need the frameweork
* it's used to display an interface
* that let test things
*/

#call
static function call($p){//pr($p);
	$act=$p['act']??''; $p1=$p['p1']??''; $ret='';
	if($act=='url'){
		$prm=explode_k($p1,'&','=');//build array from url
		$ret=self::core($prm);}
	return $ret;}

#content
static function menu($p){$p1=$p['p1']??'';
	$ja=self::$cb.'|'.self::$a.',call|';
	$p1=htmlentities(mkprm(self::test())); $j=$ja.'act=url|p1';
	$ret=form::call(['p1'=>['url','inputcall',$p1,$j],['ok','submit',$j,'']]);//build ajax form
	$p1=''; $j=$ja.'act=item|p1';
	//$ret=form::call(['p1'=>['item','inputcall',$p1,$j],['ok','submit',$j,'']]);
	return $ret;}

static function content($p){
	//self::install();
	$p['p1']=$p['p1']??'';
	$bt=self::menu($p);
	return $bt.div('','board',self::$cb);}
	
###############################

/*
* here is the job
* begin to read from the bottom to the top
* api() is called by api.php
* core() if the main activity of this app
* first we verify types of datas
* secondly we verify validity of datas (was not necessary here, i know...)
* global_result() call calculate_price()
*/

#api/server
static private $status=[200=>'ok',201=>'created',204=>'no_content',206=>'partial_content',304=>'not_modified',400=>'bad_request',401=>'Unauthorized',403=>'Forbidden',404=>'Not Found',500=>'Internal Server Error'];

/*
* method render_results
* returns json
*/

static function render_results($r,$n=404){
	$ret['status']=$n;
	$ret['results']=$r;
	$ret['created at']=date('Y-m-d H:i:s');
	header('HTTP/1.1 '.$n.' '.(self::$status[$n]??404));
	header('Content-Type: text/json; charset=UTF-8');
	return json_encode($ret);}

/*
* method verif_types
* do not verify validity, only types
* accepted types are int/var/list given by "-", and time.
* return boolean $res, 1 if true
*/

static function verif_types($k,$v){//type,value
	$expected_type=self::$types[$k]??'';
	$detected_type='';
	if(is_numeric($v) && strpos($v,'.')===false)
		$detected_type='int';//prevent float numbers
	elseif(is_numeric($v))
		$detected_type='float';//unused, will produce an error later
	elseif(strpos($expected_type.'-',$v)!==false)
		$detected_type='pdf-psd-ai-';//it's a few stupid but we just verif if "is_type"
	elseif(DateTime::createFromFormat('Y-m-d',$v)!==false)
		$detected_type='date';//we are not sure if the date is good
	elseif(is_string($v))
		$detected_type='var';//surely more verifs to do here
	$res=$detected_type==$expected_type?1:0;//validation
	if(!$res)self::$errors[]=$k.' at '.$v.' using bad type: '.$detected_type.' instead of '.$expected_type;
	return $res;}

//we check if this commerce exists
static function ecommerce_exists($n){return 1;}
//we check if this customer exists (and if the request is verfied etc...
static function customer_exists($n){return 1;}

/*
* method itemlist_validation
* desesprate way to validate values without using asserts :(
* returns nothing, only fuels the array self::$error[]
*/

//we check if list of items are valid
static function itemlist_validation($sku,$r){
	$er=[];//errors
	foreach($r as $k=>$v){
		switch($k){
			case('filetype'):
				if($v!='pdf' && $v!='psd' && $v!='ai')
					self::$errors[]='bad filetype for item '.$sku;
			break;
			case('delivery_date'):
				if(strlen($v)!=10)
					self::$errors[]='bad format of date for item '.$sku;
				if(strtotime($v)<time())
					self::$errors[]='illogical date for item '.$sku;
			break;
			case('quantity'):
				//assert('$v>0');
				if($v<0)
					self::$errors[]='negative quantity for item '.$sku;
			break;
		}
	}
}

//static $types=['ecommerce_id'=>'int','customer_id'=>'int','item_list'=>'json','product_sku'=>'int','product_name'=>'var','filetype'=>'pdf-psd-ai-','quantity'=>'int','delivery_date'=>'date'];

/*
* method calculate_prise
* contains definitions of remittances and costs (should be in an other place)
* returns array containing: Amount, Vat, Total
*/

static function calculate_price($item){//pr($item);
	$price_of_product=1;//all products costs 1
	$cost_deadline=0;
	$remittance_quantity=0;
	//definitions
	$cost_by_type=['pdf'=>15,'psd'=>35,'ai'=>25];
	$cost_by_quantity=[100=>5,250=>10,500=>15,1000=>20];//remittance by quantity
	$cost_by_deadline=[0=>30,86400=>20,172800=>10,259200=>0];//nb days in seconds
	$deadline=strtotime($item['delivery_date'])-time();//time left at this moment
	//calculation
	$amount=$price_of_product*$item['quantity'];
	$cost_filetype=$cost_by_type[$item['filetype']];
	foreach($cost_by_quantity as $k=>$v)if($item['quantity']>$k)$remittance_quantity=$v;
	foreach($cost_by_deadline as $k=>$v)if($deadline>$k)$cost_deadline=$v;
	//add all remittances/costs
	$vat=$cost_filetype+$cost_deadline-$remittance_quantity;
	//apply remittances/costs
	$total=$amount+($amount*($vat/100));
	$ret=['amount'=>$amount,'vat'=>$vat,'total'=>$total];
	//we keep that 
	self::$process[$item['product_sku']]=['product'=>$item['product_name'],'net_price'=>$amount,'cost_filetype'=>$cost_filetype,'remittance_quantity'=>$remittance_quantity,'cost_deadline'=>$cost_deadline]+$ret;
	return $ret;}

/*
* method global_result()
* do the calculation for each item of the list
* returns array
*/

static function global_result($items){
	$additions=[];
	foreach($items as $k=>$item)
		$additions[]=self::calculate_price($item);
	$price=array_sum(array_column($additions,'amount'));
	$vat=array_sum(array_column($additions,'vat'))/count($additions);//average
	$total=array_sum(array_column($additions,'total'));
	$ret=['Net price'=>round($price,2),'Vat'=>round($vat,2),'Total quote'=>round($total,2)];
	self::$process['global_result']=$ret;//keep that
	return $ret;}

/*
* method core()
* the script of all the activities
* 1. check types
* 2. chek validity
* 3. calculate results
*/

static function core($p){
	//we receive our parameters
	[$ecommerce_id,$customer_id,$item_list,$action]=vals($p,['ecommerce_id','customer_id','item_list','action']);
	
	//check json
	$items=json_decode($item_list,true); //pr($items);
	if(!$items)
		return self::render_results(['bad request'=>json_er()],400);
	else self::$process['datas']=$items;
	
	//we evaluate the types of our 3 variables
	$ecommerce_id_ok=self::verif_types('ecommerce_id',$ecommerce_id);
	if(!$ecommerce_id_ok)self::$errors[]='ecommerce_id using bad type';
	$customer_id_ok=self::verif_types('customer_id',$customer_id);
	if(!$customer_id_ok)self::$errors[]='customer_id using bad type';
	
	//evaluate types of the elements of the items
	$rb=[];
	foreach($items as $k=>$item)
		foreach($item as $kb=>$vb)
			$rb[$k][$kb]=self::verif_types($kb,$vb); //pr($rb);
	//validation means n points for n entries
	$item_validation=[];
	foreach($rb as $k=>$v)
		$item_validation[$k]=count($v)==array_sum($v);//verify sum of validations for each item
	$ok=count($item_validation)==array_sum($item_validation);//all is okay
	
	//return errors
	if(!$ok)
		return self::render_results(self::$errors,400);
	else self::$process[]='no types error';
	
	//we kindly evaluate the validity of our 3 variables
	$ecom_ok=self::ecommerce_exists($ecommerce_id);//unused here
	$cust_ok=self::customer_exists($customer_id);//unused here
	//... verify is others values are reasonables... (todo)
	//I just really need to verif dates and filetype
	foreach($items as $k=>$item)
		self::itemlist_validation($k,$item);
	//return errors
	if(self::$errors)//before last action, self::$errors was empty
		return self::render_results(self::$errors,400);
	else self::$process[]='datas are valid';
	
	//ok, and now the real job...
	$res=self::global_result($items);
	if(get('verbose'))$res['verbose']=self::$process; //pr(self::$process);
	
	return self::render_results($res,200);//output result
}

/*
* mehod api()
* here begins the activity
* just recuperate the gets
*/

static function api($p){
	//pr($p);//framework usually accept pseudo-json urls
	//pr($_GET);//we obtain here : [app] => cartapi, [p] => put, [ecommerce_id] => 1, [item_list] => {"1":...
	
	//ok, go : we build the array
	$params['action']=get('p');//post,put,get,del
	$params['ecommerce_id']=get('ecommerce_id');
	$params['customer_id']=get('customer_id');
	$params['item_list']=get('item_list');
	return self::core($params);}

}

//things of framework (don't be afraid)
//function get($d){if(isset($_GET[$d]))return urldecode($_GET[$d]);}
//function mkprm($p){foreach($p as $k=>$v)$rt[]=$k.'='.$v; if($rt)return implode('&',$rt);}
//function val($r,$d,$b=''){if(!isset($r[$d]))return $b; return $r[$d]=='memtmp'?memtmp($d):$r[$d];}
//function vals($p,$r,$o=''){foreach($r as $k=>$v)$rt[]=val($p,$v,$o); return $rt;}
//function pr($r,$o=''){$ret='<pre>'.print_r($r,true).'</pre>'; if($o)return $ret; else echo $ret;}
/*
function json_er(){
switch(json_last_error()){
case JSON_ERROR_NONE:$ret='no error';break;
case JSON_ERROR_DEPTH:$ret='maximum depth reached';break;
case JSON_ERROR_STATE_MISMATCH:$ret='bad modes (underflow)';break;
case JSON_ERROR_CTRL_CHAR:$ret='error during character check';break;
case JSON_ERROR_SYNTAX:$ret='syntax error; malformed Json';break;
case JSON_ERROR_UTF8:$ret='malformed UTF-8 characters';break;
default:$ret='unknown error';break;}
return $ret;}*/

?>