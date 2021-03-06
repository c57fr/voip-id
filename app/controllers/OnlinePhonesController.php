<?php

class OnlinePhonesController extends \BaseController {

	/**
	 * Instantiate a new OnlinePhonesController instance.
	 */
	public function __construct() {

		$this->beforeFilter('auth');

	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function getIndex()
	{
		$status = Auth::user()->status;
		if($status == 2){
			$online_phone = OnlinePhone::all();
		}elseif($status == 3){
			$domain = Domain::whereUserId(Auth::user()->id)->get(array('sip_server'));
			$sip_server = array();
			foreach ($domain as $row) {
				$sip_server[] = $row['sip_server'];
			}
			if($sip_server){
				$online_phone = OnlinePhone::whereIn('sip_server', $sip_server)->get();
			} else $online_phone = [];
		}else{
			$sip_server = Domain::find(Cookie::get('domain_hash'))->sip_server;
			$online_phone = OnlinePhone::whereSipServer($sip_server)->get();
		}

		return View::make('online_phone.index')->with('online_phones', $online_phone);
	}
	
	private function _haveOnlinePhone($sip_server){
		$results = DB::select('select sip_server from domains where deleted_at IS NULL and sip_server = ?', array($sip_server));
		if($results){
				return TRUE;
			}else return FALSE;
		
	}


}
