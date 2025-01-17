<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model('model_task');
		$this->load->model('model_news');
		$this->load->model('model_student');
		$this->load->model('model_teacher');
		$this->load->library('session');

		if (!isset($_SESSION['username'])) redirect('auth/login');

		$this->sync_notif();
	}

	public function index()
	{
		$username 				= $this->session->userdata('username');
		$user 					= $this->db->where('username', $username)->get('users')->row_array();

		$kelas = $this->db->get('kelas')->result_array();
		foreach ($kelas as $key => $value) {
			$rowStudent = $this->db->where('class_id', $value['class_id'])->get('student')->num_rows();
			$kelas[$key]['value'] = ($rowStudent) ? $rowStudent : 0;
		}

		$data['student_class'] 	= $kelas;
		$data['tasks'] 			= $this->model_task->get_tasks($username);
		$data['news']			= $this->model_news->get_news();

		// =========================== CREATE TEACHER STATUS ===========================
		$teacher_status = [];

		$status = $this->model_teacher->get_teacher_status(1);
		$count = ($status) ? $status['count'] : 0;

		$teacher_status[0]['country'] = 'aktif ('.$count.')';
		$teacher_status[0]['sales'] = $count;

		$status = $this->model_teacher->get_teacher_status(0);
		$count = ($status) ? $status['count'] : 0;

		$teacher_status[1]['country'] = 'tidak aktif ('.$count.')';
		$teacher_status[1]['sales'] = $count;

		$data['teacher_status'] = $teacher_status;

		// =============================================================================
		// =================== CREATE COUNT LOGIN STUDENT & TEACHER ====================
		// var data = [{
		// 	category: "Jan",
		// 	categoryLabel: "Jan",
		// 	teacher: 100,
		// 	student: 75
		// }, {
		// 	category: "Feb",
		// 	categoryLabel: "Feb",
		// 	teacher: 80,
		// 	student: 50
		// }, {
		// 	category: "Mar",
		// 	categoryLabel: "Mar",
		// 	teacher: 65,
		// 	student: 40
		// }, {
		// 	category: "Apr",
		// 	categoryLabel: "Apr",
		// 	teacher: 50,
		// 	student: 95
		// }];
		// $countLogin = [];
		// $bulan = ['jan', 'feb', 'mar', 'apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec'];
		// foreach ($bulan as $key => $value) {
		// 	$countLogin[$key]['category'] = $value; 
		// 	$countLogin[$key]['categoryLabel'] = $value;
		// 	$teacher_login = $this->model_teacher->get_teacher_login_month($key+1);
		// 	var_dump($teacher_login);
		// }die;

		// echo (json_encode($countLogin));die;
		// =============================================================================

		$this->load->view('header');
		$this->load->view('home/index', $data);
		$this->load->view('footer');
	}

	private function cari_task_notif(){
		$session = $this->session->userdata();

		// CARI DATA TUGAS JIKA TIDAK ADA DI TABEL NOTIF MAKA LAKUKAN INSERT
		$tasks = $this->db->where('DATE(available_date) >=', date("Y-m-d", strtotime("-1 months")))->get('task')->result_array();
		
		############################## INI DI GUNAKAN JIKA MENGGUNAKAN CONTENT JSON ##############################
		
		// $notifs = $this->db->where('type', 'TASK')->where('user_id', $session['userid'])->get('notif')->result_array();
		// looping array notif untuk mendapatkan data task_id
		// $taskIds = [];
		// foreach ($notifs as $key => $notif) {
		// 	$content = json_decode($notif['content']);
		// 	if($content){
		// 		$taskIds[] = $content->task_id;
		// 	}
		// }

		// foreach($tasks as $key => $task){
		// 	$search = array_search($task['task_id'], $taskIds);
		// 	if(!is_numeric($search)){
		// 		$data = [
		// 				'type' 		=> 'TASK',
		// 				'title' 	=> $task['note'],
		// 				'seen' 		=> false,
		// 				'user_id' 	=> $session['userid'],
		// 				'created_at' => $task['available_date'],
		// 				'link'		=> 'task/detail/'.$task['task_id'],
		// 				'task_id'	=> $task['task_id']
		// 			];
		// 			$this->db->insert('notif', $data);
		// 	}
		// }
		
		// die;

		
		foreach ($tasks as $task) {
			$notif = $this->db->where('type', 'TASK')->where('task_id', $task['task_id'])->where('user_id', $session['userid'])->get('notif')->row_array();
			if(!$notif){
				$data = [
					'type' 		=> 'TASK',
					'title' 	=> $task['note'],
					'seen' 		=> false,
					'user_id' 	=> $session['userid'],
					'created_at' => $task['available_date'],
					'link'		=> 'task/detail/'.$task['task_id'],
					'task_id'	=> $task['task_id']
				];
				$this->db->insert('notif', $data);
			}
		}
	}

	private function cari_news_notif(){
		$session = $this->session->userdata();

		// CARI DATA NEWS JIKA TIDAK ADA DI TABEL NOTIF MAKA LAKUKAN INSERT
		$beritas = $this->db->where('DATE(tanggal) >=', date("Y-m-d", strtotime("-1 months")))->get('news')->result_array();
		foreach ($beritas as $news) {
			$notif = $this->db->where('type', 'NEWS')->where('news_id', $news['id'])->where('user_id', $session['userid'])->get('notif')->row_array();
			if(!$notif){
				$data = [
					'type' 		=> 'NEWS',
					'title' 	=> $news['judul'],
					'seen' 		=> false,
					'user_id' 	=> $session['userid'],
					'created_at' => $news['tanggal'],
					'link'		=> 'news/detail/'.$news['id'],
					'news_id'	=> $news['id']
				];
				$this->db->insert('notif', $data);
			}
		} 
	}

	private function cari_sesi_notif(){
		$session = $this->session->userdata();

		// CARI DATA SESI JIKA TIDAK ADA DI TABEL NOTIF MAKA LAKUKAN INSERT
		$sesies = $this->db->where('DATE(sesi_date) >=', date("Y-m-d", strtotime("-1 months")))->get('sesi')->result_array();
		foreach ($sesies as $sesi) {
			$notif = $this->db->where('type', 'SESI')->where('sesi_id', $sesi['sesi_id'])->where('user_id', $session['userid'])->get('notif')->row_array();
			if(!$notif){
				$data = [
					'type' 		=> 'SESI',
					'title' 	=> $sesi['sesi_title'],
					'seen' 		=> false,
					'user_id' 	=> $session['userid'],
					'created_at' => $sesi['sesi_date'].' '.$sesi['sesi_jam_start'],
					'link'		=> 'sesi/detail/'.$sesi['sesi_id'],
					'sesi_id'	=> $sesi['sesi_id']
				];
				$this->db->insert('notif', $data);
			}
		} 
	}

	public function sync_notif(){
		$session = $this->session->userdata();

		if($session['user_level'] == 4 || $session['user_level'] == 5){
			$this->cari_task_notif();
			$this->cari_news_notif();
			$this->cari_sesi_notif();
		}

		if($session['user_level'] == 3 || $session['user_level'] == 6){
			$this->cari_news_notif();
		}

		if($session['user_level'] == 3){
			$this->cari_sesi_notif();
		}
	}

	public function notif(){
		$user_id = $this->session->userdata('userid'); 
		$notif = $this->db->where('user_id', $user_id)->where('seen', false)->get('notif')->num_rows();

		$response = [ 'success' => true, 'total' => $notif ];

		header('Content-Type: application/json');
		echo json_encode($response, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);
	}

	public function notif_data(){
		$user_id = $this->session->userdata('userid'); 
		$notif = $this->db->where('user_id', $user_id)->limit('100')->order_by('created_at', 'DESC')->get('notif')->result_array();

		$response = [ 'success' => true, 'data' => $notif ];

		header('Content-Type: application/json');
		echo json_encode($response, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);
	}

	public function notif_update(){
		$get = $this->input->get();
		$notif_id = $get['notif_id'];

		$update = $this->db->where('notif_id', $notif_id)->update('notif', ['seen'=> true]);

		if($update){
			$response = [ 'success' => true, 'data' => 'data berhasil diupdate'];
		}else{
			$response = [ 'success' => false, 'data' => 'data gagal diupdate'];
		}


		header('Content-Type: application/json');
		echo json_encode($response, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);
	}
}
